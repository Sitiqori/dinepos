@extends('layouts.master')
@section('title', 'Manajemen Pesanan')
@section('page_title', 'Pesanan')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}" />
<style>
/* ─── Stat cards ─────────────────────── */
.pes-stats { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:24px; }
.pes-stat { background:#fff; border:1px solid var(--border); border-radius:var(--radius-md); padding:18px 20px; display:flex; align-items:center; gap:14px; }
.pes-stat-icon { width:44px; height:44px; border-radius:var(--radius-sm); background:var(--navy); color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.1rem; flex-shrink:0; }
.pes-stat-label { font-size:.82rem; color:var(--text-muted); font-weight:600; margin-bottom:2px; }
.pes-stat-val { font-size:1.5rem; font-weight:800; color:var(--navy); font-family:'Poppins',sans-serif; }

/* ─── Status tabs ────────────────────── */
.status-tabs { display:flex; gap:6px; flex-wrap:wrap; margin-bottom:16px; }
.stab { display:flex; align-items:center; gap:6px; padding:7px 16px; border-radius:99px; border:1.5px solid var(--border); background:#fff; font-size:.82rem; font-weight:600; color:var(--text-muted); text-decoration:none; cursor:pointer; transition:all .15s; }
.stab .cnt { background:var(--bg); border-radius:99px; padding:1px 7px; font-size:.7rem; font-weight:700; }
.stab.active, .stab:hover { background:var(--navy); color:#fff; border-color:var(--navy); }
.stab.active .cnt, .stab:hover .cnt { background:rgba(255,255,255,.2); }

/* ─── Search ─────────────────────────── */
.pes-topbar { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:16px; flex-wrap:wrap; }
.pes-search { display:flex; align-items:center; gap:8px; background:#fff; border:1.5px solid var(--border); border-radius:10px; padding:8px 14px; flex:1; max-width:360px; transition:border-color .2s; }
.pes-search:focus-within { border-color:var(--blue); }
.pes-search i { color:var(--text-muted); }
.pes-search input { border:none; outline:none; background:none; font-family:inherit; font-size:.875rem; color:var(--text); width:100%; }

/* ─── Order list ─────────────────────── */
.pes-card { background:#fff; border:1px solid var(--border); border-radius:var(--radius-md); overflow:hidden; }
.pes-card-header { display:flex; align-items:center; gap:10px; padding:14px 20px; border-bottom:1px solid var(--border); }
.pes-card-header h3 { font-size:.95rem; font-weight:700; color:var(--navy); }

.order-row { display:flex; align-items:center; gap:12px; padding:16px 20px; border-bottom:1px solid var(--border); transition:background .12s; }
.order-row:last-child { border-bottom:none; }
.order-row:hover { background:#f8fafc; }

.orow-check { width:18px; height:18px; border-radius:4px; border:1.5px solid var(--border); cursor:pointer; accent-color:var(--navy); flex-shrink:0; }
.orow-edit { color:var(--teal); font-size:.9rem; cursor:pointer; padding:4px; flex-shrink:0; transition:color .15s; }
.orow-edit:hover { color:var(--navy); }

.orow-body { flex:1; min-width:0; }
.orow-name { font-weight:700; font-size:.925rem; color:var(--navy); margin-bottom:2px; }
.orow-amount { font-size:.82rem; color:var(--text-muted); margin-bottom:2px; }
.orow-note { font-size:.78rem; color:var(--blue); margin-bottom:2px; }
.orow-meta { font-size:.75rem; color:var(--text-muted); }

/* ─── Action buttons ─────────────────── */
.orow-actions { display:flex; align-items:center; gap:8px; flex-shrink:0; flex-wrap:wrap; justify-content:flex-end; }

/* Detail button (navy) */
.btn-detail { display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border-radius:8px; background:var(--navy); color:#fff; border:none; font-family:inherit; font-size:.78rem; font-weight:600; cursor:pointer; transition:all .15s; white-space:nowrap; }
.btn-detail:hover { background:var(--navy-mid); }

/* Proses button (blue outline) */
.btn-proses { display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border-radius:8px; background:#dbeafe; color:#1d4ed8; border:1.5px solid #93c5fd; font-family:inherit; font-size:.78rem; font-weight:600; cursor:pointer; transition:all .15s; white-space:nowrap; }
.btn-proses:hover { background:#1d4ed8; color:#fff; border-color:#1d4ed8; }
.btn-proses:disabled { opacity:.5; cursor:not-allowed; }

/* Selesaikan button (green) */
.btn-selesai { display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border-radius:8px; background:#d1fae5; color:#065f46; border:1.5px solid #6ee7b7; font-family:inherit; font-size:.78rem; font-weight:600; cursor:pointer; transition:all .15s; white-space:nowrap; }
.btn-selesai:hover { background:#059669; color:#fff; border-color:#059669; }
.btn-selesai:disabled { opacity:.5; cursor:not-allowed; }

/* Hapus button (red) */
.btn-hapus { display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border-radius:8px; background:#fee2e2; color:#991b1b; border:1.5px solid #fca5a5; font-family:inherit; font-size:.78rem; font-weight:600; cursor:pointer; transition:all .15s; white-space:nowrap; }
.btn-hapus:hover { background:#ef4444; color:#fff; border-color:#ef4444; }
.btn-hapus:disabled { opacity:.5; cursor:not-allowed; }

/* Bulk toolbar */
.bulk-toolbar {
  display: none;
  align-items: center;
  gap: 12px;
  padding: 10px 20px;
  background: #eff6ff;
  border-bottom: 1.5px solid #bfdbfe;
  animation: slideDown .2s ease;
}
.bulk-toolbar.show { display: flex; }
@keyframes slideDown { from{opacity:0;transform:translateY(-8px)} to{opacity:1;transform:translateY(0)} }
.bulk-info { font-size:.82rem; font-weight:700; color:#1d4ed8; flex:1; }
.btn-bulk-hapus { display:inline-flex; align-items:center; gap:6px; padding:7px 16px; border-radius:8px; background:#ef4444; color:#fff; border:none; font-family:inherit; font-size:.82rem; font-weight:700; cursor:pointer; transition:all .15s; }
.btn-bulk-hapus:hover { background:#dc2626; }
.btn-bulk-cancel { display:inline-flex; align-items:center; gap:6px; padding:7px 14px; border-radius:8px; background:#fff; color:var(--text-muted); border:1.5px solid var(--border); font-family:inherit; font-size:.82rem; font-weight:600; cursor:pointer; transition:all .15s; }
.btn-bulk-cancel:hover { border-color:var(--navy); color:var(--navy); }

/* Status badge */
.obadge { display:inline-flex; align-items:center; padding:3px 10px; border-radius:99px; font-size:.72rem; font-weight:700; flex-shrink:0; }
.obadge-pending    { background:#fef3c7; color:#92400e; }
.obadge-processing { background:#dbeafe; color:#1d4ed8; }
.obadge-completed  { background:#d1fae5; color:#065f46; }
.obadge-cancelled  { background:#fee2e2; color:#991b1b; }

/* Product thumbnail */
.orow-img    { width:70px; height:70px; border-radius:10px; object-fit:cover; flex-shrink:0; border:1px solid var(--border); }
.orow-img-ph { width:70px; height:70px; border-radius:10px; background:var(--bg); display:flex; align-items:center; justify-content:center; font-size:1.8rem; flex-shrink:0; border:1px solid var(--border); }

/* Empty state */
.empty-state { text-align:center; padding:50px 20px; color:var(--text-muted); }
.empty-state i { font-size:3rem; display:block; margin-bottom:12px; opacity:.3; }
.empty-state p { font-size:.875rem; }

/* Footer */
.pes-footer { padding:12px 20px; border-top:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; font-size:.8rem; color:var(--text-muted); }

/* ─── MODAL DETAIL ───────────────────── */
.mbd { display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:400; align-items:center; justify-content:center; padding:20px; backdrop-filter:blur(4px); }
.mbd.show { display:flex; }
.mbox { background:#fff; border-radius:16px; width:100%; max-width:520px; max-height:92vh; overflow-y:auto; box-shadow:0 24px 64px rgba(0,0,0,.2); animation:mIn .22s ease; }
@keyframes mIn { from{opacity:0;transform:scale(.95) translateY(10px)} to{opacity:1;transform:scale(1) translateY(0)} }
.mhd { display:flex; align-items:center; justify-content:space-between; padding:18px 24px; border-bottom:1px solid var(--border); }
.mhd h3 { font-size:1.05rem; color:var(--navy); }
.mcls { width:30px; height:30px; border-radius:50%; border:none; background:var(--bg); color:var(--text-muted); cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:1rem; transition:all .15s; }
.mcls:hover { background:var(--navy); color:#fff; }
.mbody { padding:20px 24px 24px; }

.detail-info-box { background:#e8f0fe; border-radius:10px; padding:16px; display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:20px; }
.dib-label { font-size:.72rem; color:#4a6fa5; font-weight:600; text-transform:uppercase; letter-spacing:.04em; margin-bottom:4px; }
.dib-val   { font-size:.9rem; font-weight:700; color:var(--navy); }
.dib-status { display:inline-flex; align-items:center; padding:4px 12px; border-radius:99px; font-size:.78rem; font-weight:700; }
.dib-status.pending    { background:#fef3c7; color:#92400e; }
.dib-status.processing { background:#dbeafe; color:#1d4ed8; }
.dib-status.completed  { background:#d1fae5; color:#065f46; }
.dib-status.cancelled  { background:#fee2e2; color:#991b1b; }

.detail-items-label { font-size:.8rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.04em; margin-bottom:10px; }
.detail-item { display:flex; align-items:center; justify-content:space-between; padding:10px 14px; border:1px solid var(--border); border-radius:8px; margin-bottom:8px; }
.di-name  { font-weight:700; font-size:.875rem; color:var(--text); }
.di-sub   { font-size:.75rem; color:var(--text-muted); margin-top:2px; }
.di-price { font-weight:700; font-size:.9rem; color:var(--blue); white-space:nowrap; }

.modal-action-row { display:flex; gap:8px; margin-top:18px; }
.modal-action-row button { flex:1; padding:11px; border-radius:10px; font-family:inherit; font-size:.875rem; font-weight:700; cursor:pointer; transition:all .2s; display:flex; align-items:center; justify-content:center; gap:6px; }
.mbtn-tutup   { background:#fff; color:var(--text); border:1.5px solid var(--border); }
.mbtn-tutup:hover { border-color:var(--navy); color:var(--navy); }
.mbtn-proses  { background:#dbeafe; color:#1d4ed8; border:1.5px solid #93c5fd; }
.mbtn-proses:hover { background:#1d4ed8; color:#fff; border-color:#1d4ed8; }
.mbtn-selesai { background:var(--navy); color:#fff; border:none; }
.mbtn-selesai:hover { background:var(--navy-mid); }

/* Spinning loader */
@keyframes spin { from{transform:rotate(0)} to{transform:rotate(360deg)} }
</style>
@endpush

@section('content')

{{-- ── STAT CARDS ─────────────────────── --}}
<div class="pes-stats">
  <div class="pes-stat">
    <div class="pes-stat-icon"><i class="ri-loader-4-line"></i></div>
    <div>
      <div class="pes-stat-label">Pesanan Diproses</div>
      <div class="pes-stat-val">{{ $statusCounts['processing'] }}</div>
    </div>
  </div>
  <div class="pes-stat">
    <div class="pes-stat-icon"><i class="ri-time-line"></i></div>
    <div>
      <div class="pes-stat-label">Menunggu Diproses</div>
      <div class="pes-stat-val">{{ $statusCounts['pending'] }}</div>
    </div>
  </div>
</div>

{{-- ── STATUS TABS ────────────────────── --}}
<div class="status-tabs">
  <a href="{{ route('pesanan.index', ['status'=>'active']) }}"
     class="stab {{ $activeStatus === 'active' ? 'active' : '' }}">
    <i class="ri-time-line"></i> Belum Selesai
    <span class="cnt">{{ $statusCounts['active'] }}</span>
  </a>
  <a href="{{ route('pesanan.index', ['status'=>'pending']) }}"
     class="stab {{ $activeStatus === 'pending' ? 'active' : '' }}">
    Pending <span class="cnt">{{ $statusCounts['pending'] }}</span>
  </a>
  <a href="{{ route('pesanan.index', ['status'=>'processing']) }}"
     class="stab {{ $activeStatus === 'processing' ? 'active' : '' }}">
    Diproses <span class="cnt">{{ $statusCounts['processing'] }}</span>
  </a>
  <a href="{{ route('pesanan.index', ['status'=>'completed']) }}"
     class="stab {{ $activeStatus === 'completed' ? 'active' : '' }}">
    <i class="ri-checkbox-circle-line"></i> Selesai
    <span class="cnt">{{ $statusCounts['completed'] }}</span>
  </a>
  <a href="{{ route('pesanan.index', ['status'=>'all']) }}"
     class="stab {{ $activeStatus === 'all' ? 'active' : '' }}">
    Semua <span class="cnt">{{ $statusCounts['all'] }}</span>
  </a>
</div>

{{-- ── SEARCH ─────────────────────────── --}}
<div class="pes-topbar">
  <div class="pes-search">
    <i class="ri-search-line"></i>
    <input type="text" id="searchInput" placeholder="Cari pesanan, produk..."
      value="{{ request('search') }}" oninput="debSearch()" />
  </div>
  @if($activeStatus === 'active' || $activeStatus === 'pending' || $activeStatus === 'processing')
  <span style="font-size:.8rem;color:var(--text-muted);">
    <i class="ri-information-line"></i>
    Pesanan selesai akan dipindahkan ke Riwayat Transaksi
  </span>
  @endif
</div>

{{-- ── ORDER LIST ─────────────────────── --}}
<div class="pes-card" id="orderList">
  <div class="pes-card-header">
    <input type="checkbox" class="chk-all" id="chkAll" onchange="toggleAll(this)"
      style="width:18px;height:18px;border-radius:4px;accent-color:var(--navy);" />
    <h3>
      @if($activeStatus === 'active')
        Pesanan Aktif (Belum Selesai)
      @elseif($activeStatus === 'completed')
        Pesanan Selesai
      @elseif($activeStatus === 'all')
        Semua Pesanan
      @else
        Pesanan — {{ ucfirst($activeStatus) }}
      @endif
    </h3>
  </div>

  {{-- Bulk toolbar --}}
  <div class="bulk-toolbar" id="bulkToolbar">
    <span class="bulk-info" id="bulkInfo">0 pesanan dipilih</span>
    <button class="btn-bulk-hapus" onclick="hapusBulk()">
      <i class="ri-delete-bin-line"></i> Hapus Semua Dipilih
    </button>
    <button class="btn-bulk-cancel" onclick="clearSelection()">
      <i class="ri-close-line"></i> Batal
    </button>
  </div>

  @forelse($orders as $order)
  @php
    $firstItem    = $order->items->first();
    $firstProduct = $firstItem?->product;
    $allNames     = $order->items->pluck('product_name')->join(', ');
    $totalQty     = $order->items->sum('qty');
    $isActive     = in_array($order->status, ['pending', 'processing']);
  @endphp
  <div class="order-row" id="orow-{{ $order->id }}">
    <input type="checkbox" class="orow-check row-chk" data-id="{{ $order->id }}" onchange="onRowCheck()" />
    <i class="ri-edit-line orow-edit" onclick="openDetail({{ $order->id }})" title="Edit"></i>

    <div class="orow-body">
      <div class="orow-name">{{ $allNames ?: $order->order_code }}</div>
      <div class="orow-amount">
        Qty: {{ $totalQty }} item ·
        <strong>Rp {{ number_format($order->total, 0, ',', '.') }}</strong>
      </div>
      @if($order->notes)
        <div class="orow-note">Note: {{ $order->notes }}</div>
      @endif
      <div class="orow-meta">
        {{ $order->order_code }} ·
        {{ $order->created_at->format('d/m/Y H:i') }} ·
        {{ $order->user?->name ?? 'Admin' }}
        @if($order->table_number) · Meja {{ $order->table_number }} @endif
      </div>
    </div>

    <div class="orow-actions">
      {{-- Status badge --}}
      <span class="obadge obadge-{{ $order->status }}" id="badge-{{ $order->id }}">
        {{ ['pending'=>'Pending','processing'=>'Diproses','completed'=>'Selesai','cancelled'=>'Batal'][$order->status] ?? $order->status }}
      </span>

      {{-- Action buttons for active orders --}}
      @if($isActive)
        @if($order->status === 'pending')
          <button class="btn-proses" onclick="quickUpdate({{ $order->id }}, 'processing', this)">
            <i class="ri-loader-4-line"></i> Proses
          </button>
        @endif
        <button class="btn-selesai" onclick="quickUpdate({{ $order->id }}, 'completed', this)">
          <i class="ri-checkbox-circle-line"></i> Selesaikan
        </button>
      @endif

      {{-- Detail button --}}
      <button class="btn-detail" onclick="openDetail({{ $order->id }})">
        Lihat Detail <i class="ri-eye-line"></i>
      </button>

      {{-- Hapus button --}}
      <button class="btn-hapus" onclick="hapusPesanan({{ $order->id }}, '{{ $order->order_code }}', this)">
        <i class="ri-delete-bin-line"></i>
      </button>
    </div>

    {{-- Product thumbnail --}}
    @if($firstProduct?->image)
      <img class="orow-img" src="{{ asset('storage/'.$firstProduct->image) }}" alt="" />
    @else
      <div class="orow-img-ph">🍽️</div>
    @endif
  </div>
  @empty
  <div class="empty-state" id="emptyState">
    <i class="ri-inbox-line"></i>
    <p>
      @if($activeStatus === 'active')
        Tidak ada pesanan yang menunggu. Semua sudah selesai! 🎉
      @else
        Belum ada pesanan
      @endif
    </p>
  </div>
  @endforelse

  <div class="pes-footer">
    <span>{{ $orders->count() }} pesanan ditampilkan</span>
    @if($orders->hasPages())
      {{ $orders->withQueryString()->links() }}
    @endif
  </div>
</div>


{{-- ════ MODAL: DETAIL PESANAN ════ --}}
<div class="mbd" id="mDetail">
  <div class="mbox">
    <div class="mhd">
      <h3>Detail Pesanan</h3>
      <button class="mcls" onclick="closeModal()"><i class="ri-close-line"></i></button>
    </div>
    <div class="mbody" id="mDetailBody">
      <div style="text-align:center;padding:30px;color:var(--text-muted);">
        <i class="ri-loader-4-line" style="font-size:2rem;animation:spin 1s linear infinite;display:block;margin-bottom:8px;"></i>
        Memuat data...
      </div>
    </div>
  </div>
</div>

{{-- ════ MODAL: KONFIRMASI HAPUS ════ --}}
<div class="mbd" id="mHapus">
  <div class="mbox" style="max-width:400px;">
    <div class="mhd">
      <h3 style="color:var(--red);">Hapus Pesanan</h3>
      <button class="mcls" onclick="document.getElementById('mHapus').classList.remove('show')"><i class="ri-close-line"></i></button>
    </div>
    <div class="mbody">
      <div style="text-align:center;padding:8px 0 20px;">
        <div style="width:56px;height:56px;background:#fee2e2;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;font-size:1.5rem;color:#ef4444;">
          <i class="ri-delete-bin-line"></i>
        </div>
        <p style="font-size:.9rem;color:var(--text);font-weight:600;margin-bottom:6px;">
          Hapus pesanan <span id="hapusOrderCode" style="color:var(--navy)"></span>?
        </p>
        <p style="font-size:.8rem;color:var(--text-muted);">
          Tindakan ini tidak bisa dibatalkan. Data pesanan dan item-nya akan dihapus permanen.
        </p>
      </div>
      <div style="display:flex;gap:8px;">
        <button onclick="document.getElementById('mHapus').classList.remove('show')"
          style="flex:1;padding:11px;border-radius:10px;background:#fff;border:1.5px solid var(--border);font-family:inherit;font-size:.875rem;font-weight:700;cursor:pointer;">
          Batal
        </button>
        <button id="btnKonfirmasiHapus" onclick="konfirmasiHapus()"
          style="flex:1;padding:11px;border-radius:10px;background:#ef4444;color:#fff;border:none;font-family:inherit;font-size:.875rem;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;">
          <i class="ri-delete-bin-line"></i> Ya, Hapus
        </button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
const CSRF    = '{{ csrf_token() }}';
const baseUrl = '{{ url("/pesanan") }}';
let currentOrderId = null;

// ─── Quick update buttons (Proses / Selesaikan) ───────
async function quickUpdate(id, newStatus, btnEl) {
  btnEl.disabled = true;
  const origHTML = btnEl.innerHTML;
  btnEl.innerHTML = '<i class="ri-loader-4-line" style="animation:spin 1s linear infinite"></i> Menyimpan...';

  try {
    const res  = await fetch(`${baseUrl}/${id}/status`, {
      method: 'PATCH',
      headers: { 'Content-Type':'application/json', 'Accept':'application/json', 'X-CSRF-TOKEN':CSRF },
      body: JSON.stringify({ status: newStatus }),
    });
    const data = await res.json();

    if (data.success) {
      if (data.completed) {
        // Selesai → remove row with animation, redirect to transaksi
        const row = document.getElementById(`orow-${id}`);
        if (row) {
          row.style.transition = 'all .4s ease';
          row.style.opacity = '0';
          row.style.transform = 'translateX(20px)';
          setTimeout(() => {
            row.remove();
            checkEmptyList();
          }, 400);
        }
        toast('Pesanan selesai! Diarahkan ke Riwayat Transaksi...', 'success');
        setTimeout(() => window.location.href = '{{ route("transaksi.index") }}', 1800);
      } else {
        // Status update — refresh row buttons
        updateRowStatus(id, newStatus);
        toast('Status diperbarui: ' + ({pending:'Pending',processing:'Diproses'}[newStatus] || newStatus), 'success');
      }
    } else {
      toast(data.message || 'Gagal memperbarui.', 'error');
      btnEl.disabled = false;
      btnEl.innerHTML = origHTML;
    }
  } catch(e) {
    toast('Terjadi kesalahan.', 'error');
    btnEl.disabled = false;
    btnEl.innerHTML = origHTML;
  }
}

function updateRowStatus(id, newStatus) {
  // Update badge
  const badge = document.getElementById(`badge-${id}`);
  if (badge) {
    const labelMap = { pending:'Pending', processing:'Diproses', completed:'Selesai', cancelled:'Batal' };
    const classMap = { pending:'obadge-pending', processing:'obadge-processing', completed:'obadge-completed', cancelled:'obadge-cancelled' };
    badge.textContent = labelMap[newStatus] || newStatus;
    badge.className   = 'obadge ' + (classMap[newStatus] || '');
  }

  // Replace action buttons
  const row = document.getElementById(`orow-${id}`);
  if (!row) return;
  const actionsDiv = row.querySelector('.orow-actions');
  if (!actionsDiv) return;

const statusText = {
  pending: 'Pending',
  processing: 'Diproses'
};

let btnHTML = `<span class="obadge obadge-${newStatus}" id="badge-${id}">
  ${statusText[newStatus] || newStatus}
</span>`;

  if (newStatus === 'processing') {
    btnHTML += `<button class="btn-selesai" onclick="quickUpdate(${id},'completed',this)"><i class="ri-checkbox-circle-line"></i> Selesaikan</button>`;
  } else if (newStatus === 'pending') {
    btnHTML += `<button class="btn-proses" onclick="quickUpdate(${id},'processing',this)"><i class="ri-loader-4-line"></i> Proses</button>`;
    btnHTML += `<button class="btn-selesai" onclick="quickUpdate(${id},'completed',this)"><i class="ri-checkbox-circle-line"></i> Selesaikan</button>`;
  }

  btnHTML += `<button class="btn-detail" onclick="openDetail(${id})">Lihat Detail <i class="ri-eye-line"></i></button>`;
  actionsDiv.innerHTML = btnHTML;
}

function checkEmptyList() {
  const rows = document.querySelectorAll('.order-row');
  if (rows.length === 0) {
    const card = document.getElementById('orderList');
    if (card) {
      const existing = card.querySelector('.empty-state');
      if (!existing) {
        const footer = card.querySelector('.pes-footer');
        const div = document.createElement('div');
        div.className = 'empty-state';
        div.innerHTML = '<i class="ri-inbox-line"></i><p>Tidak ada pesanan yang menunggu. Semua sudah selesai! 🎉</p>';
        card.insertBefore(div, footer);
      }
    }
  }
}

// ─── Modal detail ─────────────────────────────────────
async function openDetail(id) {
  currentOrderId = id;
  document.getElementById('mDetail').classList.add('show');
  document.getElementById('mDetailBody').innerHTML = `
    <div style="text-align:center;padding:30px;color:var(--text-muted);">
      <i class="ri-loader-4-line" style="font-size:2rem;animation:spin 1s linear infinite;display:block;margin-bottom:8px;"></i>
      Memuat data...
    </div>`;

  try {
    const res   = await fetch(`${baseUrl}/${id}`, { headers:{'Accept':'application/json','X-CSRF-TOKEN':CSRF} });
    const order = await res.json();
    renderDetail(order);
  } catch(e) {
    document.getElementById('mDetailBody').innerHTML = `<p style="color:var(--red);text-align:center;">Gagal memuat data pesanan.</p>`;
  }
}

function closeModal() {
  document.getElementById('mDetail').classList.remove('show');
  currentOrderId = null;
}

function renderDetail(order) {
  const dt    = new Date(order.created_at);
  const dtStr = dt.toLocaleDateString('id-ID',{day:'2-digit',month:'2-digit',year:'numeric'})
              + ' ' + dt.toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'});

  const statusLabel = {pending:'Pending',processing:'Diproses',completed:'Selesai',cancelled:'Batal'}[order.status] || order.status;
  const invoiceCode = order.transaction?.invoice_code ?? order.order_code;

  const itemsHtml = (order.items || []).map(item => `
    <div class="detail-item">
      <div>
        <div class="di-name">${item.product_name}</div>
        <div class="di-sub">Rp ${parseInt(item.price).toLocaleString('id-ID')} × ${item.qty}</div>
      </div>
      <div class="di-price">Rp ${parseInt(item.subtotal).toLocaleString('id-ID')}</div>
    </div>`).join('');

  const totalItems = (order.items || []).reduce((s,i)=>s+i.qty,0);
  const isActive   = ['pending','processing'].includes(order.status);

  // Modal action buttons
  let actionBtns = `<button class="mbtn-tutup" onclick="closeModal()">Tutup</button>`;
  if (order.status === 'pending') {
    actionBtns += `<button class="mbtn-proses"  onclick="modalUpdate('processing')"><i class="ri-loader-4-line"></i> Proses</button>`;
    actionBtns += `<button class="mbtn-selesai" onclick="modalUpdate('completed')"><i class="ri-checkbox-circle-line"></i> Selesaikan</button>`;
  } else if (order.status === 'processing') {
    actionBtns += `<button class="mbtn-selesai" onclick="modalUpdate('completed')"><i class="ri-checkbox-circle-line"></i> Selesaikan</button>`;
  } else if (order.status === 'completed') {
    actionBtns += `<span style="font-size:.82rem;color:var(--text-muted);align-self:center;"><i class="ri-checkbox-circle-line" style="color:var(--green)"></i> Pesanan selesai</span>`;
  }

  // Tombol hapus selalu ada
  actionBtns += `<button class="mbtn-hapus" onclick="hapusPesananModal()" style="background:#fee2e2;color:#991b1b;border:1.5px solid #fca5a5;border-radius:10px;padding:11px 16px;font-family:inherit;font-size:.875rem;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:6px;transition:all .2s;">
    <i class="ri-delete-bin-line"></i> Hapus
  </button>`;

  document.getElementById('mDetailBody').innerHTML = `
    <div class="detail-info-box">
      <div>
        <div class="dib-label">No. Transaksi</div>
        <div class="dib-val" style="font-size:.82rem;font-family:monospace;">${invoiceCode}</div>
      </div>
      <div>
        <div class="dib-label">Tanggal/Jam</div>
        <div class="dib-val" style="font-size:.82rem;">${dtStr}</div>
      </div>
      <div>
        <div class="dib-label">Kasir</div>
        <div class="dib-val">${order.user?.name ?? 'Admin'}</div>
      </div>
      <div>
        <div class="dib-label">Status</div>
        <div class="dib-val"><span class="dib-status ${order.status}">${statusLabel}</span></div>
      </div>
    </div>

    <div class="detail-items-label">Item Transaksi (${totalItems} item)</div>
    ${itemsHtml}

    <div style="display:flex;justify-content:space-between;padding:10px 14px;border-top:1.5px solid var(--border);margin-top:8px;">
      <span style="font-weight:600;font-size:.875rem;">Total Pembayaran</span>
      <span style="font-weight:800;font-size:.95rem;color:var(--navy);">Rp ${parseInt(order.total).toLocaleString('id-ID')}</span>
    </div>

    <div class="modal-action-row" id="modalActionBtns">
      ${actionBtns}
    </div>`;
}

async function modalUpdate(newStatus) {
  if (!currentOrderId) return;
  const btns = document.querySelectorAll('#modalActionBtns button');
  btns.forEach(b => b.disabled = true);

  try {
    const res  = await fetch(`${baseUrl}/${currentOrderId}/status`, {
      method: 'PATCH',
      headers: { 'Content-Type':'application/json', 'Accept':'application/json', 'X-CSRF-TOKEN':CSRF },
      body: JSON.stringify({ status: newStatus }),
    });
    const data = await res.json();

    if (data.success) {
      closeModal();
      if (data.completed) {
        // Remove from list, redirect
        const row = document.getElementById(`orow-${currentOrderId}`);
        if (row) {
          row.style.transition = 'all .4s ease';
          row.style.opacity = '0';
          setTimeout(() => { row.remove(); checkEmptyList(); }, 400);
        }
        toast('Pesanan selesai! Menuju Riwayat Transaksi...', 'success');
        setTimeout(() => window.location.href = '{{ route("transaksi.index") }}', 1800);
      } else {
        updateRowStatus(currentOrderId, newStatus);
        toast('Status diperbarui.', 'success');
      }
    } else {
      toast(data.message || 'Gagal.', 'error');
      btns.forEach(b => b.disabled = false);
    }
  } catch(e) {
    toast('Terjadi kesalahan.', 'error');
    btns.forEach(b => b.disabled = false);
  }
}

// ─── Hapus pesanan ────────────────────────────────────
let hapusTargetId = null;

function hapusPesanan(id, code, btnEl) {
  hapusTargetId = id;
  document.getElementById('hapusOrderCode').textContent = code;
  document.getElementById('mHapus').classList.add('show');
}

function hapusPesananModal() {
  if (!currentOrderId) return;
  closeModal();
  const row = document.getElementById(`orow-${currentOrderId}`);
  const code = row?.querySelector('.orow-meta')?.textContent?.split('·')[0]?.trim() ?? '';
  hapusTargetId = currentOrderId;
  document.getElementById('hapusOrderCode').textContent = code;
  document.getElementById('mHapus').classList.add('show');
}

async function konfirmasiHapus() {
  if (!hapusTargetId) return;
  const btn = document.getElementById('btnKonfirmasiHapus');
  btn.disabled = true;
  btn.innerHTML = '<i class="ri-loader-4-line" style="animation:spin 1s linear infinite"></i> Menghapus...';

  try {
    const res  = await fetch(`${baseUrl}/${hapusTargetId}`, {
      method: 'DELETE',
      headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
    });
    const data = await res.json();

    if (data.success) {
      document.getElementById('mHapus').classList.remove('show');
      const row = document.getElementById(`orow-${hapusTargetId}`);
      if (row) {
        row.style.transition = 'all .35s ease';
        row.style.opacity    = '0';
        row.style.transform  = 'translateX(20px)';
        setTimeout(() => { row.remove(); checkEmptyList(); }, 360);
      }
      toast(data.message || 'Pesanan berhasil dihapus.', 'success');
    } else {
      toast(data.message || 'Gagal menghapus.', 'error');
    }
  } catch(e) {
    toast('Terjadi kesalahan.', 'error');
  } finally {
    btn.disabled = false;
    btn.innerHTML = '<i class="ri-delete-bin-line"></i> Ya, Hapus';
    hapusTargetId = null;
  }
}

// ─── Checkbox & bulk selection ───────────────────────
function toggleAll(chk) {
  document.querySelectorAll('.row-chk').forEach(c => c.checked = chk.checked);
  onRowCheck();
}

function onRowCheck() {
  const checked = document.querySelectorAll('.row-chk:checked');
  const toolbar = document.getElementById('bulkToolbar');
  const info    = document.getElementById('bulkInfo');
  if (checked.length > 0) {
    toolbar.classList.add('show');
    info.textContent = `${checked.length} pesanan dipilih`;
  } else {
    toolbar.classList.remove('show');
    document.getElementById('chkAll').checked = false;
  }
}

function clearSelection() {
  document.querySelectorAll('.row-chk').forEach(c => c.checked = false);
  document.getElementById('chkAll').checked = false;
  document.getElementById('bulkToolbar').classList.remove('show');
}

async function hapusBulk() {
  const checked = document.querySelectorAll('.row-chk:checked');
  if (!checked.length) return;
  const ids = [...checked].map(c => parseInt(c.dataset.id));

  if (!confirm(`Hapus ${ids.length} pesanan? Stok akan dikembalikan otomatis.`)) return;

  const btn = document.querySelector('.btn-bulk-hapus');
  btn.disabled = true;
  btn.innerHTML = '<i class="ri-loader-4-line" style="animation:spin 1s linear infinite"></i> Menghapus...';

  try {
    const res  = await fetch(baseUrl, {
      method: 'DELETE',
      headers: { 'Content-Type':'application/json', 'Accept':'application/json', 'X-CSRF-TOKEN':CSRF },
      body: JSON.stringify({ ids }),
    });
    const data = await res.json();

    if (data.success) {
      ids.forEach(id => {
        const row = document.getElementById(`orow-${id}`);
        if (row) {
          row.style.transition = 'all .3s ease';
          row.style.opacity    = '0';
          row.style.transform  = 'translateX(20px)';
          setTimeout(() => { row.remove(); checkEmptyList(); }, 320);
        }
      });
      clearSelection();
      toast(data.message, 'success');
    } else {
      toast(data.message || 'Gagal menghapus.', 'error');
    }
  } catch(e) {
    toast('Terjadi kesalahan.', 'error');
  } finally {
    btn.disabled = false;
    btn.innerHTML = '<i class="ri-delete-bin-line"></i> Hapus Semua Dipilih';
  }
}

// ─── Search debounce ──────────────────────────────────
let st;
function debSearch() {
  clearTimeout(st);
  st = setTimeout(() => {
    const q   = document.getElementById('searchInput').value;
    const url = new URL(window.location.href);
    url.searchParams.set('search', q);
    url.searchParams.delete('page');
    window.location.href = url.toString();
  }, 400);
}

// ─── Toast ────────────────────────────────────────────
function toast(msg, type='success') {
  const t = document.createElement('div');
  Object.assign(t.style, {
    position:'fixed', bottom:'24px', right:'24px', zIndex:'9999',
    background: type==='error'?'#ef4444':'#10b981',
    color:'#fff', padding:'10px 18px', borderRadius:'10px',
    fontWeight:'600', fontSize:'.875rem',
    boxShadow:'0 4px 16px rgba(0,0,0,.15)',
  });
  t.textContent = msg;
  document.body.appendChild(t);
  setTimeout(()=>{ t.style.opacity='0'; t.style.transition='opacity .3s'; setTimeout(()=>t.remove(),300); }, 3000);
}

// Close modal on backdrop click
document.getElementById('mDetail').addEventListener('click', function(e) {
  if (e.target === this) closeModal();
});
</script>
@endpush