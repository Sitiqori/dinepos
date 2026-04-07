<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class PesananController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['items.product', 'user', 'transaction']);

        // Default: hanya tampilkan pesanan yang BELUM selesai (pending & processing)
        // Kalau filter status dipilih, ikuti filter itu
        $activeStatus = $request->get('status', 'active');

        if ($activeStatus === 'active') {
            $query->whereIn('status', ['pending', 'processing']);
        } elseif ($activeStatus === 'completed') {
            $query->where('status', 'completed');
        } elseif ($activeStatus === 'cancelled') {
            $query->where('status', 'cancelled');
        }
        // 'all' → no filter

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('order_code', 'like', '%'.$request->search.'%')
                  ->orWhere('notes', 'like', '%'.$request->search.'%')
                  ->orWhereHas('items', fn($qi) => $qi->where('product_name', 'like', '%'.$request->search.'%'));
            });
        }

        $orders = $query->latest()->paginate(20)->withQueryString();

        $statusCounts = [
            'active'    => Order::whereIn('status', ['pending', 'processing'])->count(),
            'pending'   => Order::where('status', 'pending')->count(),
            'processing'=> Order::where('status', 'processing')->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
            'all'       => Order::count(),
        ];

        $totalPesanan = Order::whereIn('status', ['pending', 'processing'])->count();

        return view('pesanan.index', compact('orders', 'statusCounts', 'totalPesanan', 'activeStatus'));
    }

    public function show(Order $pesanan)
    {
        $pesanan->load(['items.product', 'user', 'transaction']);
        return response()->json($pesanan);
    }

    public function updateStatus(Request $request, Order $pesanan)
    {
        $request->validate([
            'status' => ['required', 'in:pending,processing,completed,cancelled'],
        ]);

        $oldStatus = $pesanan->status;
        $newStatus = $request->status;

        $pesanan->update(['status' => $newStatus]);

        // If completing order, ensure transaction is marked paid
        if ($newStatus === 'completed' && $pesanan->transaction) {
            $pesanan->transaction->update([
                'payment_status' => 'paid',
                'paid_at'        => $pesanan->transaction->paid_at ?? now(),
            ]);
        }

        $pesanan->load(['items.product', 'user', 'transaction']);

        return response()->json([
            'success'    => true,
            'message'    => 'Status pesanan diperbarui.',
            'order'      => $pesanan,
            'completed'  => $newStatus === 'completed',  // flag for JS to remove from list
        ]);
    }
}
