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
 *   manager / test@example.com / password123  (role admin)
 *   technician / tech@example.com / password123 (role staff)
 *   clerk / clerk@example.com / password123 (role staff)
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
        $tech = $this->user('Alex Technician', 'tech@example.com', 'staff');
        $this->user('Casey Clerk', 'clerk@example.com', 'staff');

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

        // ---- Products: healthy, low (<=10), and out-of-stock ----
        $products = [
            ['iPhone 13 OLED Screen', 'SCR-IP13-001', 'Audio', 1800, 2500, 15],
            ['Samsung A54 Battery', 'BAT-SA54-002', 'Batteries', 450, 750, 8],
            ['65W Fast Charger', 'CHR-65W-003', 'Chargers & Cables', 300, 550, 25],
            ['Type-C Braided Cable 1m', 'CBL-TC1M-004', 'Chargers & Cables', 80, 150, 4],
            ['Clear Case iPhone 14', 'CAS-IP14-005', 'Cases & Covers', 120, 220, 0],
            ['Tempered Glass iPhone 13', 'GLS-IP13-006', 'Screen Protectors', 40, 99, 60],
            ['Wireless Earbuds Pro', 'AUD-TWS-007', 'Audio', 900, 1499, 6],
            ['Charging Port Flex (Generic)', 'PRT-FLEX-008', 'Batteries', 200, 350, 12],
        ];
        foreach ($products as [$name, $sku, $category, $cost, $sell, $stock]) {
            Product::updateOrCreate(
                ['sku' => $sku],
                [
                    'product_name' => $name,
                    'category_id' => $cat($category)->id,
                    'supplier_id' => ($sku === 'CHR-65W-003' || $sku === 'CBL-TC1M-004') ? $metro->id : $apex->id,
                    'cost_price' => $cost,
                    'sell_price' => $sell,
                    'stock_quantity' => $stock,
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
