<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\Transaction;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        // ── Period / tab ─────────────────────────────────
        $activeTab = $request->get('tab', 'pendapatan'); // pendapatan | pengeluaran | perbandingan

        // ── Month filter ─────────────────────────────────
        $selectedMonth = $request->get('month', now()->format('Y-m'));
        [$year, $month] = explode('-', $selectedMonth);

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate   = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        // ── Stat cards ────────────────────────────────────
        $pesananDiproses = Order::whereIn('status', ['pending','processing'])->count();

        $penjualan1Bulan = Transaction::where('payment_status','paid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->count();

        $kasirAktif = User::where('role','kasir')
            ->when(Schema::hasColumn('users','is_active'), fn($q) => $q->where('is_active', true))
            ->count();

        // ── Chart: daily revenue for selected month ───────
        $daysInMonth  = $startDate->daysInMonth;
        $today = now();
        $isCurrentMonth = ((int)$year === $today->year && (int)$month === $today->month);
        $endDay = $isCurrentMonth ? $today->day : $daysInMonth;
        $chartLabels  = $chartRevenue = [];
        for ($d = 1; $d <= $endDay; $d++) {
            $date = Carbon::createFromDate($year, $month, $d);
            $chartLabels[]  = $d . ' ' . $date->format('M');
            $chartRevenue[] = (int) Transaction::where('payment_status','paid')
                ->whereDate('paid_at', $date->toDateString())
                ->sum('amount');
        }

        // ── Top products this month ────────────────────────
        $topProducts = OrderItem::selectRaw('product_name, SUM(qty) as total_qty, SUM(subtotal) as total_revenue')
            ->whereHas('order', fn($q) => $q
                ->where('status','completed')
                ->whereBetween('created_at', [$startDate, $endDate])
            )
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->limit(3)
            ->get();

        // ── Pengeluaran table (mock with order items for now) ─
        // In a real app you'd have an 'expenses' table.
        // We show completed transactions as pengeluaran placeholder
        $pengeluaran = Transaction::with(['order.user'])
            ->where('payment_status','paid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->latest('paid_at')
            ->limit(7)
            ->get();

        $totalPengeluaran = $pengeluaran->sum('amount');

        // ── Month selector ────────────────────────────────
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $months[] = ['value' => $m->format('Y-m'), 'label' => $m->format('M Y')];
        }

        // ── Pre-process chart data (avoid Blade closure issue) ─
        $chartDataForJs = [
            'labels'  => $chartLabels,
            'revenue' => $chartRevenue,
        ];

        return view('laporan.index', compact(
            'activeTab','selectedMonth','months',
            'pesananDiproses','penjualan1Bulan','kasirAktif',
            'chartDataForJs',
            'topProducts',
            'pengeluaran','totalPengeluaran',
            'startDate','endDate'
        ));
    }

    public function download(Request $request)
    {
        $selectedMonth = $request->get('month', now()->format('Y-m'));
        [$year, $month] = explode('-', $selectedMonth);
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate   = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $filename = 'laporan-' . $selectedMonth . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\LaporanExport($startDate, $endDate, $selectedMonth),
            $filename
        );
    }
}
