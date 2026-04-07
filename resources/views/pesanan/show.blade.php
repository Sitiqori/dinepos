@extends('layouts.master')
@section('title', 'Detail Pesanan')
@section('page_title', 'Pesanan')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}" />
<style>
.detail-grid { display:grid; grid-template-columns:1fr 340px; gap:20px; }
@media(max-width:900px){ .detail-grid { grid-template-columns:1fr; } }
.info-row { display:flex; justify-content:space-between; align-items:center; padding:10px 0; border-bottom:1px solid var(--border); font-size:.875rem; }
.info-row:last-child { border-bottom:none; }
.info-label { color:var(--text-muted); font-weight:500; }
.info-value { font-weight:600; text-align:right; }
.total-row { display:flex; justify-content:space-between; padding:10px 0; font-size:.875rem; }
.total-row.final { font-size:1rem; font-weight:700; border-top:2px solid var(--border); margin-top:4px; padding-top:14px; }
</style>
@endpush

@section('content')
<div class="page-header">
  <div>
    <h1>Detail Pesanan</h1>
    <div class="breadcrumb">
      <a href="{{ route('dashboard') }}">Dashboard</a> /
      <a href="{{ route('pesanan.index') }}">Pesanan</a> /
      {{ $pesanan->order_code }}
    </div>
  </div>
  <a href="{{ route('pesanan.index') }}" class="btn btn-outline">
    <i class="ri-arrow-left-line"></i> Kembali
  </a>
</div>

<div class="detail-grid">
  {{-- Left: items --}}
  <div>
    <div class="card" style="padding:20px;margin-bottom:16px;">
      <h3 style="font-size:.95rem;margin-bottom:16px;color:var(--navy);">Item Pesanan</h3>
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Produk</th>
              <th>Harga</th>
              <th>Qty</th>
              <th>Subtotal</th>
            </tr>
          </thead>
          <tbody>
            @foreach($pesanan->items as $item)
            <tr>
              <td style="font-weight:600;">{{ $item->product_name }}</td>
              <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
              <td>{{ $item->qty }}</td>
              <td style="font-weight:700;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div style="margin-top:16px;">
        <div class="total-row">
          <span style="color:var(--text-muted);">Subtotal</span>
          <span>Rp {{ number_format($pesanan->items->sum('subtotal'), 0, ',', '.') }}</span>
        </div>
        <div class="total-row final">
          <span>Total</span>
          <span style="color:var(--navy);">Rp {{ number_format($pesanan->total, 0, ',', '.') }}</span>
        </div>
      </div>
    </div>

    @if($pesanan->notes)
    <div class="card" style="padding:16px 20px;">
      <h3 style="font-size:.875rem;color:var(--text-muted);margin-bottom:8px;">Catatan</h3>
      <p style="font-style:italic;color:var(--text);">{{ $pesanan->notes }}</p>
    </div>
    @endif
  </div>

  {{-- Right: info & actions --}}
  <div>
    <div class="card" style="padding:20px;margin-bottom:16px;">
      <h3 style="font-size:.95rem;margin-bottom:14px;color:var(--navy);">Informasi Pesanan</h3>
      <div class="info-row">
        <span class="info-label">Kode Order</span>
        <span class="info-value" style="font-family:monospace;">{{ $pesanan->order_code }}</span>
      </div>
      <div class="info-row">
        <span class="info-label">Status</span>
        <span class="info-value">{!! $pesanan->status_badge !!}</span>
      </div>
      <div class="info-row">
        <span class="info-label">Metode Bayar</span>
        <span class="info-value">{{ $pesanan->payment_method ? strtoupper($pesanan->payment_method) : '-' }}</span>
      </div>
      <div class="info-row">
        <span class="info-label">Meja</span>
        <span class="info-value">{{ $pesanan->table_number ?? '-' }}</span>
      </div>
      <div class="info-row">
        <span class="info-label">Kasir</span>
        <span class="info-value">{{ $pesanan->user?->name ?? '-' }}</span>
      </div>
      <div class="info-row">
        <span class="info-label">Waktu</span>
        <span class="info-value" style="font-size:.82rem;">{{ $pesanan->created_at->format('d/m/Y H:i') }}</span>
      </div>
    </div>

    {{-- Status update (admin only) --}}
    @if(auth()->user()->isAdmin())
    <div class="card" style="padding:20px;margin-bottom:16px;">
      <h3 style="font-size:.875rem;margin-bottom:12px;color:var(--navy);">Update Status</h3>
      <form action="{{ route('pesanan.status', $pesanan) }}" method="POST">
        @csrf @method('PATCH')
        <div class="form-group">
          <select name="status" class="form-control">
            @foreach(['pending'=>'Pending','processing'=>'Diproses','completed'=>'Selesai','cancelled'=>'Batal'] as $val => $label)
              <option value="{{ $val }}" {{ $pesanan->status === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;">
          <i class="ri-refresh-line"></i> Update Status
        </button>
      </form>
    </div>
    @endif

    {{-- Transaction info --}}
    @if($pesanan->transaction)
    <div class="card" style="padding:20px;background:#ecfdf5;border-color:#a7f3d0;">
      <h3 style="font-size:.875rem;margin-bottom:10px;color:#065f46;">
        <i class="ri-checkbox-circle-line"></i> Pembayaran Lunas
      </h3>
      <div style="font-size:.82rem;color:#065f46;">
        Invoice: <strong>{{ $pesanan->transaction->invoice_code }}</strong><br>
        Dibayar: {{ $pesanan->transaction->paid_at?->format('d/m/Y H:i') ?? '-' }}
        @if($pesanan->transaction->change_amount > 0)
        <br>Kembalian: <strong>Rp {{ number_format($pesanan->transaction->change_amount, 0, ',', '.') }}</strong>
        @endif
      </div>
    </div>
    @endif
  </div>
</div>
@endsection
