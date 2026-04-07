<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['order.items.product', 'order.user']);

        // Filter by payment status
        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }

        // Filter by payment method — support both 'tunai' and 'cash' labels
        if ($request->filled('method')) {
            $method = $request->method;
            if ($method === 'tunai' || $method === 'cash') {
                $query->whereIn('payment_method', ['tunai', 'cash']);
            } else {
                $query->where('payment_method', $method);
            }
        }

        // Filter by date — use paid_at (actual payment time) not created_at
        if ($request->filled('date_from')) {
            $query->whereDate('paid_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('paid_at', '<=', $request->date_to);
        }

        // Search by invoice code or order code
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('invoice_code', 'like', '%'.$s.'%')
                  ->orWhereHas('order', fn($oq) => $oq->where('order_code', 'like', '%'.$s.'%'));
            });
        }

        // Order by paid_at DESC (terbaru dulu), fallback to created_at
        $transactions = $query
            ->orderByRaw('COALESCE(paid_at, created_at) DESC')
            ->paginate(15)
            ->withQueryString();

        // Summary stats
        $totalPaid    = Transaction::where('payment_status', 'paid')->sum('amount');
        $todayPaid    = Transaction::where('payment_status', 'paid')
                            ->whereDate('paid_at', today())->sum('amount');
        $totalCount   = Transaction::where('payment_status', 'paid')->count();

        // Pre-process transaction data for JS (avoid Blade parser conflict with closures)
        $txsForJs = $transactions->keyBy('id')->map(function ($tx) {
            return [
                'id'             => $tx->id,
                'invoice_code'   => $tx->invoice_code,
                'order_code'     => $tx->order?->order_code,
                'amount'         => $tx->amount,
                'payment_method' => $tx->payment_method,
                'payment_status' => $tx->payment_status,
                'change_amount'  => $tx->change_amount,
                'paid_at'        => $tx->paid_at
                                      ? $tx->paid_at->format('d/m/Y H:i')
                                      : $tx->created_at->format('d/m/Y H:i'),
                'kasir'          => $tx->order?->user?->name ?? 'Admin',
                'customer_name'  => $tx->order?->customer_name ?? null,
                'notes'          => $tx->order?->notes ?? null,
                'items'          => $tx->order?->items?->map(fn($i) => [
                    'name'     => $i->product_name,
                    'price'    => $i->price,
                    'qty'      => $i->qty,
                    'subtotal' => $i->subtotal,
                ])->values() ?? [],
            ];
        })->values()->keyBy('id');

        return view('transaksi.index', compact(
            'transactions', 'totalPaid', 'todayPaid', 'totalCount', 'txsForJs'
        ));
    }

    public function show(Transaction $transaksi)
    {
        $transaksi->load(['order.items.product', 'order.user']);
        return view('transaksi.show', compact('transaksi'));
    }
}
