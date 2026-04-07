<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Users ─────────────────────────────
        $admin = User::create([
            'name'     => 'Admin',
            'email'    => 'admin@dinepos.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        User::create([
            'name'     => 'Kasir Utama',
            'email'    => 'kasir@dinepos.com',
            'password' => Hash::make('password'),
            'role'     => 'kasir',
        ]);

        // ── Categories ────────────────────────
        $categories = [
            ['name' => 'Dimsum',   'slug' => 'dimsum'],
            ['name' => 'Minuman',  'slug' => 'minuman'],
            ['name' => 'Makanan',  'slug' => 'makanan'],
            ['name' => 'Snack',    'slug' => 'snack'],
        ];
        foreach ($categories as $cat) {
            Category::create($cat + ['description' => null]);
        }

        // ── Products ──────────────────────────
        $products = [
            ['category_id' => 1, 'name' => 'Dimsum Mentai Keju',  'slug' => 'dimsum-mentai-keju',  'price' => 25000, 'cost_price' => 12000, 'stock' => 3],
            ['category_id' => 1, 'name' => 'Dimsum Ayam',         'slug' => 'dimsum-ayam',          'price' => 20000, 'cost_price' => 9000,  'stock' => 2],
            ['category_id' => 1, 'name' => 'Dimsum Mozarella',    'slug' => 'dimsum-mozarella',     'price' => 28000, 'cost_price' => 13000, 'stock' => 4],
            ['category_id' => 1, 'name' => 'Dimsum Udang',        'slug' => 'dimsum-udang',         'price' => 30000, 'cost_price' => 14000, 'stock' => 10],
            ['category_id' => 2, 'name' => 'Es Teh Manis',        'slug' => 'es-teh-manis',         'price' => 8000,  'cost_price' => 3000,  'stock' => 50],
            ['category_id' => 2, 'name' => 'Jus Alpukat',         'slug' => 'jus-alpukat',          'price' => 18000, 'cost_price' => 8000,  'stock' => 20],
            ['category_id' => 3, 'name' => 'Nasi Goreng',         'slug' => 'nasi-goreng',          'price' => 35000, 'cost_price' => 15000, 'stock' => 15],
            ['category_id' => 4, 'name' => 'Keripik Singkong',    'slug' => 'keripik-singkong',     'price' => 12000, 'cost_price' => 5000,  'stock' => 30],
        ];
        foreach ($products as $p) {
            Product::create($p + ['is_active' => true]);
        }

        // ── Orders & Transactions (last 7 days) ──
        $days = collect(range(0, 6))->map(fn($d) => now()->subDays($d));
        $productList = Product::all();

        foreach ($days as $day) {
            $ordersPerDay = rand(2, 5);
            for ($o = 0; $o < $ordersPerDay; $o++) {
                $order = Order::create([
                    'user_id'        => $admin->id,
                    'order_code'     => 'ORD-' . strtoupper(Str::random(8)),
                    'total'          => 0,
                    'status'         => 'completed',
                    'payment_method' => collect(['tunai', 'qris', 'transfer'])->random(),
                    'notes'          => null,
                    'created_at'     => $day->copy()->addHours(rand(8, 20)),
                ]);

                $total = 0;
                $itemCount = rand(1, 3);
                $selectedProducts = $productList->random($itemCount);

                foreach ($selectedProducts as $prod) {
                    $qty = rand(1, 3);
                    $subtotal = $prod->price * $qty;
                    $total += $subtotal;

                    OrderItem::create([
                        'order_id'     => $order->id,
                        'product_id'   => $prod->id,
                        'product_name' => $prod->name,
                        'qty'          => $qty,
                        'price'        => $prod->price,
                        'subtotal'     => $subtotal,
                    ]);
                }

                $order->update(['total' => $total]);

                Transaction::create([
                    'order_id'       => $order->id,
                    'invoice_code'   => 'INV-' . $day->format('Ymd') . '-' . strtoupper(Str::random(6)),
                    'amount'         => $total,
                    'payment_method' => $order->payment_method,
                    'payment_status' => 'paid',
                    'paid_at'        => $order->created_at->addMinutes(rand(2, 10)),
                    'change_amount'  => 0,
                    'created_at'     => $order->created_at,
                ]);
            }
        }

        // ── Pending orders (for dashboard) ───
        $pendingNotes = [
            ['name' => 'Dimsum Mentai Keju', 'qty' => 2, 'note' => 'pedasnya dikit'],
            ['name' => 'Dimsum Ayam',        'qty' => 1, 'note' => 'jangan pake mayonaise'],
        ];
        foreach ($pendingNotes as $pn) {
            $prod = Product::where('name', $pn['name'])->first();
            $order = Order::create([
                'user_id'        => $admin->id,
                'order_code'     => 'ORD-' . strtoupper(Str::random(8)),
                'total'          => $prod ? $prod->price * $pn['qty'] : 0,
                'status'         => 'pending',
                'payment_method' => null,
                'notes'          => $pn['note'],
            ]);
            if ($prod) {
                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $prod->id,
                    'product_name' => $prod->name,
                    'qty'          => $pn['qty'],
                    'price'        => $prod->price,
                    'subtotal'     => $prod->price * $pn['qty'],
                ]);
            }
        }

        $this->command->info('✅ Seeder selesai! Login: admin@dinepos.com / password');
    }
}
