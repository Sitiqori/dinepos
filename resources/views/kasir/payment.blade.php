@extends('layouts.master')
@section('title', 'Pembayaran')
@section('page_title', 'Kasir')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}" />
<style>
.pay-layout { display:grid; grid-template-columns:1fr 380px; gap:20px; }
@media(max-width:900px){ .pay-layout { grid-template-columns:1fr; } }
.method-tabs { display:grid; grid-template-columns:repeat(3,1fr); gap:8px; margin-bottom:20px; }
.method-tab { display:flex;flex-direction:column;align-items:center;gap:6px;padding:14px;border-radius:var(--radius-sm);border:2px solid var(--border);background:#fff;cursor:pointer;transition:all .2s;font-size:.82rem;font-weight:600;color:var(--text-muted); }
.method-tab i { font-size:1.4rem; }
.method-tab.active { border-color:var(--navy);background:var(--navy);color:#fff; }
.method-tab:hover:not(.active) { border-color:var(--navy);color:var(--navy); }
.cash-input { font-size:1.4rem;font-weight:800;text-align:center;border:2px solid var(--border);border-radius:var(--radius-sm);padding:14px;width:100%;font-family:'Syne',sans-serif; }
.cash-input:focus { border-color:var(--blue);outline:none; }
.change-display { text-align:center;padding:16px;background:var(--bg);border-radius:var(--radius-sm);margin-top:12px; }
.change-label { font-size:.8rem;color:var(--text-muted);font-weight:600;text-transform:uppercase;letter-spacing:.04em; }
.change-amount { font-size:1.6rem;font-weight:800;color:var(--teal);font-family:'Syne',sans-serif;margin-top:4px; }
</style>
@endpush

@section('content')
<div class="page-header">
  <div>
    <h1>Proses Pembayaran</h1>
    <div class="breadcrumb"><a href="#">Home</a> / <a href="{{ route('kasir.index') }}">Kasir</a> / Pembayaran</div>
  </div>
  <a href="{{ route('kasir.index') }}" class="btn btn-outline">
    <i class="ri-arrow-left-line"></i> Kembali
  </a>
</div>

<div class="pay-layout">
  {{-- Order summary --}}
  <div>
    <div class="card" style="padding:20px;margin-bottom:16px;">
      <h3 style="font-size:.95rem;margin-bottom:16px;color:var(--navy);">
        Detail Order: <span style="font-family:monospace;">{{ $order->order_code }}</span>
      </h3>
      <div class="table-wrapper">
        <table>
          <thead>
            <tr><th>Produk</th><th>Harga</th><th>Qty</th><th>Subtotal</th></tr>
          </thead>
          <tbody>
            @foreach($order->items as $item)
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
      <div style="border-top:2px solid var(--border);margin-top:12px;padding-top:12px;display:flex;justify-content:space-between;align-items:center;">
        <span style="font-weight:600;color:var(--text-muted);">TOTAL BAYAR</span>
        <span style="font-size:1.3rem;font-weight:800;color:var(--navy);font-family:'Syne',sans-serif;">
          Rp {{ number_format($order->total, 0, ',', '.') }}
        </span>
      </div>
    </div>

    @if($order->notes)
    <div class="card" style="padding:14px 18px;">
      <span style="font-weight:600;color:var(--text-muted);">Catatan:</span>
      <span style="font-style:italic;"> {{ $order->notes }}</span>
    </div>
    @endif
  </div>

  {{-- Payment panel --}}
  <div class="card" style="padding:20px;">
    <h3 style="font-size:.95rem;margin-bottom:16px;color:var(--navy);">Metode Pembayaran</h3>

    <div class="method-tabs" id="methodTabs">
      <div class="method-tab {{ $order->payment_method === 'cash' ? 'active' : '' }}"
           data-method="cash" onclick="selectMethod('cash')">
        <i class="ri-money-dollar-circle-line"></i> Cash
      </div>
      <div class="method-tab {{ $order->payment_method === 'qris' ? 'active' : '' }}"
           data-method="qris" onclick="selectMethod('qris')">
        <i class="ri-qr-code-line"></i> QRIS
      </div>
      <div class="method-tab {{ $order->payment_method === 'transfer' ? 'active' : '' }}"
           data-method="transfer" onclick="selectMethod('transfer')">
        <i class="ri-bank-line"></i> Transfer
      </div>
    </div>

    {{-- Cash section --}}
    <div id="cashSection" style="{{ $order->payment_method !== 'cash' ? 'display:none' : '' }}">
      <div class="form-group">
        <label class="form-label">Uang yang Diterima</label>
        <input type="number" id="cashGiven" class="cash-input"
          placeholder="0" min="{{ $order->total }}"
          oninput="calcChange()" />
      </div>
      <div class="change-display">
        <div class="change-label">Kembalian</div>
        <div class="change-amount" id="changeDisplay">Rp 0</div>
      </div>
    </div>

    {{-- QRIS section --}}
    <div id="qrisSection" style="{{ $order->payment_method !== 'qris' ? 'display:none' : '' }}">
      <div style="text-align:center;padding:20px;">
        <div style="background:var(--bg);border-radius:var(--radius-md);padding:24px;display:inline-block;">
          <i class="ri-qr-code-line" style="font-size:4rem;color:var(--navy);"></i>
          <div style="font-size:.78rem;color:var(--text-muted);margin-top:8px;">QR Code akan muncul di sini</div>
          <div style="font-size:.72rem;color:var(--text-muted);">(Implementasi QRIS dinamis)</div>
        </div>
      </div>
    </div>

    {{-- Transfer section --}}
    <div id="transferSection" style="{{ $order->payment_method !== 'transfer' ? 'display:none' : '' }}">
      <div style="background:var(--bg);border-radius:var(--radius-sm);padding:16px;text-align:center;">
        <div style="font-size:.8rem;color:var(--text-muted);">No. Rekening</div>
        <div style="font-size:1.2rem;font-weight:800;color:var(--navy);margin:6px 0;">1234-5678-9012</div>
        <div style="font-size:.8rem;color:var(--text-muted);">a/n DINE POS</div>
      </div>
    </div>

    <form action="{{ route('kasir.pay', $order) }}" method="POST" id="payForm">
      @csrf
      <input type="hidden" name="cash_given" id="cashGivenInput" value="0" />
      <button type="submit" class="btn btn-primary" style="width:100%;padding:14px;margin-top:20px;font-size:1rem;">
        <i class="ri-checkbox-circle-line"></i>
        Konfirmasi Pembayaran — Rp {{ number_format($order->total, 0, ',', '.') }}
      </button>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
const orderTotal = {{ $order->total }};

function selectMethod(method) {
  document.querySelectorAll('.method-tab').forEach(t => t.classList.remove('active'));
  document.querySelector(`[data-method="${method}"]`).classList.add('active');
  document.getElementById('cashSection').style.display    = method === 'cash'     ? '' : 'none';
  document.getElementById('qrisSection').style.display    = method === 'qris'     ? '' : 'none';
  document.getElementById('transferSection').style.display = method === 'transfer' ? '' : 'none';
}

function calcChange() {
  const given  = parseInt(document.getElementById('cashGiven').value) || 0;
  const change = Math.max(0, given - orderTotal);
  document.getElementById('changeDisplay').textContent = 'Rp ' + change.toLocaleString('id-ID');
  document.getElementById('cashGivenInput').value = given;
  document.getElementById('changeDisplay').style.color = change >= 0 ? 'var(--teal)' : 'var(--red)';
}
</script>
@endpush
