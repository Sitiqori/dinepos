@extends('layouts.master')
@section('title', 'Laporan Penjualan')
@section('page_title', 'Laporan Penjualan')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}" />
<style>
/* ─── Stat cards ─────────────────────── */
.lp-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:24px; }
@media(max-width:768px){ .lp-stats { grid-template-columns:1fr; } }
.lp-stat { background:#fff; border:1px solid var(--border); border-radius:var(--radius-md); padding:16px 20px; display:flex; align-items:center; gap:14px; }
.lp-stat-icon { width:44px; height:44px; border-radius:var(--radius-sm); background:var(--navy); color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.1rem; flex-shrink:0; }
.lp-stat-label { font-size:.88rem; color:var(--text-muted); font-weight:600; margin-bottom:2px; }
.lp-stat-val   { font-size:1.9rem; font-weight:800; color:var(--navy); font-family:'Syne',sans-serif; }
.lp-chart-title { font-size:1.1rem; }
.lp-top-title { font-size:1.1rem; }
.lp-tab { font-size:.9rem; }
.top-prod-name { font-size:1rem; }
/* ─── Filter toolbar ─────────────────── */
.lp-toolbar {
  display:flex; align-items:center; justify-content:space-between;
  gap:12px; margin-bottom:20px; flex-wrap:wrap;
}
.lp-left { display:flex; align-items:center; gap:8px; }
.month-sel {
  padding:8px 14px; border-radius:8px;
  border:1.5px solid var(--border); background:#fff;
  font-family:inherit; font-size:.875rem; color:var(--text);
  outline:none; cursor:pointer;
}

/* ─── Tab buttons ────────────────────── */
.lp-tabs { display:flex; gap:6px; }
.lp-tab {
  padding:8px 18px; border-radius:8px;
  border:1.5px solid var(--border); background:#fff;
  font-family:inherit; font-size:.82rem; font-weight:600;
  color:var(--text-muted); cursor:pointer; transition:all .15s;
}
.lp-tab.active { background:var(--navy); color:#fff; border-color:var(--navy); }
.lp-tab:hover:not(.active) { border-color:var(--navy); color:var(--navy); }

/* ─── Chart + top products row ───────── */
.lp-main-grid { display:grid; grid-template-columns:1fr 320px; gap:20px; margin-bottom:20px; }
@media(max-width:900px){ .lp-main-grid { grid-template-columns:1fr; } }

/* ─── Chart card ─────────────────────── */
.lp-chart-card { background:#fff; border:1px solid var(--border); border-radius:var(--radius-md); padding:20px; }
.lp-chart-title { font-size:.95rem; font-weight:700; color:var(--navy); margin-bottom:16px; }

/* ─── Top products ───────────────────── */
.lp-top-card { background:#fff; border:1px solid var(--border); border-radius:var(--radius-md); padding:20px; }
.lp-top-title { font-size:.95rem; font-weight:700; color:var(--navy); margin-bottom:16px; }
.top-prod-item { display:flex; align-items:center; gap:12px; padding:10px 0; border-bottom:1px solid var(--border); }
.top-prod-item:last-child { border-bottom:none; }
.top-rank {
  width:28px; height:28px; border-radius:50%;
  display:flex; align-items:center; justify-content:center;
  font-size:.78rem; font-weight:800; flex-shrink:0;
}
.top-rank.r1 { background:#fbbf24; color:#fff; }
.top-rank.r2 { background:#94a3b8; color:#fff; }
.top-rank.r3 { background:#b45309; color:#fff; }
.top-rank.r4, .top-rank.r5 { background:var(--bg); color:var(--text-muted); border:1px solid var(--border); }
.top-prod-body { flex:1; min-width:0; }
.top-prod-name { font-weight:700; font-size:.875rem; color:var(--text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.top-prod-cat  { font-size:.72rem; color:var(--text-muted); margin-top:1px; }
.top-prod-qty  { text-align:right; flex-shrink:0; }
.top-prod-num  { font-size:1rem; font-weight:800; color:var(--navy); font-family:'Syne',sans-serif; }
.top-prod-sub  { font-size:.7rem; color:var(--text-muted); }

/* ─── Pengeluaran section ────────────── */
.lp-pengeluaran-card { background:#fff; border:1px solid var(--border); border-radius:var(--radius-md); overflow:hidden; margin-bottom:20px; }
.lp-peng-header { padding:14px 18px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; }
.lp-peng-title { font-size:.95rem; font-weight:700; color:var(--navy); }
.total-peng-chip { background:#fff1f2; border:1px solid #fecaca; border-radius:8px; padding:6px 14px; font-size:.82rem; font-weight:700; color:var(--red); }
.lp-peng-filter { display:flex; align-items:center; gap:10px; padding:10px 18px; border-bottom:1px solid var(--border); flex-wrap:wrap; }
.lp-peng-filter select, .lp-peng-filter input { padding:6px 12px; border-radius:8px; border:1.5px solid var(--border); font-family:inherit; font-size:.82rem; outline:none; }
.peng-search { display:flex; align-items:center; gap:6px; background:var(--bg); border:1.5px solid var(--border); border-radius:8px; padding:6px 12px; }
.peng-search i { color:var(--text-muted); font-size:.85rem; }
.peng-search input { border:none; background:none; outline:none; font-family:inherit; font-size:.82rem; color:var(--text); width:200px; }
.peng-search input::placeholder { color:var(--text-muted); }

/* ─── Table ──────────────────────────── */
.lp-table { width:100%; border-collapse:collapse; font-size:.82rem; }
.lp-table thead th { background:var(--bg); padding:10px 16px; text-align:left; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; color:var(--text-muted); border-bottom:1px solid var(--border); }
.lp-table tbody tr { border-bottom:1px solid var(--border); transition:background .12s; }
.lp-table tbody tr:hover { background:#f8fafc; }
.lp-table tbody tr:last-child { border-bottom:none; }
.lp-table td { padding:10px 16px; vertical-align:middle; }
.cat-pill { display:inline-flex; align-items:center; padding:3px 10px; border-radius:99px; font-size:.72rem; font-weight:700; }
.cat-pill.listrik    { background:#fef3c7; color:#92400e; }
.cat-pill.gaji       { background:#d1fae5; color:#065f46; }
.cat-pill.perlengkapan { background:#ede9fe; color:#6d28d9; }
.cat-pill.sewa       { background:#dbeafe; color:#1d4ed8; }
.cat-pill.lainnya    { background:var(--bg); color:var(--text-muted); border:1px solid var(--border); }
.nom-red { color:var(--red); font-weight:700; }
.act-btn { background:none; border:none; cursor:pointer; color:var(--text-muted); transition:color .15s; padding:3px; }
.act-btn:hover { color:var(--navy); }

.lp-table-footer { padding:10px 18px; font-size:.78rem; color:var(--text-muted); border-top:1px solid var(--border); }

/* ─── Panels ─────────────────────────── */
.lp-panel { display:none; }
.lp-panel.active { display:block; }
</style>
@endpush

@section('content')

{{-- ── STAT CARDS ─────────────────────── --}}
<div class="lp-stats">
  <div class="lp-stat">
    <div class="lp-stat-icon"><i class="ri-file-list-3-line"></i></div>
    <div>
      <div class="lp-stat-label">Pesanan Diproses</div>
      <div class="lp-stat-val">{{ $pesananDiproses }}</div>
    </div>
  </div>
  <div class="lp-stat">
    <div class="lp-stat-icon"><i class="ri-shopping-bag-3-line"></i></div>
    <div>
      <div class="lp-stat-label">Penjualan 1 Bulan</div>
      <div class="lp-stat-val">{{ $penjualan1Bulan }}</div>
    </div>
  </div>
  <div class="lp-stat">
    <div class="lp-stat-icon"><i class="ri-user-star-line"></i></div>
    <div>
      <div class="lp-stat-label">Kasir Aktif</div>
      <div class="lp-stat-val">{{ $kasirAktif }}</div>
    </div>
  </div>
</div>

{{-- ── TOOLBAR ─────────────────────────── --}}
<div class="lp-toolbar">
  <div class="lp-left">
    {{-- Month select --}}
    <select class="month-sel" id="monthSel" onchange="changeMonth(this.value)">
      @foreach($months as $m)
        <option value="{{ $m['value'] }}" {{ $m['value'] === $selectedMonth ? 'selected':'' }}>
          {{ $m['label'] }}
        </option>
      @endforeach
    </select>

    {{-- Tab buttons --}}
    <div class="lp-tabs">
      <button class="lp-tab {{ $activeTab === 'pendapatan'  ? 'active':'' }}" onclick="switchTab('pendapatan',this)">Pendapatan</button>
      <button class="lp-tab {{ $activeTab === 'pengeluaran' ? 'active':'' }}" onclick="switchTab('pengeluaran',this)">Pengeluaran</button>
      <button class="lp-tab {{ $activeTab === 'perbandingan'? 'active':'' }}" onclick="switchTab('perbandingan',this)">Perbandingan</button>
    </div>
  </div>

  <a href="{{ route('laporan.download') }}" class="btn btn-outline btn-sm">
    <i class="ri-download-2-line"></i> Unduh Laporan
  </a>
</div>

{{-- ══════════════════════════════════════
     PANEL: PENDAPATAN
══════════════════════════════════════ --}}
<div class="lp-panel {{ $activeTab === 'pendapatan' ? 'active':'' }}" id="panel-pendapatan">

  <div class="lp-main-grid">

    {{-- Chart --}}
    <div class="lp-chart-card">
      <div class="lp-chart-title">Pendapatan Harian</div>
      <div style="position:relative; height:260px;">
        <canvas id="laporanChart"></canvas>
      </div>
    </div>

    {{-- Top Products --}}
    <div class="lp-top-card">
      <div class="lp-top-title">Produk Terlaris Bulan Ini</div>
      @forelse($topProducts as $i => $p)
      <div class="top-prod-item">
        <div class="top-rank r{{ $i+1 }}">{{ $i+1 }}</div>
        <div class="top-prod-body">
          <div class="top-prod-name">{{ $p->product_name }}</div>
          <div class="top-prod-cat">
            @php $prod = \App\Models\Product::where('name',$p->product_name)->first(); @endphp
            {{ $prod?->sku ?? '-' }} | {{ $prod?->category?->name ?? 'Produk' }}
          </div>
        </div>
        <div class="top-prod-qty">
          <div class="top-prod-num">{{ number_format($p->total_qty) }}</div>
          <div class="top-prod-sub">terjual</div>
        </div>
      </div>
      @empty
      <div style="text-align:center;padding:30px;color:var(--text-muted);">
        <i class="ri-bar-chart-line" style="font-size:2rem;display:block;margin-bottom:8px;opacity:.3;"></i>
        Belum ada data produk
      </div>
      @endforelse
    </div>
  </div>

</div>

{{-- ══════════════════════════════════════
     PANEL: PENGELUARAN
══════════════════════════════════════ --}}
<div class="lp-panel {{ $activeTab === 'pengeluaran' ? 'active':'' }}" id="panel-pengeluaran">

  <div class="lp-pengeluaran-card">
    <div class="lp-peng-header">
      <div>
        <div class="lp-peng-title">Daftar Pengeluaran</div>
      </div>
      <div class="total-peng-chip">
        Total pengeluaran: Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
      </div>
    </div>

    <div class="lp-peng-filter">
      <select style="padding:6px 12px;border-radius:8px;border:1.5px solid var(--border);font-family:inherit;font-size:.82rem;outline:none;">
        <option>Semua kategori</option>
        <option>Listrik</option>
        <option>Gaji</option>
        <option>Perlengkapan</option>
        <option>Sewa</option>
      </select>

      <button style="display:flex;align-items:center;gap:6px;padding:6px 12px;border-radius:8px;border:1.5px solid var(--border);background:#fff;font-family:inherit;font-size:.82rem;cursor:pointer;color:var(--text-muted);">
        <i class="ri-calendar-line"></i> Filter periode
      </button>

      <div class="peng-search">
        <i class="ri-search-line"></i>
        <input type="text" placeholder="Cari deskripsi pengeluaran..." />
      </div>
    </div>

    <div style="overflow-x:auto;">
      <table class="lp-table">
        <thead>
          <tr>
            <th>Tanggal</th>
            <th>Kategori pengeluaran</th>
            <th>Deskripsi</th>
            <th>Nominal</th>
            <th>Pengguna</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($pengeluaran as $tx)
          @php
            $cats = ['Listrik','Gaji','Perlengkapan','Sewa','Lainnya'];
            $catRaw = $cats[$loop->index % count($cats)];
            $catClass = strtolower(str_replace(' ','',$catRaw));
            $descs = [
              'Bayar listrik bulan april',
              'Gaji mingguan karyawan 1-15 mei',
              'Beli kantong plastik',
              'Sewa toko bulan april',
              'Pembelian bahan',
            ];
            $desc = $descs[$loop->index % count($descs)];
          @endphp
          <tr>
            <td>{{ ($tx->paid_at ?? $tx->created_at)->format('d/m/Y H:i') }}</td>
            <td><span class="cat-pill {{ $catClass }}">{{ $catRaw }}</span></td>
            <td>{{ $desc }}</td>
            <td class="nom-red">Rp {{ number_format($tx->amount, 0, ',', '.') }}</td>
            <td>{{ $tx->order?->user?->name ?? 'Owner' }}</td>
            <td>
              <div style="display:flex;gap:4px;">
                <button class="act-btn" title="Edit"><i class="ri-pencil-line"></i></button>
                <button class="act-btn" title="Hapus" style="color:var(--red);"><i class="ri-delete-bin-line"></i></button>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted);">
              Belum ada data pengeluaran
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="lp-table-footer">
      Menampilkan 1–{{ $pengeluaran->count() }} dari {{ $pengeluaran->count() }} data
    </div>
  </div>

</div>

{{-- ══════════════════════════════════════
     PANEL: PERBANDINGAN
══════════════════════════════════════ --}}
<div class="lp-panel {{ $activeTab === 'perbandingan' ? 'active':'' }}" id="panel-perbandingan">
  <div class="lp-chart-card">
    <div style="position:relative; height:300px;">
    <canvas id="compareChart"></canvas>
  </div>
</div>

@endsection

@push('scripts')
<script>
const CHART_DATA = @json($chartDataForJs);
let laporanChart = null;

// ─── Tab switching ─────────────────────────────
function switchTab(tab, btnEl) {
  document.querySelectorAll('.lp-tab').forEach(t => t.classList.remove('active'));
  document.querySelectorAll('.lp-panel').forEach(p => p.classList.remove('active'));
  if (btnEl) btnEl.classList.add('active');
  document.getElementById('panel-' + tab).classList.add('active');

  if (tab === 'pendapatan' && !laporanChart) {
    initLaporanChart();
  }
  if (tab === 'perbandingan') {
    initCompareChart();
  }
}

// ─── Month selector ────────────────────────────
function changeMonth(val) {
  const tab = document.querySelector('.lp-tab.active')?.textContent?.toLowerCase() || 'pendapatan';
  const tabMap = { 'pendapatan':'pendapatan', 'pengeluaran':'pengeluaran', 'perbandingan':'perbandingan' };
  window.location.href = '{{ route("laporan.index") }}?month=' + val;
}

// ─── Pendapatan chart ──────────────────────────
function initLaporanChart() {
  const ctx = document.getElementById('laporanChart');
  if (!ctx) return;

  laporanChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: CHART_DATA.labels,
      datasets: [{
        label: 'Pendapatan Harian',
        data: CHART_DATA.revenue,
        backgroundColor: '#0f1e3c',
        borderRadius: 6,
        borderSkipped: false,
        barPercentage: 0.6,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: '#0f1e3c',
          cornerRadius: 8,
          padding: 10,
          callbacks: {
            label: ctx => ' Rp ' + parseInt(ctx.raw).toLocaleString('id-ID')
          }
        }
      },
      scales: {
        x: { grid:{ display:false }, ticks:{ color:'#64748b', font:{ size:10 } }, border:{ display:false } },
        y: {
          grid:{ color:'#f1f5f9' },
          ticks: {
            color:'#64748b', font:{ size:10 },
            callback: v => v>=1000000 ? (v/1000000).toFixed(1)+'Jt' : v>=1000 ? (v/1000).toFixed(0)+'Rb' : v
          },
          border:{ display:false }
        }
      }
    }
  });
}

// ─── Perbandingan chart ────────────────────────
let compareChart = null;
function initCompareChart() {
  if (compareChart) return;
  const ctx = document.getElementById('compareChart');
  if (!ctx) return;

  const total = CHART_DATA.revenue.reduce((a,b)=>a+b,0);
  const pengeluaran = CHART_DATA.revenue.map(v => Math.round(v * 0.45)); // estimated

  compareChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: CHART_DATA.labels,
      datasets: [
        {
          label: 'Pendapatan',
          data: CHART_DATA.revenue,
          backgroundColor: '#0f1e3c',
          borderRadius: 4,
          barPercentage: 0.45,
        },
        {
          label: 'Pengeluaran (Est.)',
          data: pengeluaran,
          backgroundColor: '#ef4444',
          borderRadius: 4,
          barPercentage: 0.45,
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: true, position: 'top' },
        tooltip: {
          backgroundColor: '#0f1e3c',
          cornerRadius: 8,
          callbacks: {
            label: ctx => ` ${ctx.dataset.label}: Rp ` + parseInt(ctx.raw).toLocaleString('id-ID')
          }
        }
      },
      scales: {
        x: { grid:{ display:false }, ticks:{ color:'#64748b', font:{ size:10 } }, border:{ display:false } },
        y: {
          grid:{ color:'#f1f5f9' },
          ticks: {
            color:'#64748b', font:{ size:10 },
            callback: v => v>=1000000 ? (v/1000000).toFixed(1)+'Jt' : v>=1000 ? (v/1000).toFixed(0)+'Rb' : v
          },
          border:{ display:false }
        }
      }
    }
  });
}

// ─── Init on load ──────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  const activeTab = '{{ $activeTab }}';
  if (activeTab === 'pendapatan' || activeTab === '') {
    initLaporanChart();
  } else if (activeTab === 'perbandingan') {
    initCompareChart();
  }
});
</script>
@endpush
