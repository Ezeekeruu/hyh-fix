# AGENTS.md — HYH Fix (IT12)

Laravel 12 + PHP ^8.2 + Vite 7 + Tailwind v4. No CI, no `opencode.json`, no shared Blade layout.

## Setup / run

- `composer setup` on fresh clone; `composer dev` for serve + queue:listen + pail + vite (kill-others). MySQL `hyh_fix` must exist first.
- Local `.env` is MySQL (`127.0.0.1:3306`, `hyh_fix`, root / empty) but `.env.example` is sqlite. Tests ignore this: `phpunit.xml` forces sqlite `:memory:`.
- Seed: `php artisan migrate:fresh --seed` (`DatabaseSeeder` → `SampleDataSeeder`, full demo data; accounts/creds in its docblock). Bare `migrate:fresh` wipes to empty.

## Verify

- `composer test` (`config:clear` + `php artisan test`). Single: `php artisan test --filter=Name` or `php artisan test tests/Feature/FooTest.php`.
- `vendor/bin/pint --test` to check, `vendor/bin/pint` to fix. No `pint.json` — Laravel defaults.

## Gotchas — read before editing

- Views are standalone pages, no `layouts/` or `components/`; sidebar HTML is duplicated per page. Copy an existing page for new ones. Styling is `public/css/*.css` via `asset()`, not `@vite` (only `welcome.blade.php` uses Vite) — edit the matching css file.
- No `auth` middleware; every sidebar links `url('/logout')` with no backing route. `SaleController`/`RepairTicketController` use `Auth::id() ?? User::first()?->id ?? 6` fallback. `resources/views/login/` is empty — no login UI.
- `GET /transaction-history` is defined twice; the later `SaleController@transactionHistory` wins. Ignore the closure.
- Dead actions: `ProductController@show` → `products.show`, `SaleController@index` → `sales.index`, `SaleController@edit` → `sales.edit`, `RepairTicketController@show/edit` → `repair_tickets.show/edit`, `UserController@show` → `users.show` — none of these Blade files exist (real dirs: `inventory/`, `transactions/`, `repair/`, `users/`, `pos/`, `dashboard/`). There is also no `sales.index` route name, so `SaleController@update/@destroy` redirects throw.
- `sales.status` enum is `completed,voided`, but `SaleController@update` validates `completed,cancelled,refunded` and restores stock only on cancelled/refunded — mismatched, will 422 / never restore. Seeder's `voided` is correct.
- `users.role` enum is `admin,staff`, `status` is `active,disabled`. Forms accept display roles (`manager`→`admin`, `secretary`/`sales-clerk`/`technician`→`staff`); `UserController@update` also accepts `inactive` status, which the DB rejects. `User $fillable` lacks `role`/`status` — keep the existing direct-assignment pattern, not `User::create()`.
- Use `App\Models\Sale` (`sales` table); `app/Models/Sales.php` is deleted — don't reintroduce it.
- Uploads go to the `public` disk (`products/`, `repair-photos/`); `public/storage` is absent until `php artisan storage:link`, so `getImageUrlAttribute()` 404s without it. `.env*`, `public/build|hot|storage`, `vendor/`, `node_modules/` are gitignored — don't edit or commit.
