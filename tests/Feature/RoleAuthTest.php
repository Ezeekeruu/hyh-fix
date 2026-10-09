<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\RepairTicket;
use App\Models\Sale;
use App\Models\ServiceType;
use App\Models\Supplier;
use App\Models\User;
use Database\Seeders\SampleDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAuthTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role, string $status = 'active'): User
    {
        $user = User::factory()->create();
        $user->role = $role;
        $user->status = $status;
        $user->save();

        return $user;
    }

    public function test_login_screen_renders_without_signup(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
        $response->assertSee('email', false);
        $response->assertSee('password', false);
        $response->assertSee('Log In', false);
        $response->assertDontSee('Register', false);
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/staff/dashboard')->assertRedirect('/login');
        $this->get('/pos')->assertRedirect('/login');
        $this->get('/inventory')->assertRedirect('/login');
        $this->get('/transaction-history')->assertRedirect('/login');
        $this->get('/reports')->assertRedirect('/login');
        $this->get('/user-management')->assertRedirect('/login');
    }

    public function test_admin_login_redirects_to_admin_dashboard(): void
    {
        $admin = $this->makeUser('admin');

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($admin);
    }

    public function test_staff_login_redirects_to_staff_dashboard(): void
    {
        $staff = $this->makeUser('staff');

        $response = $this->post('/login', [
            'email' => $staff->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/staff/dashboard');
        $this->assertAuthenticatedAs($staff);
    }

    public function test_disabled_accounts_cannot_log_in(): void
    {
        $user = $this->makeUser('staff', 'disabled');

        $response = $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_each_role_lands_on_its_own_dashboard(): void
    {
        $admin = $this->makeUser('admin');
        $staff = $this->makeUser('staff');

        $this->actingAs($admin)->get('/staff/dashboard')->assertRedirect('/dashboard');
        $this->actingAs($staff)->get('/dashboard')->assertRedirect('/staff/dashboard');
        $this->actingAs($staff)->get('/staff/dashboard')->assertOk();
    }

    public function test_staff_cannot_access_admin_sections(): void
    {
        $staff = $this->makeUser('staff');

        $this->actingAs($staff)->get('/reports')->assertForbidden();
        $this->actingAs($staff)->get('/user-management')->assertForbidden();
        $this->actingAs($staff)->get('/inventory/add')->assertForbidden();
        $this->actingAs($staff)->post('/inventory', [])->assertForbidden();
    }

    public function test_staff_sees_limited_navigation_and_read_only_inventory(): void
    {
        $staff = $this->makeUser('staff');

        $dash = $this->actingAs($staff)->get('/staff/dashboard');
        $dash->assertOk();
        $dash->assertDontSee('/reports', false);
        $dash->assertDontSee('/user-management', false);

        $response = $this->actingAs($staff)->get('/inventory');
        $response->assertOk();
        $response->assertDontSee('/reports', false);
        $response->assertDontSee('/user-management', false);
        $response->assertDontSee('Add New Item', false);
        $response->assertDontSee('ACTIONS', false);
        $response->assertSee('MAIN', false);
        $response->assertSee('MANAGEMENT', false);
        $response->assertDontSee('ADMIN', false);

        $history = $this->actingAs($staff)->get('/transaction-history');
        $history->assertOk();
        $history->assertDontSee('/reports', false);
        $history->assertDontSee('/user-management', false);
    }

    public function test_admin_keeps_full_navigation_and_inventory_actions(): void
    {
        $admin = $this->makeUser('admin');

        $response = $this->actingAs($admin)->get('/inventory');
        $response->assertOk();
        $response->assertSee('/reports', false);
        $response->assertSee('/user-management', false);
        $response->assertSee('Add New Item', false);
        $response->assertSee('ACTIONS', false);
        $response->assertSee('MAIN', false);
        $response->assertSee('MANAGEMENT', false);
        $response->assertSee('ADMIN', false);
    }

    public function test_logout_returns_to_login(): void
    {
        $staff = $this->makeUser('staff');

        $response = $this->actingAs($staff)->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_role_pages_render(): void
    {
        $admin = $this->makeUser('admin');
        $staff = $this->makeUser('staff');

        $this->actingAs($admin)->get('/dashboard')->assertOk();
        $this->actingAs($staff)->get('/pos')->assertOk();
        $this->actingAs($staff)->get('/repair-management')->assertOk();
        $this->actingAs($admin)->get('/reports')->assertOk();
        $this->actingAs($admin)->get('/user-management')->assertOk();

        $breakdown = $this->actingAs($admin)->get('/reports');
        $breakdown->assertSee('NET REVENUE', false);
        $breakdown->assertSee('PROFIT', false);
    }

    public function test_root_goes_straight_to_login_for_guests(): void
    {
        $this->get('/')->assertRedirect('/login');

        $admin = $this->makeUser('admin');
        $staff = $this->makeUser('staff');

        $this->actingAs($admin)->get('/')->assertRedirect('/dashboard');
        $this->actingAs($staff)->get('/')->assertRedirect('/staff/dashboard');
    }

    public function test_dashboard_cards_link_to_their_features(): void
    {
        $admin = $this->makeUser('admin');

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertOk();
        $response->assertSee("TODAY'S TRANSACTIONS", false);
        $response->assertSee('stat-link', false);
        $response->assertSee('/reports?filter=today', false);
        $response->assertSee('/transaction-history?date=today', false);
        $response->assertSee('/repair-management?status=pending', false);
    }

    public function test_sales_overview_has_trajectory_and_insights(): void
    {
        $this->seed(SampleDataSeeder::class);
        $admin = User::where('email', 'test@example.com')->firstOrFail();

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertOk();
        $response->assertSee('Total Revenue Trajectory', false);
        $response->assertSee('Peak Operational Day', false);
        $response->assertSee('Top Grossing Category', false);
        $response->assertSee('Retail Conversion Rate', false);
        // Seeded demo: Audio (₱1,499) out-grosses every other category.
        $response->assertSee('Audio', false);
        // Both seeded repair clients (Juan, Maria) also bought retail.
        $response->assertSee('100.0% (of repair clients)', false);

        $staff = $this->makeUser('staff');
        $this->actingAs($staff)->get('/staff/dashboard')->assertOk();
    }

    public function test_admin_dashboard_shows_staff_performance(): void
    {
        $this->seed(SampleDataSeeder::class);
        $admin = User::where('email', 'test@example.com')->firstOrFail();

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertOk();
        $response->assertSee('Staff Performance', false);
        $response->assertSee('Alex Technician', false);
        $response->assertSee('Revenue Generated', false);

        // Role filter keeps matching staff, drops the rest.
        $filtered = $this->actingAs($admin)->get('/dashboard?staff_role=staff');
        $filtered->assertOk();
        $filtered->assertSee('Alex Technician', false);
        $filtered->assertSee('Casey Clerk', false);

        $adminOnly = $this->actingAs($admin)->get('/dashboard?staff_role=admin');
        $adminOnly->assertOk();
        $adminOnly->assertSee('Sonayah Faisal', false);
        $adminOnly->assertDontSee('Alex Technician', false);
        $adminOnly->assertDontSee('Casey Clerk', false);

        // Staff never sees peer performance.
        $staff = $this->makeUser('staff');
        $this->actingAs($staff)->get('/staff/dashboard')->assertDontSee('Staff Performance', false);
    }

    public function test_recent_transactions_actions_link_correctly(): void
    {
        $this->seed(SampleDataSeeder::class);
        $admin = User::where('email', 'test@example.com')->firstOrFail();
        $sale = Sale::where('status', 'completed')->firstOrFail();
        $ticket = RepairTicket::whereIn('status', ['completed', 'in_progress'])->firstOrFail();

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertOk();
        // Retail rows: receipt view + direct print link.
        $response->assertSee('/sales/'.$sale->id, false);
        $response->assertSee('/sales/'.$sale->id.'?print=1', false);
        // Repair rows: ticket detail instead of a dead icon.
        $response->assertSee('/repair-management/'.$ticket->id, false);
        $response->assertSee('View ticket', false);

        // Print link renders a receipt that auto-prints.
        $receipt = $this->actingAs($admin)->get('/sales/'.$sale->id.'?print=1');
        $receipt->assertOk();
        $receipt->assertSee("addEventListener('load'", false);
    }

    public function test_master_data_modules_are_admin_only(): void
    {
        $staff = $this->makeUser('staff');
        $admin = $this->makeUser('admin');

        foreach (['/categories', '/categories/add', '/suppliers', '/suppliers/add', '/service-types', '/service-types/add'] as $url) {
            $this->actingAs($staff)->get($url)->assertForbidden();
            $this->actingAs($admin)->get($url)->assertOk();
        }
    }

    public function test_master_data_crud_round_trip(): void
    {
        $admin = $this->makeUser('admin');

        $this->actingAs($admin)->post('/categories', ['category_name' => 'Test Cat'])
            ->assertRedirect('/categories');
        $this->assertDatabaseHas('categories', ['category_name' => 'Test Cat']);

        $category = Category::where('category_name', 'Test Cat')->firstOrFail();
        $this->actingAs($admin)->get('/categories/'.$category->id.'/edit')->assertOk();
        $this->actingAs($admin)->put('/categories/'.$category->id, ['category_name' => 'Renamed Cat'])
            ->assertRedirect('/categories');
        $this->assertDatabaseHas('categories', ['category_name' => 'Renamed Cat']);

        $this->actingAs($admin)->post('/service-types', ['name' => 'Test Service'])
            ->assertRedirect('/service-types');
        $this->assertDatabaseHas('service_types', ['name' => 'Test Service']);

        $this->actingAs($admin)->post('/suppliers', ['supplier_name' => 'Test Supplier'])
            ->assertRedirect('/suppliers');
        $this->assertDatabaseHas('suppliers', ['supplier_name' => 'Test Supplier']);
    }

    public function test_receipt_shows_cashier_name(): void
    {
        $this->seed(SampleDataSeeder::class);
        $admin = User::where('email', 'test@example.com')->firstOrFail();
        $sale = Sale::firstOrFail();

        $response = $this->actingAs($admin)->get('/sales/'.$sale->id);
        $response->assertOk();
        $response->assertSee('Served By', false);
        $response->assertSee($sale->user->name, false);
    }

    public function test_repair_ticket_accepts_empty_optional_fields(): void
    {
        $admin = $this->makeUser('admin');
        $tech = $this->makeUser('staff');

        $response = $this->actingAs($admin)->post('/repair-management', [
            'customer_name' => 'QA Customer',
            'phone_number' => '09000000001',
            'address' => null,
            'brand' => 'QA Brand',
            'model' => 'QA Model',
            'serial_or_imei' => null,
            'service_type' => 'Screen Replacement',
            'problem_description' => null,
            'assigned_to' => $tech->id,
            'quotation_price' => null,
        ]);

        $response->assertRedirect('/repair-management');
        $this->assertDatabaseHas('customers', ['phone' => '09000000001', 'address' => '']);
        $this->assertDatabaseHas('devices', ['brand' => 'QA Brand', 'serial_or_imei' => '']);
    }

    public function test_repair_ticket_show_and_edit_render(): void
    {
        $this->seed(SampleDataSeeder::class);
        $admin = User::where('email', 'test@example.com')->firstOrFail();
        $ticket = RepairTicket::firstOrFail();

        $this->actingAs($admin)->get('/repair-management/'.$ticket->id)->assertOk();
        $this->actingAs($admin)->get('/repair-management/'.$ticket->id.'/edit')->assertOk();
    }

    public function test_repair_ticket_update_changes_status(): void
    {
        $this->seed(SampleDataSeeder::class);
        $admin = User::where('email', 'test@example.com')->firstOrFail();
        $ticket = RepairTicket::where('status', 'pending')->firstOrFail();

        $response = $this->actingAs($admin)->put('/repair-management/'.$ticket->id, [
            'device_id' => $ticket->device_id,
            'assigned_to' => $ticket->assigned_to,
            'problem_description' => $ticket->problem_description,
            'quotation_price' => $ticket->quotation_price,
            'final_price' => $ticket->final_price,
            'status' => 'in_progress',
            'date_received' => $ticket->date_received->format('Y-m-d H:i:s'),
            'date_completed' => null,
        ]);

        $response->assertRedirect('/repair-management');
        $this->assertDatabaseHas('repair_tickets', ['id' => $ticket->id, 'status' => 'in_progress']);
    }

    public function test_product_show_renders(): void
    {
        $this->seed(SampleDataSeeder::class);
        $admin = User::where('email', 'test@example.com')->firstOrFail();
        $product = Product::firstOrFail();

        $response = $this->actingAs($admin)->get('/inventory/'.$product->id);
        $response->assertOk();
        $response->assertSee($product->product_name, false);
    }

    public function test_user_update_rejects_unknown_status(): void
    {
        $admin = $this->makeUser('admin');

        $response = $this->actingAs($admin)->from('/user-management/'.$admin->id.'/edit')->put(
            '/user-management/'.$admin->id,
            [
                'name' => $admin->name,
                'email' => $admin->email,
                'role' => 'admin',
                'status' => 'inactive',
            ]
        );

        $response->assertRedirect('/user-management/'.$admin->id.'/edit');
        $response->assertSessionHasErrors('status');
        $this->assertDatabaseMissing('users', ['id' => $admin->id, 'status' => 'inactive']);
    }

    public function test_product_store_saves_low_stock_threshold(): void
    {
        $admin = $this->makeUser('admin');
        $category = Category::create(['category_name' => 'QA Cat']);
        $supplier = Supplier::create(['supplier_name' => 'QA Sup', 'contact_info' => 'QA Contact', 'location' => 'QA Location']);

        $response = $this->actingAs($admin)->post('/inventory', [
            'product_name' => 'QA Widget',
            'sku' => 'QA-TH-001',
            'category_id' => $category->id,
            'supplier_id' => $supplier->id,
            'cost_price' => 100,
            'sell_price' => 130,
            'stock_quantity' => 25,
            'low_stock_threshold' => 5,
        ]);

        $response->assertRedirect('/inventory/add');
        $this->assertDatabaseHas('products', ['sku' => 'QA-TH-001', 'low_stock_threshold' => 5]);
    }

    public function test_low_stock_status_uses_per_product_threshold(): void
    {
        $admin = $this->makeUser('admin');
        $category = Category::create(['category_name' => 'QA Cat']);
        $supplier = Supplier::create(['supplier_name' => 'QA Sup', 'contact_info' => 'QA Contact', 'location' => 'QA Location']);

        // Same stock, different thresholds: only the custom one reads Low Stock.
        Product::create([
            'product_name' => 'Custom Low',
            'sku' => 'QA-TH-002',
            'category_id' => $category->id,
            'supplier_id' => $supplier->id,
            'cost_price' => 10,
            'sell_price' => 13,
            'stock_quantity' => 15,
            'low_stock_threshold' => 20,
        ]);
        Product::create([
            'product_name' => 'Normal Stock',
            'sku' => 'QA-TH-003',
            'category_id' => $category->id,
            'supplier_id' => $supplier->id,
            'cost_price' => 10,
            'sell_price' => 13,
            'stock_quantity' => 15,
            'low_stock_threshold' => 10,
        ]);

        $low = $this->actingAs($admin)->get('/inventory?status=low_stock');
        $low->assertOk();
        $low->assertSee('Custom Low', false);
        $low->assertDontSee('Normal Stock', false);

        $inStock = $this->actingAs($admin)->get('/inventory?status=in_stock');
        $inStock->assertOk();
        $inStock->assertSee('Normal Stock', false);
        $inStock->assertDontSee('Custom Low', false);
    }

    public function test_master_data_lists_paginate(): void
    {
        $admin = $this->makeUser('admin');

        for ($i = 1; $i <= 12; $i++) {
            Category::create(['category_name' => 'QA Cat '.str_pad((string) $i, 2, '0', STR_PAD_LEFT)]);
            Supplier::create([
                'supplier_name' => 'QA Sup '.str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'contact_info' => 'QA Contact',
                'location' => 'QA Location',
            ]);
            ServiceType::create(['name' => 'QA Svc '.str_pad((string) $i, 2, '0', STR_PAD_LEFT)]);
        }

        foreach ([
            ['/categories', 'QA Cat 01', 'QA Cat 11'],
            ['/suppliers', 'QA Sup 01', 'QA Sup 11'],
            ['/service-types', 'QA Svc 01', 'QA Svc 11'],
        ] as [$url, $firstPage, $secondPage]) {
            $page1 = $this->actingAs($admin)->get($url);
            $page1->assertOk();
            $page1->assertSee($firstPage, false);
            $page1->assertDontSee($secondPage, false);

            $page2 = $this->actingAs($admin)->get($url.'?page=2');
            $page2->assertOk();
            $page2->assertSee($secondPage, false);
        }
    }

    public function test_master_data_lists_search(): void
    {
        $admin = $this->makeUser('admin');

        Category::create(['category_name' => 'QA Batteries']);
        Category::create(['category_name' => 'QA Chargers']);
        Supplier::create(['supplier_name' => 'QA Apex', 'contact_info' => 'c1', 'location' => 'Davao']);
        Supplier::create(['supplier_name' => 'QA Metro', 'contact_info' => 'c2', 'location' => 'Manila']);
        ServiceType::create(['name' => 'QA Screen Fix']);
        ServiceType::create(['name' => 'QA Battery Fix']);

        $cats = $this->actingAs($admin)->get('/categories?search=Batter');
        $cats->assertOk();
        $cats->assertSee('QA Batteries', false);
        $cats->assertDontSee('QA Chargers', false);

        // Suppliers match name, contact info, or location.
        $sup = $this->actingAs($admin)->get('/suppliers?search=Manila');
        $sup->assertOk();
        $sup->assertSee('QA Metro', false);
        $sup->assertDontSee('QA Apex', false);

        $svc = $this->actingAs($admin)->get('/service-types?search=Screen');
        $svc->assertOk();
        $svc->assertSee('QA Screen Fix', false);
        $svc->assertDontSee('QA Battery Fix', false);
    }
}
