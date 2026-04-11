<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $selectedMonth = $request->get('month', now()->format('Y-m'));
        [$year, $month] = explode('-', $selectedMonth);

        // ── Stat cards ──────────────────────────────────
        $totalTransaksi = Transaction::whereYear('paid_at', $year)
            ->whereMonth('paid_at', $month)
            ->where('payment_status', 'paid')
            ->count();

        $totalPenjualan = Transaction::whereYear('paid_at', $year)
            ->whereMonth('paid_at', $month)
            ->where('payment_status', 'paid')
            ->sum('amount');

        $labaKotor = round($totalPenjualan * 0.508);

        // ── Delta vs previous month ──────────────────────
        $prev = Carbon::createFromDate($year, $month, 1)->subMonth();

        $prevTx  = Transaction::whereYear('paid_at', $prev->year)
            ->whereMonth('paid_at', $prev->month)->where('payment_status','paid')->count();
        $prevRev = Transaction::whereYear('paid_at', $prev->year)
            ->whereMonth('paid_at', $prev->month)->where('payment_status','paid')->sum('amount');

        $deltaTransaksi = $prevTx  > 0 ? round((($totalTransaksi - $prevTx)  / $prevTx)  * 100, 1) : 0;
        $deltaPenjualan = $prevRev > 0 ? round((($totalPenjualan - $prevRev) / $prevRev) * 100, 1) : 0;
        $deltaLaba      = $deltaPenjualan;

        // ── Chart — last 7 days of selected month ────────
        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
        $today = now();
        $isCurrentMonth = ((int)$year === $today->year && (int)$month === $today->month);
        $endDay   = $isCurrentMonth ? $today->day : $daysInMonth;
        $startDay = max(1, $endDay - 13);
        $chartLabels = $chartPenjualan = $chartTransaksi = $chartLaba = [];

        for ($d = $startDay; $d <= $endDay; $d++) {
            $date = Carbon::createFromDate($year, $month, $d);
            $chartLabels[]    = $d . ' ' . $date->format('M');
            $dayRev           = Transaction::whereDate('paid_at', $date->toDateString())
                ->where('payment_status','paid')->sum('amount');
            $dayTx            = Transaction::whereDate('paid_at', $date->toDateString())
                ->where('payment_status','paid')->count();
            $chartPenjualan[] = (int) $dayRev;
            $chartTransaksi[] = (int) $dayTx;
            $chartLaba[]      = (int) round($dayRev * 0.508);
        }

        // ── Pending orders (pesanan baru) ─────────────────
        $pesananBaru = Order::with(['items.product', 'user'])
            ->whereIn('status', ['pending', 'processing'])
            ->latest()->limit(5)->get();

        // ── Low stock alert ───────────────────────────────
        $hasMinStock = Schema::hasColumn('products', 'min_stock');
        $stokAlerts  = $hasMinStock
            ? Product::whereColumn('stock', '<=', 'min_stock')->orderBy('stock')->limit(5)->get()
            : Product::where('stock', '<=', 5)->orderBy('stock')->limit(5)->get();

        // ── Month selector ────────────────────────────────
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $months[] = ['value' => $m->format('Y-m'), 'label' => $m->format('F Y')];
        }

        return view('dashboard.index', compact(
            'totalTransaksi','totalPenjualan','labaKotor',
            'deltaTransaksi','deltaPenjualan','deltaLaba',
            'chartLabels','chartPenjualan','chartTransaksi','chartLaba',
            'pesananBaru','stokAlerts','months','selectedMonth'
        ));
    }
}
