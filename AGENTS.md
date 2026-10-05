# AGENTS.md — HYH Fix (IT12)

Laravel 12 + PHP ^8.2 + Vite 7 + Tailwind v4. No CI, no `opencode.json`, no shared Blade layout.

## Setup / run

- `composer setup` on fresh clone; `composer dev` for serve + queue:listen + pail + vite (kill-others). MySQL `hyh_fix` must exist first.
- Local `.env` is MySQL (`127.0.0.1:3306`, `hyh_fix`, root / empty) but `.env.example` is sqlite. Tests ignore this: `phpunit.xml` forces sqlite `:memory:`.
- Seed: `php artisan migrate:fresh --seed` (`DatabaseSeeder` → `SampleDataSeeder`, full demo data; accounts/creds in its docblock). Bare `migrate:fresh` wipes to empty.

## Deploy (Render free + Supabase)

- `Dockerfile` (php:8.2-apache, pdo_pgsql) + `render.yaml` blueprint + `.dockerignore` are the deploy contract. Push to `origin/main`, then New → Blueprint in Render; fill `APP_KEY` + `DB_PASSWORD` secrets when prompted.
- Boot CMD migrates + caches config/routes/views, so deploys are self-applying. `QUEUE_CONNECTION=sync` on Render (no worker on free tier; the app dispatches no jobs anyway).
- Uploads (`products/`, `repair-photos/`) live on Render's ephemeral disk — they vanish on restart/redeploy. Acceptable for now; move to R2/S3 before real use.

## Verify

- `composer test` (`config:clear` + `php artisan test`). Single: `php artisan test --filter=Name` or `php artisan test tests/Feature/FooTest.php`.
- `vendor/bin/pint --test` to check, `vendor/bin/pint` to fix. No `pint.json` — Laravel defaults.

## Auth / roles — session auth is enforced, don't bypass it

- `GET /login` + `POST /login` (`AuthController::homeFor()` decides the landing page), `POST /logout` (named `logout`). `/` redirects guests to login and signed-in users to their home. No public registration — admins create accounts via User Management.
- Admins land on `/dashboard`, staff on `/staff/dashboard` (`DashboardController@index` / `staffDashboard` redirect cross-role visits). They are separate views; staff nav hides Reports + User Management entirely via `@if(auth()->...->isAdmin())`.
- `role` middleware alias (`EnsureRole`, registered in `bootstrap/app.php`): admin-only group covers inventory writes, master-data modules (`/categories*`, `/suppliers*`, `/service-types*`), `/reports*`, `/user-management*`. Staff hitting them gets 403. POS, repairs, transaction history, inventory list stay `auth`-only (both roles).
- Topbar user menu is a `profile-dropdown` block (name + role + arrow, single Logout POST form) styled by shared `public/css/topbar-user.css`. No sidebar logout remains; never reintroduce `url('/logout')` GET links.
- Disabled accounts (`users.status = 'disabled'`) are rejected at login with an error, even with a correct password.
- `SaleController`/`RepairTicketController` still use `Auth::id() ?? User::first()?->id ?? 6` fallback when recording actor ids. The login view lives at `login/index.blade.php`.
- Master data lives in its own modules: `CategoryController`, `SupplierController`, `ServiceTypeController` with views under `resources/views/{categories,suppliers,service-types}/` reusing `inventory.css` + `master-data.css`. No quick-add forms on Add Product anymore. `suppliers.contact_info/location` are NOT NULL in schema — controllers coalesce to `''`.
- Every list page injects the same inline shell script before `</body>`: hamburger `.menu-btn` flips one `collapsed` boolean that sets inline `display:none` on `.sidebar` and `margin-left:0` on `.main-content` together (inline, so the two can never disagree), then dispatches `window.resize` so Chart.js re-fits (without it the canvas keeps its expanded width and the layout looks stuck + scrolls); and `input[name="search"]` auto-submits its form after 450ms (POS `pos-filter-form` excluded — it filters client-side). Replicate both when adding pages.
- Layout shrink rules live in `topbar-user.css`: `.main-content { min-width: 0 }` (flex item must shrink) plus `min-width: 0` on grid/card children. Never add fixed `min-width` to filter bars (dashboard's old 400px search box caused page-level horizontal scroll).
- Dashboard queries must stay sqlite-compatible for tests: device label uses `||` on sqlite / `CONCAT()` on MySQL. Dashboard total-transactions card counts today only; stat cards are `stat-card stat-link` anchors with prefiltered hrefs.

## Gotchas — read before editing

- Views are standalone pages, no `layouts/` or `components/`; sidebar + topbar HTML is duplicated per page. Copy an existing page for new ones and replicate the `@if(isAdmin())` nav gating + `profile-dropdown` block. Sidebar nav is grouped with `.nav-group-label` headers: MAIN (Dashboard, POS), MANAGEMENT (repairs, transactions, inventory), ADMIN (reports, master data, users — inside the admin `@if`, header included). Styling is `public/css/*.css` via `asset()`, not `@vite` (only `welcome.blade.php` uses Vite) — edit the matching css file; `topbar-user.css` is the one shared exception.
- Detail/edit views live next to their list views: `repair/show-ticket` + `repair/edit-ticket`, `inventory/show`. `categories/suppliers/service-types` are full modules (index/add/edit each). Unrouted leftovers redirect instead of 500ing: `SaleController@index` → transaction history, `SaleController@edit` → receipt, `UserController@show` → users list. `DeviceController` was unrouted dead code and is deleted — don't reintroduce it.
- `sales.status` enum is `completed,voided`: `SaleController@update` validates/restores stock on `voided` and redirects to transaction history. Seeder's `voided` is correct. There is still no void/refund button in the UI — `update`/`destroy` have no routes.
- `users.role` enum is `admin,staff`, `status` is `active,disabled`. Forms accept display roles (`manager`→`admin`, `secretary`/`sales-clerk`/`technician`→`staff`); update validation accepts only `active,disabled`. `User $fillable` lacks `role`/`status` — keep the existing direct-assignment pattern, not `User::create()`.
- Optional repair-ticket fields that are NOT NULL in schema are coalesced in `RepairTicketController@store`: `address` → `''`, `serial_or_imei` → `''` (both 500'd on empty submit before). Same pattern as suppliers. `problem_description`/`quotation_price`/`final_price` already default safely.
- Use `App\Models\Sale` (`sales` table); `app/Models/Sales.php` is deleted — don't reintroduce it.
- Uploads go to the `public` disk (`products/`, `repair-photos/`); `public/storage` is absent until `php artisan storage:link`, so `getImageUrlAttribute()` 404s without it. `.env*`, `public/build|hot|storage`, `vendor/`, `node_modules/` are gitignored — don't edit or commit.
