<?php

namespace App\Exports;

use App\Models\Transaction;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    protected $startDate;
    protected $endDate;
    protected $selectedMonth;

    public function __construct($startDate, $endDate, $selectedMonth)
    {
        $this->startDate     = $startDate;
        $this->endDate       = $endDate;
        $this->selectedMonth = $selectedMonth;
    }

    public function collection()
    {
        return Transaction::with('order.user')
            ->where('payment_status', 'paid')
            ->whereBetween('paid_at', [$this->startDate, $this->endDate])
            ->latest('paid_at')
            ->get()
            ->map(function ($tx, $i) {
                return [
                    'no'             => $i + 1,
                    'invoice'        => $tx->invoice_code,
                    'tanggal'        => $tx->paid_at->format('d/m/Y H:i'),
                    'kasir'          => $tx->order?->user?->name ?? 'Owner',
                    'metode'         => strtoupper($tx->payment_method ?? '-'),
                    'total'          => $tx->amount,
                    'status'         => 'Lunas',
                ];
            });
    }

    public function headings(): array
    {
        return ['No', 'No Invoice', 'Tanggal', 'Kasir', 'Metode Bayar', 'Total (Rp)', 'Status'];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 28,
            'C' => 20,
            'D' => 18,
            'E' => 15,
            'F' => 18,
            'G' => 12,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '0F1E3C']],
            ],
        ];
    }

    public function title(): string
    {
        return 'Laporan ' . $this->selectedMonth;
    }
}