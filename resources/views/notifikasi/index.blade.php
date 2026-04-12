@extends('layouts.master')
@section('title', 'Notifikasi')
@section('page_title', 'Notifikasi')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}" />
<style>
.notif-header { display:flex; align-items:center; gap:10px; margin-bottom:20px; }
.notif-header h2 { font-size:1.1rem; color:var(--navy); font-family:'Syne',sans-serif; flex:1; }
.notif-total-badge { display:inline-flex; align-items:center; justify-content:center; background:var(--red); color:#fff; font-size:.72rem; font-weight:700; min-width:22px; height:22px; padding:0 6px; border-radius:99px; }

.btn-read-all { display:inline-flex; align-items:center; gap:6px; padding:7px 14px; background:#fff; border:1.5px solid var(--border); border-radius:8px; font-size:.8rem; font-weight:600; color:var(--text-muted); cursor:pointer; transition:all .15s; }
.btn-read-all:hover { border-color:var(--blue); color:var(--blue); }

.ns-card { background:var(--card); border:1.5px solid var(--border); border-radius:var(--radius-md); overflow:hidden; margin-bottom:16px; }
.ns-head { display:flex; align-items:center; gap:10px; padding:12px 18px; border-bottom:1.5px solid var(--border); background:var(--bg); }
.ns-icon { width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:.95rem; flex-shrink:0; }
.ns-icon.orange { background:#fff7ed; color:var(--orange); }
.ns-icon.red    { background:var(--red-soft); color:var(--red); }
.ns-icon.blue   { background:#eff6ff; color:var(--blue); }
.ns-icon.teal   { background:#f0fdfa; color:var(--teal); }
.ns-head h3 { font-size:.88rem; font-weight:700; color:var(--navy); flex:1; }
.ns-badge { font-size:.72rem; font-weight:700; padding:2px 9px; border-radius:99px; color:#fff; }
.ns-badge.orange { background:var(--orange); }
.ns-badge.red    { background:var(--red); }
.ns-badge.blue   { background:var(--blue); }
.ns-badge.teal   { background:var(--teal); }

/* Row */
.ns-row { display:flex; align-items:center; gap:12px; padding:12px 18px; border-bottom:1px solid var(--border); transition:background .12s; position:relative; }
.ns-row:last-child { border-bottom:none; }
.ns-row:hover { background:var(--bg); }
.ns-row.is-read { opacity:.55; }

/* Unread dot */
.ns-unread-dot { width:8px; height:8px; border-radius:50%; background:var(--blue); flex-shrink:0; }
.ns-unread-dot.hidden { visibility:hidden; }

.ns-row-icon { width:34px; height:34px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:.88rem; flex-shrink:0; }
.ns-row-icon.orange { background:#fff7ed; color:var(--orange); }
.ns-row-icon.red    { background:var(--red-soft); color:var(--red); }
.ns-row-icon.blue   { background:#eff6ff; color:var(--blue); }
.ns-row-icon.teal   { background:#f0fdfa; color:var(--teal); }

.ns-row-body { flex:1; min-width:0; }
.ns-row-title { font-weight:600; font-size:.875rem; color:var(--navy); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.ns-row-sub { font-size:.78rem; color:var(--text-muted); margin-top:2px; }

.ns-chip { flex-shrink:0; display:inline-block; padding:3px 10px; border-radius:99px; font-size:.72rem; font-weight:700; }
.ns-chip.red    { background:var(--red-soft); color:var(--red); }
.ns-chip.orange { background:#fff7ed; color:var(--orange); }

.ns-link { flex-shrink:0; font-size:.8rem; color:var(--blue); font-weight:600; }
.ns-link:hover { text-decoration:underline; }

.btn-mark-read { flex-shrink:0; background:none; border:1.5px solid var(--border); border-radius:6px; padding:4px 10px; font-size:.72rem; font-weight:600; color:var(--text-muted); cursor:pointer; transition:all .15s; white-space:nowrap; }
.btn-mark-read:hover { border-color:var(--blue); color:var(--blue); }
.btn-mark-read.done { border-color:var(--green); color:var(--green); pointer-events:none; }

.ns-empty { display:flex; flex-direction:column; align-items:center; padding:32px 20px; color:var(--text-muted); gap:6px; }
.ns-empty i { font-size:1.8rem; opacity:.35; }
.ns-empty span { font-size:.82rem; }
</style>
@endpush

@section('content')

<div class="notif-header">
  <h2>Semua Notifikasi</h2>
  @if($totalNotif > 0)
    <span class="notif-total-badge" id="totalBadge">{{ $totalNotif }}</span>
  @else
    <span class="notif-total-badge" id="totalBadge" style="display:none;">0</span>
  @endif
  <button class="btn-read-all" id="btnReadAll" onclick="markAllRead()">
    <i class="ri-check-double-line"></i> Tandai semua dibaca
  </button>
</div>

{{-- 1. PESANAN BARU --}}
<div class="ns-card">
  <div class="ns-head">
    <div class="ns-icon blue"><i class="ri-shopping-bag-3-line"></i></div>
    <h3>Pesanan Baru</h3>
    @if($pesananBaru->count() > 0)<span class="ns-badge blue">{{ $pesananBaru->count() }}</span>@endif
  </div>
  @forelse($pesananBaru as $order)
    <div class="ns-row {{ $order->is_read ? 'is-read' : '' }}" id="notif-pesanan-{{ $order->id }}">
      <span class="ns-unread-dot {{ $order->is_read ? 'hidden' : '' }}"></span>
      <div class="ns-row-icon blue"><i class="ri-receipt-line"></i></div>
      <div class="ns-row-body">
        <div class="ns-row-title">#{{ $order->order_code }}@if($order->customer_name) — {{ $order->customer_name }}@endif</div>
        <div class="ns-row-sub">{{ $order->items->count() }} item &middot; Rp {{ number_format($order->total,0,',','.') }} &middot; {{ $order->created_at->diffForHumans() }}</div>
      </div>
      <a href="{{ route('pesanan.index', ['search' => $order->order_code, 'status' => 'all']) }}" class="ns-link" onclick="markRead('pesanan', {{ $order->id }}, this)">Lihat →</a>
      @if(!$order->is_read)
        <button class="btn-mark-read" onclick="markRead('pesanan', {{ $order->id }}, this)">Tandai dibaca</button>
      @endif
    </div>
  @empty
    <div class="ns-empty"><i class="ri-checkbox-circle-line"></i><span>Tidak ada pesanan baru saat ini.</span></div>
  @endforelse
</div>

{{-- 2. PESANAN DIPROSES --}}
@if($pesananDiproses->count() > 0)
<div class="ns-card">
  <div class="ns-head">
    <div class="ns-icon teal"><i class="ri-loader-4-line"></i></div>
    <h3>Sedang Diproses</h3>
    <span class="ns-badge teal">{{ $pesananDiproses->count() }}</span>
  </div>
  @foreach($pesananDiproses as $order)
    <div class="ns-row {{ $order->is_read ? 'is-read' : '' }}" id="notif-pesanan-{{ $order->id }}">
      <span class="ns-unread-dot {{ $order->is_read ? 'hidden' : '' }}"></span>
      <div class="ns-row-icon teal"><i class="ri-time-line"></i></div>
      <div class="ns-row-body">
        <div class="ns-row-title">#{{ $order->order_code }}@if($order->customer_name) — {{ $order->customer_name }}@endif</div>
        <div class="ns-row-sub">{{ $order->items->count() }} item &middot; Rp {{ number_format($order->total,0,',','.') }} &middot; {{ $order->created_at->diffForHumans() }}</div>
      </div>
      <a href="{{ route('pesanan.index', ['search' => $order->order_code, 'status' => 'all']) }}" class="ns-link" onclick="markRead('pesanan', {{ $order->id }}, this)">Lihat →</a>
      @if(!$order->is_read)
        <button class="btn-mark-read" onclick="markRead('pesanan', {{ $order->id }}, this)">Tandai dibaca</button>
      @endif
    </div>
  @endforeach
</div>
@endif

{{-- 3. STOK MENIPIS --}}
<div class="ns-card">
  <div class="ns-head">
    <div class="ns-icon orange"><i class="ri-archive-line"></i></div>
    <h3>Stok Menipis</h3>
    @if($stokMenipis->count() > 0)<span class="ns-badge orange">{{ $stokMenipis->count() }}</span>@endif
  </div>
  @forelse($stokMenipis as $p)
    <div class="ns-row {{ $p->is_read ? 'is-read' : '' }}" id="notif-stok-{{ $p->id }}">
      <span class="ns-unread-dot {{ $p->is_read ? 'hidden' : '' }}"></span>
      <div class="ns-row-icon orange"><i class="ri-alert-line"></i></div>
      <div class="ns-row-body">
        <div class="ns-row-title">{{ $p->name }}</div>
        <div class="ns-row-sub">{{ $p->category?->name ?? '-' }} &middot; Stok tersisa: <strong>{{ $p->stock }}</strong>@if($p->min_stock) &middot; Min: {{ $p->min_stock }}@endif</div>
      </div>
      <span class="ns-chip orange">Stok Rendah</span>
      @if(!$p->is_read)
        <button class="btn-mark-read" onclick="markRead('stok', {{ $p->id }}, this)">Tandai dibaca</button>
      @endif
    </div>
  @empty
    <div class="ns-empty"><i class="ri-checkbox-circle-line"></i><span>Semua stok barang dalam kondisi aman.</span></div>
  @endforelse
</div>

{{-- 4 & 5. KADALUARSA --}}
@if($hasExpiryDate)
<div class="ns-card">
  <div class="ns-head">
    <div class="ns-icon orange"><i class="ri-calendar-close-line"></i></div>
    <h3>Kadaluarsa dalam 30 Hari</h3>
    @if($kadaluarsaMendekat->count() > 0)<span class="ns-badge orange">{{ $kadaluarsaMendekat->count() }}</span>@endif
  </div>
  @forelse($kadaluarsaMendekat as $p)
    <div class="ns-row {{ $p->is_read ? 'is-read' : '' }}" id="notif-expiry-{{ $p->id }}">
      <span class="ns-unread-dot {{ $p->is_read ? 'hidden' : '' }}"></span>
      <div class="ns-row-icon orange"><i class="ri-timer-line"></i></div>
      <div class="ns-row-body">
        <div class="ns-row-title">{{ $p->name }}</div>
        <div class="ns-row-sub">{{ $p->category?->name ?? '-' }} &middot; Kadaluarsa: {{ $p->expiry_date->format('d M Y') }}</div>
      </div>
      <span class="ns-chip {{ $p->sisa_hari <= 7 ? 'red' : 'orange' }}">{{ $p->sisa_hari }} hari lagi</span>
      @if(!$p->is_read)
        <button class="btn-mark-read" onclick="markRead('expiry', {{ $p->id }}, this)">Tandai dibaca</button>
      @endif
    </div>
  @empty
    <div class="ns-empty"><i class="ri-checkbox-circle-line"></i><span>Tidak ada barang yang akan kadaluarsa dalam 30 hari ke depan.</span></div>
  @endforelse
</div>

@if($sudahKadaluarsa->count() > 0)
<div class="ns-card">
  <div class="ns-head">
    <div class="ns-icon red"><i class="ri-error-warning-line"></i></div>
    <h3>Sudah Kadaluarsa</h3>
    <span class="ns-badge red">{{ $sudahKadaluarsa->count() }}</span>
  </div>
  @foreach($sudahKadaluarsa as $p)
    <div class="ns-row {{ $p->is_read ? 'is-read' : '' }}" id="notif-expired-{{ $p->id }}">
      <span class="ns-unread-dot {{ $p->is_read ? 'hidden' : '' }}"></span>
      <div class="ns-row-icon red"><i class="ri-close-circle-line"></i></div>
      <div class="ns-row-body">
        <div class="ns-row-title">{{ $p->name }}</div>
        <div class="ns-row-sub">{{ $p->category?->name ?? '-' }} &middot; Kadaluarsa: {{ $p->expiry_date->format('d M Y') }} &middot; Stok: {{ $p->stock }}</div>
      </div>
      <span class="ns-chip red">Kadaluarsa</span>
      @if(!$p->is_read)
        <button class="btn-mark-read" onclick="markRead('expired', {{ $p->id }}, this)">Tandai dibaca</button>
      @endif
    </div>
  @endforeach
</div>
@endif
@endif

@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
let unreadCount = {{ $totalNotif }};

// Fix 5: Update navbar badge langsung saat halaman dibuka
(function() {
  const dot   = document.getElementById('notifDot');
  const badge = document.getElementById('notifCount');
  if (!dot || !badge) return;
  if (unreadCount > 0) {
    badge.textContent   = unreadCount > 99 ? '99+' : unreadCount;
    badge.style.display = 'flex';
    dot.style.display   = 'block';
  } else {
    badge.style.display = 'none';
    dot.style.display   = 'none';
  }
})();

function updateBadge(delta) {
  unreadCount = Math.max(0, unreadCount + delta);
  const dot   = document.getElementById('notifDot');
  const badge = document.getElementById('notifCount');
  const pageBadge = document.getElementById('totalBadge');
  if (unreadCount > 0) {
    badge.textContent   = unreadCount > 99 ? '99+' : unreadCount;
    badge.style.display = 'flex';
    if (dot) dot.style.display = 'block';
    if (pageBadge) { pageBadge.textContent = unreadCount; pageBadge.style.display = 'inline-flex'; }
  } else {
    badge.style.display = 'none';
    if (dot) dot.style.display = 'none';
    if (pageBadge) pageBadge.style.display = 'none';
  }
}

function markRead(type, id, el) {
  const row = document.getElementById(`notif-${type}-${id}`);
  if (!row || row.classList.contains('is-read')) return;

  fetch('{{ route("notifikasi.read") }}', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
    body: JSON.stringify({ type, reference_id: id })
  }).then(r => r.json()).then(data => {
    if (!data.success) return;
    row.classList.add('is-read');
    const dot = row.querySelector('.ns-unread-dot');
    if (dot) dot.classList.add('hidden');
    const btn = row.querySelector('.btn-mark-read');
    if (btn) btn.remove();
    updateBadge(-1);
  });
}

function markAllRead() {
  const btn = document.getElementById('btnReadAll');
  btn.disabled = true;
  btn.innerHTML = '<i class="ri-loader-4-line"></i> Memproses…';

  fetch('{{ route("notifikasi.read-all") }}', {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': CSRF }
  }).then(r => r.json()).then(data => {
    if (!data.success) return;
    document.querySelectorAll('.ns-row:not(.is-read)').forEach(row => {
      row.classList.add('is-read');
      const dot = row.querySelector('.ns-unread-dot');
      if (dot) dot.classList.add('hidden');
      const b = row.querySelector('.btn-mark-read');
      if (b) b.remove();
    });
    unreadCount = 0;
    const badge = document.getElementById('totalBadge');
    badge.style.display = 'none';
    btn.innerHTML = '<i class="ri-check-double-line"></i> Semua sudah dibaca';
  }).finally(() => { btn.disabled = false; });
}
</script>
@endpush