@extends('layouts.master')
@section('title', 'Detail Transaksi')
@section('page_title', 'Riwayat Transaksi')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}" />
<style>
.receipt {
  background:#fff; border:1px solid var(--border);
  border-radius:var(--radius-md); padding:28px;
  max-width:480px; margin:0 auto;
}
.receipt-header { text-align:center; padding-bottom:20px; border-bottom:2px dashed var(--border); margin-bottom:20px; }
.receipt-logo { font-family:'Syne',sans-serif; font-size:1.4rem; font-weight:800; color:var(--navy); }
.receipt-sub { font-size:.8rem; color:var(--text-muted); margin-top:4px; }
.receipt-row { display:flex; justify-content:space-between; padding:6px 0; font-size:.875rem; }
.receipt-row .label { color:var(--text-muted); }
.receipt-row .value { font-weight:600; }
.receipt-divider { border:none; border-top:1px dashed var(--border); margin:12px 0; }
.receipt-total { font-size:1.1rem; font-weight:800; color:var(--navy); }
.receipt-footer { text-align:center; margin-top:20px; padding-top:16px; border-top:2px dashed var(--border); font-size:.78rem; color:var(--text-muted); }
</style>
@endpush

@section('content')
<div class="page-header">
  <div>
    <h1>Detail Transaksi</h1>
    <div class="breadcrumb">
      <a href="{{ route('dashboard') }}">Dashboard</a> /
      <a href="{{ route('transaksi.index') }}">Transaksi</a> /
      {{ $transaksi->invoice_code }}
    </div>
  </div>
  <div style="display:flex;gap:8px;">
    <button onclick="window.print()" class="btn btn-outline">
      <i class="ri-printer-line"></i> Cetak
    </button>
    <a href="{{ route('transaksi.index') }}" class="btn btn-outline">
      <i class="ri-arrow-left-line"></i> Kembali
    </a>
  </div>
</div>

<div class="receipt">
  <div class="receipt-header">
    <div class="receipt-logo">🍽 DINE POS</div>
    <div class="receipt-sub">Struk Pembayaran</div>
    <div style="margin-top:12px;">
      <span class="badge badge-green">
        <i class="ri-checkbox-circle-line"></i> LUNAS
      </span>
    </div>
  </div>

  <div class="receipt-row">
    <span class="label">Invoice</span>
    <span class="value" style="font-family:monospace;font-size:.8rem;">{{ $transaksi->invoice_code }}</span>
  </div>
  <div class="receipt-row">
    <span class="label">Order</span>
    <span class="value" style="font-family:monospace;font-size:.8rem;">{{ $transaksi->order?->order_code }}</span>
  </div>
  <div class="receipt-row">
    <span class="label">Kasir</span>
    <span class="value">{{ $transaksi->order?->user?->name ?? '-' }}</span>
  </div>
  <div class="receipt-row">
    <span class="label">Waktu</span>
    <span class="value">{{ $transaksi->paid_at?->format('d/m/Y H:i') ?? '-' }}</span>
  </div>
  @if($transaksi->order?->table_number)
  <div class="receipt-row">
    <span class="label">Meja</span>
    <span class="value">{{ $transaksi->order->table_number }}</span>
  </div>
  @endif

  <hr class="receipt-divider" />

  {{-- Items --}}
  @foreach($transaksi->order?->items ?? [] as $item)
  <div class="receipt-row">
    <span class="label">{{ $item->product_name }} x{{ $item->qty }}</span>
    <span class="value">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
  </div>
  @endforeach

  <hr class="receipt-divider" />

  <div class="receipt-row receipt-total">
    <span>Total</span>
    <span>Rp {{ number_format($transaksi->amount, 0, ',', '.') }}</span>
  </div>
  <div class="receipt-row">
    <span class="label">Metode Bayar</span>
    <span class="value">{{ strtoupper($transaksi->payment_method) }}</span>
  </div>
  @if($transaksi->change_amount > 0)
  <div class="receipt-row">
    <span class="label">Kembalian</span>
    <span class="value" style="color:var(--teal);">
      Rp {{ number_format($transaksi->change_amount, 0, ',', '.') }}
    </span>
  </div>
  @endif

  <div class="receipt-footer">
    Terima kasih telah berkunjung!<br />
    Powered by <strong>DINE POS</strong> &copy; {{ date('Y') }}
  </div>
</div>
@endsection
