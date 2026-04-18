<?php

namespace App\Http\Controllers;

use App\Models\NotificationRead;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class NotifikasiController extends Controller
{
    // ── EVENTS POLL (global push notification) ───
    public function events(Request $request)
    {
        $since         = (int) $request->get('since', 0);
        $sinceTime     = $since > 0 ? \Carbon\Carbon::createFromTimestamp($since) : now()->subSeconds(15);
        $hasMinStock   = Schema::hasColumn('products', 'min_stock');
        $hasExpiryDate = Schema::hasColumn('products', 'expiry_date');

        // 1. Pesanan baru (created_at >= since)
        $pesananBaru = Order::where('status', 'pending')
            ->where('created_at', '>=', $sinceTime)
            ->get(['id', 'order_code', 'total', 'created_at']);

        // 2. Status update pesanan (updated_at >= since, bukan pending)
        $statusUpdate = Order::whereIn('status', ['processing', 'completed', 'cancelled'])
            ->where('updated_at', '>=', $sinceTime)
            ->get(['id', 'order_code', 'status', 'updated_at']);

        // 3. Bayar berhasil (paid_at >= since)
        $bayarBerhasil = \App\Models\Transaction::where('payment_status', 'paid')
            ->where('paid_at', '>=', $sinceTime)
            ->get(['id', 'invoice_code', 'amount', 'payment_method', 'paid_at']);

        // 4. Stok menipis (hanya kirim sekali — cek sejak kapan pun, tapi pakai session key di JS)
        $stokMenipis = collect();
        if ($hasMinStock) {
            $stokMenipis = Product::whereColumn('stock', '<=', 'min_stock')
                ->where('updated_at', '>=', $sinceTime)
                ->get(['id', 'name', 'stock']);
        }

        // 5. Kadaluarsa mendekat (≤ 7 hari) — kirim kalau belum pernah muncul hari ini
        $kadaluarsa = collect();
        $sudahKadaluarsa = collect();
        if ($hasExpiryDate) {
            $today = \Carbon\Carbon::today();
            $kadaluarsa = Product::whereNotNull('expiry_date')
                ->whereDate('expiry_date', '>=', $today)
                ->whereDate('expiry_date', '<=', \Carbon\Carbon::today()->addDays(7))
                ->get(['id', 'name', 'expiry_date'])
                ->map(function ($p) use ($today) {
                    $p->sisa_hari = $today->diffInDays($p->expiry_date);
                    return $p;
                });

            $sudahKadaluarsa = Product::whereNotNull('expiry_date')
                ->whereDate('expiry_date', '<', $today)
                ->where('updated_at', '>=', $sinceTime)
                ->get(['id', 'name', 'expiry_date']);
        }

        return response()->json([
            'now'              => now()->timestamp,
            'pesanan_baru'     => $pesananBaru,
            'status_update'    => $statusUpdate,
            'bayar_berhasil'   => $bayarBerhasil,
            'stok_menipis'     => $stokMenipis,
            'kadaluarsa'       => $kadaluarsa,
            'sudah_kadaluarsa' => $sudahKadaluarsa,
        ]);
    }

    // ── INDEX ─────────────────────────────────────
    public function index()
    {
        $userId        = Auth::id();
        $hasMinStock   = Schema::hasColumn('products', 'min_stock');
        $hasExpiryDate = Schema::hasColumn('products', 'expiry_date');

        $reads = NotificationRead::where('user_id', $userId)
            ->get()->groupBy('type')
            ->map(fn($g) => $g->pluck('reference_id')->flip());

        $readStok    = $reads->get('stok',    collect());
        $readExpiry  = $reads->get('expiry',  collect());
        $readExpired = $reads->get('expired', collect());
        $readPesanan = $reads->get('pesanan', collect());

        // 1. Stok menipis
        $stokMenipis = ($hasMinStock
            ? Product::with('category')->whereColumn('stock', '<=', 'min_stock')->orderBy('stock')->get()
            : Product::with('category')->where('stock', '<=', 5)->orderBy('stock')->get()
        )->map(fn($p) => tap($p, fn($p) => $p->is_read = $readStok->has($p->id)));

        // 2. Kadaluarsa
        $kadaluarsaMendekat = collect();
        $sudahKadaluarsa    = collect();

        if ($hasExpiryDate) {
            $today = Carbon::today();
            $limit = Carbon::today()->addDays(30);

            $kadaluarsaMendekat = Product::with('category')
                ->whereNotNull('expiry_date')
                ->whereDate('expiry_date', '>=', $today)
                ->whereDate('expiry_date', '<=', $limit)
                ->orderBy('expiry_date')->get()
                ->map(function ($p) use ($today, $readExpiry) {
                    $p->sisa_hari = $today->diffInDays($p->expiry_date);
                    $p->is_read   = $readExpiry->has($p->id);
                    return $p;
                });

            $sudahKadaluarsa = Product::with('category')
                ->whereNotNull('expiry_date')
                ->whereDate('expiry_date', '<', $today)
                ->orderBy('expiry_date', 'desc')->get()
                ->map(fn($p) => tap($p, fn($p) => $p->is_read = $readExpired->has($p->id)));
        }

        // 3. Pesanan
        $pesananBaru = Order::with(['items.product', 'user'])
            ->where('status', 'pending')->latest()->get()
            ->map(fn($o) => tap($o, fn($o) => $o->is_read = $readPesanan->has($o->id)));

        $pesananDiproses = Order::with(['items.product', 'user'])
            ->where('status', 'processing')->latest()->get()
            ->map(fn($o) => tap($o, fn($o) => $o->is_read = $readPesanan->has($o->id)));

        $totalNotif = $stokMenipis->where('is_read', false)->count()
            + $kadaluarsaMendekat->where('is_read', false)->count()
            + $sudahKadaluarsa->where('is_read', false)->count()
            + $pesananBaru->where('is_read', false)->count();

        return view('notifikasi.index', compact(
            'stokMenipis', 'kadaluarsaMendekat', 'sudahKadaluarsa',
            'pesananBaru', 'pesananDiproses',
            'totalNotif', 'hasExpiryDate'
        ));
    }

    // ── MARK ONE AS READ ─────────────────────────
    public function markRead(Request $request)
    {
        $request->validate([
            'type'         => ['required', 'in:stok,expiry,expired,pesanan'],
            'reference_id' => ['required', 'integer'],
        ]);

        NotificationRead::firstOrCreate([
            'user_id'      => Auth::id(),
            'type'         => $request->type,
            'reference_id' => $request->reference_id,
        ], ['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    // ── MARK ALL AS READ ─────────────────────────
    public function markAllRead()
    {
        $userId        = Auth::id();
        $hasMinStock   = Schema::hasColumn('products', 'min_stock');
        $hasExpiryDate = Schema::hasColumn('products', 'expiry_date');

        // Hapus records lama yang pesanannya sudah tidak pending
        $activePendingIds = Order::where('status', 'pending')->pluck('id');
        NotificationRead::where('user_id', $userId)
            ->where('type', 'pesanan')
            ->whereNotIn('reference_id', $activePendingIds)
            ->delete();

        $stokIds = $hasMinStock
            ? Product::whereColumn('stock', '<=', 'min_stock')->pluck('id')
            : Product::where('stock', '<=', 5)->pluck('id');
        foreach ($stokIds as $id) {
            NotificationRead::firstOrCreate(
                ['user_id' => $userId, 'type' => 'stok', 'reference_id' => $id],
                ['read_at' => now()]
            );
        }

        if ($hasExpiryDate) {
            $today = Carbon::today();
            foreach (Product::whereNotNull('expiry_date')->whereDate('expiry_date', '>=', $today)->whereDate('expiry_date', '<=', Carbon::today()->addDays(30))->pluck('id') as $id) {
                NotificationRead::firstOrCreate(['user_id' => $userId, 'type' => 'expiry', 'reference_id' => $id], ['read_at' => now()]);
            }
            foreach (Product::whereNotNull('expiry_date')->whereDate('expiry_date', '<', $today)->pluck('id') as $id) {
                NotificationRead::firstOrCreate(['user_id' => $userId, 'type' => 'expired', 'reference_id' => $id], ['read_at' => now()]);
            }
        }

        foreach ($activePendingIds as $id) {
            NotificationRead::firstOrCreate(['user_id' => $userId, 'type' => 'pesanan', 'reference_id' => $id], ['read_at' => now()]);
        }

        return response()->json(['success' => true]);
    }

    // ── COUNT (navbar badge) ─────────────────────
    public function count()
    {
        $userId        = Auth::id();
        $hasMinStock   = Schema::hasColumn('products', 'min_stock');
        $hasExpiryDate = Schema::hasColumn('products', 'expiry_date');

        $reads = NotificationRead::where('user_id', $userId)->get()
            ->groupBy('type')->map(fn($g) => $g->pluck('reference_id')->flip());

        $readStok    = $reads->get('stok',    collect());
        $readExpiry  = $reads->get('expiry',  collect());
        $readExpired = $reads->get('expired', collect());
        $readPesanan = $reads->get('pesanan', collect());

        $stokIds = $hasMinStock
            ? Product::whereColumn('stock', '<=', 'min_stock')->pluck('id')
            : Product::where('stock', '<=', 5)->pluck('id');
        $stok = $stokIds->filter(fn($id) => !$readStok->has($id))->count();

        $expiring = 0; $expired = 0;
        if ($hasExpiryDate) {
            $today = Carbon::today();
            $expiring = Product::whereNotNull('expiry_date')->whereDate('expiry_date', '>=', $today)->whereDate('expiry_date', '<=', Carbon::today()->addDays(30))->pluck('id')->filter(fn($id) => !$readExpiry->has($id))->count();
            $expired  = Product::whereNotNull('expiry_date')->whereDate('expiry_date', '<', $today)->pluck('id')->filter(fn($id) => !$readExpired->has($id))->count();
        }

        $pending = Order::where('status', 'pending')->pluck('id')->filter(fn($id) => !$readPesanan->has($id))->count();
        $total   = $stok + $expiring + $expired + $pending;

        return response()->json(compact('total', 'stok', 'expiring', 'expired', 'pending'));
    }
}