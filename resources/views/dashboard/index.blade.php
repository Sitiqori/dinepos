@extends('layouts.master')
@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}" />
@endpush

@push('scripts')
<script>
window.DASHBOARD_DATA = {
  labels:    @json($chartLabels),
  transaksi: @json($chartTransaksi),
  penjualan: @json($chartPenjualan),
  laba:      @json($chartLaba),
};
</script>
<script src="{{ asset('js/dashboard.js') }}"></script>
@endpush

@section('content')

{{-- ──────────── STAT CARDS ──────────── --}}
<div class="stat-grid">
  <div class="stat-card">
    <div class="stat-icon blue"><i class="ri-shopping-bag-3-line"></i></div>
    <div class="stat-body">
      <div class="stat-label">Total Transaksi</div>
      <div class="stat-value stat-number" data-val="{{ $totalTransaksi }}">{{ $totalTransaksi }}</div>
      <div class="stat-delta {{ $deltaTransaksi >= 0 ? 'up' : 'down' }}">
        <i class="ri-arrow-{{ $deltaTransaksi >= 0 ? 'up' : 'down' }}-line"></i>
        {{ abs($deltaTransaksi) }}% dari bulan lalu
      </div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon teal"><i class="ri-money-dollar-circle-line"></i></div>
    <div class="stat-body">
      <div class="stat-label">Total Penjualan</div>
      <div class="stat-value green">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</div>
      <div class="stat-delta {{ $deltaPenjualan >= 0 ? 'up' : 'down' }}">
        <i class="ri-arrow-{{ $deltaPenjualan >= 0 ? 'up' : 'down' }}-line"></i>
        {{ abs($deltaPenjualan) }}% dari bulan lalu
      </div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon purple"><i class="ri-funds-line"></i></div>
    <div class="stat-body">
      <div class="stat-label">Laba Kotor</div>
      <div class="stat-value purple">Rp {{ number_format($labaKotor, 0, ',', '.') }}</div>
      <div class="stat-delta {{ $deltaLaba >= 0 ? 'up' : 'down' }}">
        <i class="ri-arrow-{{ $deltaLaba >= 0 ? 'up' : 'down' }}-line"></i>
        {{ abs($deltaLaba) }}% dari bulan lalu
      </div>
    </div>
  </div>
</div>

{{-- ──────────── CHART ──────────── --}}
<div class="chart-card">
  <div class="chart-header">
    <h3 id="chartTitle">Total Penjualan</h3>
    <div class="chart-tabs">
      <button class="chart-tab" data-ds="transaksi" onclick="switchDS(this)">Total Transaksi</button>
      <button class="chart-tab active" data-ds="penjualan" onclick="switchDS(this)">Total Penjualan</button>
      <button class="chart-tab" data-ds="laba" onclick="switchDS(this)">Laba Kotor</button>
    </div>
  </div>

  <div class="chart-filter-row">
    <select class="month-select" id="monthSelect" onchange="changeMonth(this.value)">
      @foreach($months as $m)
        <option value="{{ $m['value'] }}" {{ $m['value'] === $selectedMonth ? 'selected' : '' }}>
          {{ $m['label'] }}
        </option>
      @endforeach
    </select>
    <a href="{{ route('laporan.download') }}?month={{ $selectedMonth }}" class="btn btn-outline btn-sm">
      <i class="ri-download-2-line"></i> Unduh Laporan
    </a>
  </div>

  <div class="chart-body">
    <canvas id="salesChart" style="height:280px;"></canvas>
  </div>
</div>

{{-- ──────────── BOTTOM GRID ──────────── --}}
<div class="bottom-grid">

  {{-- Pesanan Baru --}}
  <div class="section-card">
    <div class="section-header">
      <h3>Pesanan Baru</h3>
      <a href="{{ route('pesanan.index') }}" class="btn btn-outline btn-sm">
        Lihat Semua <i class="ri-arrow-right-line"></i>
      </a>
    </div>
    <div class="section-body">
      @forelse($pesananBaru as $order)
      <div class="order-item">
        <input type="checkbox" class="order-check" />
        <div class="order-body">
          <div class="order-name">{{ $order->items->pluck('product_name')->join(', ') ?: $order->order_code }}</div>
          <div class="order-meta">Qty: {{ $order->items->sum('qty') }}</div>
          @if($order->notes)
            <div class="order-note">Note: {{ $order->notes }}</div>
          @endif
        </div>
        <a href="{{ route('pesanan.index') }}" class="order-edit" title="Lihat">
          <i class="ri-edit-line"></i>
        </a>
      </div>
      @empty
      <div style="text-align:center;padding:24px;color:var(--text-muted);">
        <i class="ri-inbox-line" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
        Tidak ada pesanan menunggu
      </div>
      @endforelse
    </div>
  </div>

  {{-- Alert Stok --}}
  <div class="alert-section section-card">
    <div class="section-header">
      <h3><i class="ri-alarm-warning-line"></i> ALERT!</h3>
      <a href="{{ route('barang.index') }}" class="btn btn-sm" style="color:var(--red);border:1.5px solid #fca5a5;">
        Kelola Stok
      </a>
    </div>
    <div class="section-body">
      @forelse($stokAlerts as $produk)
      <div class="stock-alert-item">
        <input type="checkbox" class="stock-check" />
        <div style="flex:1;min-width:0;">
          <div class="stock-name">{{ $produk->name }}</div>
          <div class="stock-qty">Produk tersisa {{ $produk->stock }}</div>
        </div>
        <a href="{{ route('barang.index') }}" class="stock-edit-btn" title="Edit stok">
          <i class="ri-edit-line"></i>
        </a>
      </div>
      @empty
      <div style="text-align:center;padding:24px;color:var(--text-muted);">
        <i class="ri-checkbox-circle-line" style="font-size:2rem;display:block;margin-bottom:8px;color:var(--green);"></i>
        Semua stok aman!
      </div>
      @endforelse
    </div>
  </div>

</div>

@endsection

@push('scripts')
<script>
function switchDS(btn) {
  document.querySelectorAll('.chart-tab').forEach(t => t.classList.remove('active'));
  btn.classList.add('active');
  const ds = btn.dataset.ds;
  const titles = { transaksi:'Total Transaksi', penjualan:'Total Penjualan', laba:'Laba Kotor' };
  const colors = { transaksi:'#1d4ed8', penjualan:'#0f1e3c', laba:'#7c3aed' };
  document.getElementById('chartTitle').textContent = titles[ds];

  if (window._chart) {
    window._chart.data.datasets[0].data = window.DASHBOARD_DATA[ds];
    window._chart.data.datasets[0].backgroundColor = colors[ds];
    window._chart.update('active');
  }
}

function changeMonth(val) {
  window.location.href = '{{ route("dashboard") }}?month=' + val;
}
</script>
@endpush
