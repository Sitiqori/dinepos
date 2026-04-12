<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class KasirController extends Controller
{
    public function index()
    {
        $categories = Category::with([
            'products' => fn($q) => $q->where('is_active', true)->where('stock', '>', 0)
        ])->get();

        $products = Product::where('is_active', true)
            ->with('category')
            ->when(Schema::hasColumn('products', 'sku'), fn($q) => $q->orderBy('sku'))
            ->when(!Schema::hasColumn('products', 'sku'), fn($q) => $q->orderBy('name'))
            ->get();

        return view('kasir.index', compact('categories', 'products'));
    }

    public function createOrder(Request $request)
    {
        $data = $request->validate([
            'items'          => ['required', 'array', 'min:1'],
            'items.*.id'     => ['required', 'exists:products,id'],
            'items.*.qty'    => ['required', 'integer', 'min:1'],
            'order_type'     => ['nullable', 'in:dine_in,take_away'],
            'payment_method' => ['required', 'in:tunai,qris,transfer,cash'],
            'cash_given'     => ['nullable', 'integer', 'min:0'],
            'ppn_on'         => ['nullable'],
            'customer_name'  => ['nullable', 'string', 'max:100'],
            'notes'          => ['nullable', 'string', 'max:500'],
            'table_number'   => ['nullable', 'string', 'max:20'],
        ]);

        // Normalize payment method
        $payMethod = $data['payment_method'] === 'cash' ? 'tunai' : $data['payment_method'];

        DB::beginTransaction();
        try {
            // Calculate totals
            $subtotal = 0;
            $lines    = [];

            foreach ($data['items'] as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['id']);

                if ($product->stock < $item['qty']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Stok {$product->name} tidak cukup (tersisa {$product->stock}).",
                    ], 422);
                }

                $sub       = $product->price * $item['qty'];
                $subtotal += $sub;
                $lines[]   = compact('product', 'item', 'sub');
            }

            $ppnOn   = filter_var($data['ppn_on'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $ppn     = $ppnOn ? (int) round($subtotal * 0.11) : 0;
            $total   = $subtotal + $ppn;
            $rem     = $total % 500;
            $rounded = $rem === 0 ? $total : $total + (500 - $rem);

            // Build order data
            $orderData = [
                'user_id'        => auth()->id(),
                'customer_name'  => $data['customer_name'] ?? null,
                'order_code'     => 'ORD-'.strtoupper(Str::random(8)),
                'total'          => $rounded,
                'status'         => 'pending',  // ← PENDING, bukan completed
                'payment_method' => $payMethod,
                'notes'          => $data['notes'] ?? null,
                'table_number'   => $data['table_number'] ?? null,
            ];

            if (Schema::hasColumn('orders', 'order_type')) {
                $orderData['order_type'] = $data['order_type'] ?? 'dine_in';
            }

            $order = Order::create($orderData);

            // Create items + decrement stock
            foreach ($lines as $line) {
                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $line['product']->id,
                    'product_name' => $line['product']->name,
                    'qty'          => $line['item']['qty'],
                    'price'        => $line['product']->price,
                    'subtotal'     => $line['sub'],
                ]);

                $line['product']->decrement('stock', $line['item']['qty']);
            }

            // Create transaction with status pending — will be paid when order completed
            $cashGiven    = (int) ($data['cash_given'] ?? $rounded);
            $changeAmount = max(0, $cashGiven - $rounded);

            $transaction = Transaction::create([
                'order_id'       => $order->id,
                'invoice_code'   => 'INV-'.now()->format('Ymd').'-'.strtoupper(Str::random(6)),
                'amount'         => $rounded,
                'payment_method' => $payMethod,
                'payment_status' => 'paid',   // payment confirmed at POS
                'paid_at'        => now(),
                'change_amount'  => $changeAmount,
            ]);

            DB::commit();

            // Kirim stok terbaru ke frontend supaya kartu produk update tanpa reload
            $updatedStock = collect($lines)->map(fn($l) => [
                'id'    => (int) $l['product']->id,
                'stock' => (int) $l['product']->fresh()->stock,
            ])->values();

            return response()->json([
                'success'       => true,
                'message'       => 'Pesanan berhasil dibuat dan menunggu diproses.',
                'order_code'    => $order->order_code,
                'invoice_code'  => $transaction->invoice_code,
                'updated_stock' => $updatedStock,
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            $msg = config('app.debug')
                ? 'Error: '.$e->getMessage().' (line '.$e->getLine().' in '.$e->getFile().')'
                : 'Terjadi kesalahan sistem. Coba lagi.';

            return response()->json(['success' => false, 'message' => $msg], 500);
        }
    }
}