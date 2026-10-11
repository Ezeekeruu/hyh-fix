<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Device;
use App\Models\Product;
use App\Models\RepairStatusHistory;
use App\Models\RepairTicket;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\ServiceType;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Demo / test data for the HYH Fix app.
 *
 * Covers every working feature: users, categories, suppliers,
 * service types, products (in-stock / low / out), customers,
 * devices, repair tickets (pending / in-progress / completed),
 * sales + sale items (Cash / GCash, completed + voided).
 *
 * No login screen exists in the app, but these accounts are used
 * as sale cashiers (sales.user_id) and repair assignees, and are
 * ready if auth is added later.
 *
 *   admin / test@example.com / password123  (role admin)
 *   technician / tech@example.com / password123 (role technician)
 *   clerk / clerk@example.com / password123 (role sales-clerk)
 *
 * Load:   php artisan db:seed --class=Database\\Seeders\\SampleDataSeeder
 *         (or php artisan migrate:fresh --seed  — wipes all data first)
 * Remove: php artisan migrate:fresh   (wipes everything, no seed)
 *         or delete just these rows: sample records use the distinctive
 *         emails/SKUs/phones below, e.g.
 *         User::whereIn('email',['test@example.com','tech@example.com','clerk@example.com'])->delete();
 */
class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // ---- Users (cashiers / technicians / history authors) ----
        $manager = $this->user('Sonayah Faisal', 'test@example.com', 'admin');
        $tech = $this->user('Alex Technician', 'tech@example.com', 'technician');
        $this->user('Casey Clerk', 'clerk@example.com', 'sales-clerk');

        // ---- Categories ----
        foreach (['Batteries', 'Chargers & Cables', 'Cases & Covers', 'Screen Protectors', 'Audio'] as $name) {
            Category::firstOrCreate(['category_name' => $name]);
        }

        // ---- Suppliers ----
        $apex = Supplier::firstOrCreate(
            ['supplier_name' => 'Apex Tech Supplies'],
            ['contact_info' => '0917-123-4567 / sales@apex.com', 'location' => 'Davao City, Philippines']
        );
        $metro = Supplier::firstOrCreate(
            ['supplier_name' => 'Metro Parts Depot'],
            ['contact_info' => '088-555-0119 / orders@metroparts.ph', 'location' => 'Cagayan de Oro, Philippines']
        );

        // ---- Service types (repair dropdown + quick-add) ----
        foreach (['Screen Replacement', 'Battery Replacement', 'Charging Port Repair', 'Water Damage Repair', 'Software Troubleshooting'] as $name) {
            ServiceType::firstOrCreate(['name' => $name]);
        }

        $cat = fn (string $n) => Category::where('category_name', $n)->firstOrFail();

        // ---- Products: healthy, low (at/below their own threshold), and out-of-stock ----
        $products = [
            // name, sku, category, cost, sell, stock, low-stock threshold
            ['iPhone 13 OLED Screen', 'SCR-IP13-001', 'Audio', 1800, 2500, 15, 10],
            ['Samsung A54 Battery', 'BAT-SA54-002', 'Batteries', 450, 750, 8, 10],
            ['65W Fast Charger', 'CHR-65W-003', 'Chargers & Cables', 300, 550, 25, 10],
            ['Type-C Braided Cable 1m', 'CBL-TC1M-004', 'Chargers & Cables', 80, 150, 4, 10],
            ['Clear Case iPhone 14', 'CAS-IP14-005', 'Cases & Covers', 120, 220, 0, 10],
            ['Tempered Glass iPhone 13', 'GLS-IP13-006', 'Screen Protectors', 40, 99, 60, 20],
            ['Wireless Earbuds Pro', 'AUD-TWS-007', 'Audio', 900, 1499, 6, 10],
            ['Charging Port Flex (Generic)', 'PRT-FLEX-008', 'Batteries', 200, 350, 12, 10],
        ];
        foreach ($products as [$name, $sku, $category, $cost, $sell, $stock, $threshold]) {
            Product::updateOrCreate(
                ['sku' => $sku],
                [
                    'product_name' => $name,
                    'category_id' => $cat($category)->id,
                    'supplier_id' => ($sku === 'CHR-65W-003' || $sku === 'CBL-TC1M-004') ? $metro->id : $apex->id,
                    'cost_price' => $cost,
                    'sell_price' => $sell,
                    'stock_quantity' => $stock,
                    'low_stock_threshold' => $threshold,
                    'image_path' => null,
                ]
            );
        }

        // ---- Customers ----
        $juan = Customer::firstOrCreate(
            ['phone' => '09171234567'],
            ['name' => 'Juan Dela Cruz', 'address' => 'Poblacion, Davao City']
        );
        $maria = Customer::firstOrCreate(
            ['phone' => '09181234567'],
            ['name' => 'Maria Santos', 'address' => 'Buhangin, Davao City']
        );
        Customer::firstOrCreate(
            ['phone' => '09191234567'],
            ['name' => 'Jose Rizal', 'address' => 'Toril, Davao City']
        );

        // ---- Devices ----
        $d1 = Device::firstOrCreate(
            ['serial_or_imei' => '358201091234567'],
            ['customer_id' => $juan->id, 'brand' => 'Apple', 'model' => 'iPhone 13 Pro Max']
        );
        $d2 = Device::firstOrCreate(
            ['serial_or_imei' => '860123045678901'],
            ['customer_id' => $maria->id, 'brand' => 'Samsung', 'model' => 'Galaxy A54']
        );

        // ---- Repair tickets: pending / in_progress / completed ----
        $t1 = RepairTicket::firstOrCreate(
            ['device_id' => $d1->id, 'service_type' => 'Screen Replacement', 'status' => 'pending'],
            [
                'assigned_to' => $tech->id,
                'problem_description' => 'Cracked screen, touch still works.',
                'quotation_price' => 2500,
                'final_price' => 2500,
                'date_received' => now()->subDays(2),
                'date_completed' => null,
            ]
        );
        $t2 = RepairTicket::firstOrCreate(
            ['device_id' => $d2->id, 'service_type' => 'Battery Replacement', 'status' => 'in_progress'],
            [
                'assigned_to' => $tech->id,
                'problem_description' => 'Battery drains in 2 hours.',
                'quotation_price' => 1200,
                'final_price' => 1200,
                'date_received' => now()->subDays(5),
                'date_completed' => null,
            ]
        );
        $t3 = RepairTicket::firstOrCreate(
            ['device_id' => $d1->id, 'service_type' => 'Charging Port Repair', 'status' => 'completed'],
            [
                'assigned_to' => $tech->id,
                'problem_description' => 'Loose charging port.',
                'quotation_price' => 800,
                'final_price' => 900,
                'date_received' => now()->subDays(9),
                'date_completed' => now()->subDays(3),
            ]
        );
        foreach ([$t1, $t2, $t3] as $t) {
            RepairStatusHistory::firstOrCreate(
                ['repair_ticket_id' => $t->id, 'status' => $t->status],
                ['changed_by' => $manager->id, 'changed_at' => now()]
            );
        }

        // ---- Extra devices (all Juan's/Maria's, so the demo retail
        // conversion insight stays meaningful) ----
        $extraDevices = [
            // imei, owner, brand, model
            ['867123045678901', $juan->id, 'Xiaomi', 'Redmi Note 12'],
            ['869123045678902', $maria->id, 'Oppo', 'Reno 8'],
            ['860123045678903', $juan->id, 'Vivo', 'Y36'],
            ['358201091234568', $maria->id, 'Apple', 'iPhone 11'],
            ['860123045678904', $juan->id, 'Samsung', 'Galaxy S22'],
            ['867123045678905', $maria->id, 'Realme', '10 Pro'],
        ];
        $more = [];
        foreach ($extraDevices as [$imei, $owner, $brand, $model]) {
            $more[] = Device::firstOrCreate(
                ['serial_or_imei' => $imei],
                ['customer_id' => $owner, 'brand' => $brand, 'model' => $model]
            );
        }
        [$d3, $d4, $d5, $d6, $d7, $d8] = $more;

        // ---- Extra repair tickets: 17 more, 20 total, spread over the
        // last ~6 weeks so charts and filters have something to show ----
        $extraTickets = [
            // device, service, status, problem, quote, final, receivedDaysAgo, completedDaysAgo|null
            [$d3, 'Screen Replacement', 'pending', 'Cracked display, lines on screen.', 2800, 2800, 1, null],
            [$d3, 'Battery Replacement', 'completed', 'Phone shuts off at 20%.', 1500, 1500, 12, 10],
            [$d4, 'Water Damage Repair', 'in_progress', 'Dropped in water, drying + board check.', 3500, 3500, 4, null],
            [$d4, 'Software Troubleshooting', 'completed', 'Bootloop after update.', 500, 500, 20, 19],
            [$d5, 'Battery Replacement', 'pending', 'Bloated battery, needs swap.', 1100, 1100, 3, null],
            [$d5, 'Screen Replacement', 'in_progress', 'Dead pixels spreading.', 2300, 2300, 6, null],
            [$d6, 'Screen Replacement', 'completed', 'Shattered front glass.', 3200, 3200, 30, 27],
            [$d6, 'Charging Port Repair', 'pending', 'Cable only charges at an angle.', 900, 900, 2, null],
            [$d7, 'Charging Port Repair', 'completed', 'No charging at all.', 950, 950, 15, 13],
            [$d7, 'Water Damage Repair', 'pending', 'Rain damage, speakers crackling.', 4000, 4000, 1, null],
            [$d8, 'Software Troubleshooting', 'in_progress', 'Apps crashing, storage full.', 600, 600, 7, null],
            [$d8, 'Battery Replacement', 'completed', 'Overheats while charging.', 1300, 1300, 40, 38],
            [$d1, 'Battery Replacement', 'completed', 'Health at 72%, drains fast.', 1400, 1400, 25, 24],
            [$d2, 'Screen Replacement', 'pending', 'Hairline crack, touch OK.', 2600, 2600, 2, null],
            [$d2, 'Software Troubleshooting', 'completed', 'Forgot password, reset needed.', 550, 550, 33, 31],
            [$d4, 'Screen Replacement', 'cancelled', 'Customer cancelled before parts arrived.', 2400, 0, 18, null],
            [$d6, 'Battery Replacement', 'in_progress', 'Swollen back panel.', 1250, 1250, 8, null],
        ];
        foreach ($extraTickets as [$device, $service, $status, $problem, $quote, $final, $receivedAgo, $completedAgo]) {
            $ticket = RepairTicket::firstOrCreate(
                ['device_id' => $device->id, 'service_type' => $service, 'status' => $status],
                [
                    'assigned_to' => $tech->id,
                    'problem_description' => $problem,
                    'quotation_price' => $quote,
                    'final_price' => $final,
                    'date_received' => now()->subDays($receivedAgo),
                    'date_completed' => $completedAgo === null ? null : now()->subDays($completedAgo),
                ]
            );
            RepairStatusHistory::firstOrCreate(
                ['repair_ticket_id' => $ticket->id, 'status' => $ticket->status],
                ['changed_by' => $manager->id, 'changed_at' => now()]
            );
        }

        // ---- Sales + items (today + this week, Cash + GCash) ----
        $this->sale($manager->id, $juan->id, 'Cash', 'completed', now()->subHours(3), [
            ['GLS-IP13-006', 2],
            ['CBL-TC1M-004', 1],
        ]);
        $this->sale($manager->id, null, 'GCash', 'completed', now()->subDays(2), [
            ['CHR-65W-003', 1],
            ['AUD-TWS-007', 1],
        ]);
        $this->sale($manager->id, $maria->id, 'Cash', 'completed', now()->subDays(6), [
            ['BAT-SA54-002', 1],
        ]);
        $this->sale($manager->id, null, 'Cash', 'voided', now()->subDays(1), [
            ['GLS-IP13-006', 1],
        ]);
    }

    private function user(string $name, string $email, string $role): User
    {
        $user = User::firstOrNew(['email' => $email]);
        $user->name = $name;
        $user->password = Hash::make('password123');
        $user->role = $role;
        $user->status = 'active';
        $user->save();

        return $user;
    }

    /** @param array<int, array{0:string,1:int}> $items  [sku, qty] */
    private function sale(int $userId, ?int $customerId, string $method, string $status, $date, array $items): Sale
    {
        $total = 0;
        $lines = [];
        foreach ($items as [$sku, $qty]) {
            $p = Product::where('sku', $sku)->firstOrFail();
            $sub = $p->sell_price * $qty;
            $total += $sub;
            $lines[] = ['product_id' => $p->id, 'quantity' => $qty, 'unit_price' => $p->sell_price, 'subtotal' => $sub];
        }

        $sale = Sale::firstOrCreate(
            ['user_id' => $userId, 'customer_id' => $customerId, 'sale_date' => $date, 'total_amount' => $total],
            ['payment_method' => $method, 'status' => $status]
        );

        foreach ($lines as $line) {
            SaleItem::firstOrCreate(
                ['sale_id' => $sale->id, 'product_id' => $line['product_id'], 'quantity' => $line['quantity']],
                ['unit_price' => $line['unit_price'], 'subtotal' => $line['subtotal']]
            );
        }

        return $sale;
    }
}
