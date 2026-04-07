@extends('layouts.master')
@section('title', 'Riwayat Transaksi')
@section('page_title', 'Riwayat Transaksi')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}" />
<style>
/* ─── Stat cards ─────────────────────── */
.rt-stats {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
  margin-bottom: 24px;
}
@media(max-width:768px){ .rt-stats { grid-template-columns: 1fr; } }

.rt-stat {
  background: #fff;
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  padding: 18px 20px;
  display: flex;
  align-items: center;
  gap: 14px;
  transition: box-shadow .2s;
}
.rt-stat:hover { box-shadow: var(--shadow-md); }
.rt-stat-icon {
  width: 44px; height: 44px;
  border-radius: var(--radius-sm);
  display: flex; align-items: center; justify-content: center;
  font-size: 1.15rem; flex-shrink: 0;
}
.rt-stat-icon.navy   { background: var(--navy); color: #fff; }
.rt-stat-icon.teal   { background: var(--teal); color: #fff; }
.rt-stat-icon.purple { background: var(--purple); color: #fff; }
.rt-stat-label { font-size: .78rem; color: var(--text-muted); font-weight: 600; margin-bottom: 3px; }
.rt-stat-val   {
  font-size: 1.25rem; font-weight: 800;
  font-family: 'Syne', sans-serif;
  color: var(--navy);
}
.rt-stat-val.teal   { color: var(--teal); }
.rt-stat-val.purple { color: var(--purple); }

/* ─── Filter bar ─────────────────────── */
.rt-filter {
  display: flex;
  gap: 10px;
  align-items: center;
  margin-bottom: 16px;
  flex-wrap: wrap;
}
.rt-filter-date {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #fff;
  border: 1.5px solid var(--border);
  border-radius: 10px;
  padding: 7px 14px;
  cursor: pointer;
  transition: border-color .2s;
  font-size: .875rem;
  font-weight: 600;
  color: var(--text-muted);
}
.rt-filter-date:hover { border-color: var(--navy); color: var(--navy); }
.rt-filter-date i { font-size: 1rem; }

.rt-search {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #fff;
  border: 1.5px solid var(--border);
  border-radius: 10px;
  padding: 7px 14px;
  flex: 1;
  max-width: 400px;
  transition: border-color .2s, box-shadow .2s;
}
.rt-search:focus-within { border-color: var(--blue); box-shadow: 0 0 0 3px rgba(29,78,216,.1); }
.rt-search i { color: var(--text-muted); flex-shrink: 0; }
.rt-search input {
  border: none; outline: none; background: none;
  font-family: inherit; font-size: .875rem;
  color: var(--text); width: 100%;
}
.rt-search input::placeholder { color: var(--text-muted); }

/* Date filter panel (hidden by default) */
.date-panel {
  display: none;
  background: #fff;
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  padding: 16px 20px;
  margin-bottom: 16px;
}
.date-panel.open { display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-end; }
.date-field { display: flex; flex-direction: column; gap: 5px; }
.date-field label { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; color: var(--text-muted); }

/* ─── Table ──────────────────────────── */
.rt-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-md); overflow: hidden; }
.rt-table { width: 100%; border-collapse: collapse; font-size: .875rem; }
.rt-table thead th {
  background: var(--bg);
  padding: 10px 16px;
  text-align: left;
  font-size: .75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .04em;
  color: var(--text-muted);
  border-bottom: 1px solid var(--border);
  white-space: nowrap;
}
.rt-table tbody tr { border-bottom: 1px solid var(--border); transition: background .12s; }
.rt-table tbody tr:last-child { border-bottom: none; }
.rt-table tbody tr:hover { background: #f8fafc; }
.rt-table td { padding: 12px 16px; vertical-align: middle; }

.meth-badge {
  display: inline-flex; align-items: center;
  padding: 4px 12px; border-radius: 99px;
  font-size: .75rem; font-weight: 700;
  background: var(--bg); color: var(--text-muted);
  border: 1px solid var(--border);
}
.meth-badge.qris     { background: #ede9fe; color: var(--purple); border-color: #ddd6fe; }
.meth-badge.tunai    { background: #d1fae5; color: #065f46; border-color: #a7f3d0; }
.meth-badge.transfer { background: #dbeafe; color: var(--blue); border-color: #bfdbfe; }

.lunas-badge { display:inline-flex; align-items:center; gap:4px; padding:4px 12px; border-radius:99px; font-size:.75rem; font-weight:700; background:var(--navy); color:#fff; }

.rt-action-btns { display: flex; gap: 6px; }
.rt-act-btn {
  width: 30px; height: 30px;
  border-radius: 8px; border: 1.5px solid var(--border);
  background: #fff; cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  font-size: .85rem; color: var(--text-muted);
  transition: all .15s; text-decoration: none;
}
.rt-act-btn:hover { background: var(--navy); color: #fff; border-color: var(--navy); }

.rt-footer {
  padding: 12px 18px;
  border-top: 1px solid var(--border);
  display: flex; align-items: center; justify-content: space-between;
  font-size: .8rem; color: var(--text-muted);
}

/* ─── MODAL ──────────────────────────── */
.mbd { display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:400; align-items:center; justify-content:center; padding:20px; backdrop-filter:blur(4px); }
.mbd.show { display:flex; }
.mbox { background:#fff; border-radius:16px; width:100%; max-width:520px; max-height:92vh; overflow-y:auto; box-shadow:0 24px 64px rgba(0,0,0,.2); animation:mIn .22s ease; }
@keyframes mIn { from{opacity:0;transform:scale(.95) translateY(10px)} to{opacity:1;transform:scale(1) translateY(0)} }

.mhd { display:flex; align-items:center; justify-content:space-between; padding:18px 24px; border-bottom:1px solid var(--border); }
.mhd h3 { font-size:1.05rem; color:var(--navy); font-weight:700; }
.mcls { width:30px; height:30px; border-radius:50%; border:none; background:var(--bg); color:var(--text-muted); cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:1rem; transition:all .15s; }
.mcls:hover { background:var(--navy); color:#fff; }
.mbody { padding:20px 24px 8px; }

/* Info box (blue) */
.modal-info-box {
  background: #e8f0fe;
  border-radius: 10px;
  padding: 16px 18px;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 14px;
  margin-bottom: 20px;
}
.mib-label { font-size: .7rem; color: #4a6fa5; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; margin-bottom: 4px; }
.mib-val   { font-size: .9rem; font-weight: 700; color: var(--navy); }
.mib-status { display:inline-flex; align-items:center; gap:4px; padding:3px 10px; border-radius:99px; font-size:.78rem; font-weight:700; background:var(--navy); color:#fff; }

/* Items */
.modal-section-label { font-size: .8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: .04em; margin-bottom: 10px; }
.modal-item { display:flex; justify-content:space-between; align-items:flex-start; padding:10px 14px; border:1px solid var(--border); border-radius:8px; margin-bottom:8px; }
.modal-item-name { font-weight:700; font-size:.875rem; color:var(--text); }
.modal-item-sub  { font-size:.75rem; color:var(--text-muted); margin-top:2px; }
.modal-item-price { font-weight:700; font-size:.9rem; color:var(--navy); white-space:nowrap; }

/* Totals */
.modal-totals { padding: 12px 0 4px; border-top: 1px solid var(--border); margin-top: 12px; }
.mt-row { display:flex; justify-content:space-between; font-size:.82rem; padding:4px 0; }
.mt-row .ml { color: var(--text-muted); }
.mt-row .mr { font-weight: 600; color: var(--text); }
.mt-row.final { padding-top: 8px; border-top: 1.5px solid var(--border); margin-top: 4px; }
.mt-row.final .ml { font-weight: 700; font-size: .9rem; color: var(--text); }
.mt-row.final .mr { font-weight: 800; font-size: 1.1rem; color: var(--navy); font-family: 'Syne', sans-serif; }

/* Payment section */
.modal-payment { padding: 12px 0 4px; }
.modal-payment-label { font-size: .82rem; font-weight: 700; color: var(--text); margin-bottom: 6px; }
.mp-row { display:flex; justify-content:space-between; font-size:.82rem; padding:3px 0; }
.mp-row .ml { color: var(--text-muted); }
.mp-row .mr { font-weight: 600; }
.mp-row.kembalian .mr { color: var(--teal); }

/* Modal footer */
.mfoot { display:grid; grid-template-columns:1fr 1fr; gap:10px; padding:16px 24px 20px; }
.mfoot button { padding:12px; border-radius:10px; font-family:inherit; font-size:.9rem; font-weight:700; cursor:pointer; transition:all .2s; display:flex; align-items:center; justify-content:center; gap:8px; }
.mfbtn-tutup  { background:#fff; color:var(--text); border:1.5px solid var(--border); }
.mfbtn-tutup:hover { border-color:var(--navy); color:var(--navy); }
.mfbtn-cetak  { background:var(--navy); color:#fff; border:none; }
.mfbtn-cetak:hover { background:var(--navy-mid); }

/* Empty state */
.empty-state { text-align:center; padding:50px 20px; color:var(--text-muted); }
.empty-state i { font-size:3rem; display:block; margin-bottom:12px; opacity:.3; }

/* Print area */
#printArea { display:none; }
@media print {
  body > *:not(#printArea) { display:none !important; }
  #printArea { display:block !important; font-family:monospace; font-size:12px; padding:20px; }
}
</style>
@endpush

@section('content')

{{-- ── STAT CARDS ─────────────────────── --}}
<div class="rt-stats">
  <div class="rt-stat">
    <div class="rt-stat-icon navy"><i class="ri-receipt-line"></i></div>
    <div>
      <div class="rt-stat-label">Total Transaksi</div>
      <div class="rt-stat-val">{{ number_format($totalCount, 0, ',', '.') }}</div>
    </div>
  </div>
  <div class="rt-stat">
    <div class="rt-stat-icon teal"><i class="ri-money-dollar-circle-line"></i></div>
    <div>
      <div class="rt-stat-label">Total Penjualan</div>
      <div class="rt-stat-val teal">Rp {{ number_format($totalPaid, 0, ',', '.') }}</div>
    </div>
  </div>
  <div class="rt-stat">
    <div class="rt-stat-icon purple"><i class="ri-funds-line"></i></div>
    <div>
      <div class="rt-stat-label">Laba Kotor</div>
      <div class="rt-stat-val purple">Rp {{ number_format(round($totalPaid * 0.508), 0, ',', '.') }}</div>
    </div>
  </div>
</div>

{{-- ── FILTER BAR ──────────────────────── --}}
<form method="GET" id="filterForm">
  <div class="rt-filter">
    {{-- Filter tanggal toggle --}}
    <button type="button" class="rt-filter-date" onclick="toggleDatePanel()" id="dateFilterBtn">
      <i class="ri-calendar-line"></i>
      Filter tanggal
      @if(request('date_from') || request('date_to'))
        <span style="background:var(--navy);color:#fff;border-radius:99px;padding:1px 7px;font-size:.68rem;">
          {{ request('date_from') ?? '...' }} → {{ request('date_to') ?? '...' }}
        </span>
      @endif
    </button>

    {{-- Method filter --}}
    <select name="method" class="form-control" style="max-width:160px;" onchange="document.getElementById('filterForm').submit()">
      <option value="">Semua Metode</option>
      <option value="tunai"    {{ request('method') === 'tunai' || request('method') === 'cash' ? 'selected' : '' }}>Tunai</option>
      <option value="qris"     {{ request('method') === 'qris'     ? 'selected' : '' }}>QRIS</option>
      <option value="transfer" {{ request('method') === 'transfer' ? 'selected' : '' }}>Transfer</option>
    </select>

    {{-- Search --}}
    <div class="rt-search" style="margin-left:auto;">
      <i class="ri-search-line"></i>
      <input type="text" name="search"
        placeholder="Cari nama atau kode barang..."
        value="{{ request('search') }}"
        oninput="debSearch()" />
    </div>
  </div>

  {{-- Date panel --}}
  <div class="date-panel {{ request('date_from') || request('date_to') ? 'open' : '' }}" id="datePanel">
    <div class="date-field">
      <label>Dari Tanggal</label>
      <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" />
    </div>
    <div class="date-field">
      <label>Sampai Tanggal</label>
      <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" />
    </div>
    <div class="date-field" style="justify-content:flex-end;">
      <label>&nbsp;</label>
      <div style="display:flex;gap:6px;">
        <button type="submit" class="btn btn-primary btn-sm"><i class="ri-search-line"></i> Terapkan</button>
        <a href="{{ route('transaksi.index') }}" class="btn btn-outline btn-sm">Reset</a>
      </div>
    </div>
  </div>

  {{-- Hidden status filter --}}
  @if(request('status'))
    <input type="hidden" name="status" value="{{ request('status') }}" />
  @endif
</form>

{{-- ── TABLE ───────────────────────────── --}}
<div class="rt-card">
  <div style="overflow-x:auto;">
    <table class="rt-table">
      <thead>
        <tr>
          <th>No. Transaksi</th>
          <th>Tangga/Jam</th>
          <th>Kasir</th>
          <th>Total item</th>
          <th>Total pembayaran</th>
          <th>Metode bayar</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($transactions as $i => $tx)
        @php
          $orderNum = str_pad($transactions->firstItem() + $i, 3, '0', STR_PAD_LEFT);
          $totalItems = $tx->order?->items?->sum('qty') ?? 0;
          $kasirName  = $tx->order?->user?->name ?? 'Admin';
          $payTime    = $tx->paid_at ?? $tx->created_at;
          $meth       = strtolower($tx->payment_method);
          $methLabel  = match($meth) {
            'tunai','cash' => 'Tunai',
            'qris'         => 'QRIS',
            'transfer'     => 'Transfer',
            default        => strtoupper($meth),
          };
          $methClass  = match($meth) {
            'tunai','cash' => 'tunai',
            'qris'         => 'qris',
            'transfer'     => 'transfer',
            default        => '',
          };
        @endphp
        <tr>
          <td style="font-weight:700;font-family:monospace;">{{ $orderNum }}</td>
          <td style="white-space:nowrap;">{{ $payTime->format('d/m/Y H:i') }}</td>
          <td style="font-weight:600;">{{ $kasirName }}</td>
          <td style="text-align:center;font-weight:600;">{{ $totalItems }}</td>
          <td style="font-weight:700;">Rp {{ number_format($tx->amount, 0, ',', '.') }}</td>
          <td>
            <span class="meth-badge {{ $methClass }}">{{ $methLabel }}</span>
          </td>
          <td>
            @if($tx->payment_status === 'paid')
              <span class="lunas-badge"><i class="ri-checkbox-circle-fill" style="font-size:.8rem;"></i> Lunas</span>
            @elseif($tx->payment_status === 'pending')
              <span class="badge badge-orange">Pending</span>
            @elseif($tx->payment_status === 'failed')
              <span class="badge badge-red">Gagal</span>
            @else
              <span class="badge">{{ $tx->payment_status }}</span>
            @endif
          </td>
          <td>
            <div class="rt-action-btns">
              {{-- View detail --}}
              <button class="rt-act-btn" onclick="openDetail({{ $tx->id }})" title="Lihat Detail">
                <i class="ri-eye-line"></i>
              </button>
              {{-- Print --}}
              <button class="rt-act-btn" onclick="printTx({{ $tx->id }})" title="Cetak Struk">
                <i class="ri-printer-line"></i>
              </button>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8">
            <div class="empty-state">
              <i class="ri-file-list-3-line"></i>
              <p>Belum ada transaksi</p>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="rt-footer">
    <span>Menampilkan {{ $transactions->firstItem() ?? 0 }}–{{ $transactions->lastItem() ?? 0 }}
      dari {{ $transactions->total() }} data</span>
    @if($transactions->hasPages())
      {{ $transactions->withQueryString()->links() }}
    @endif
  </div>
</div>


{{-- ════ MODAL: DETAIL TRANSAKSI ════ --}}
<div class="mbd" id="mDetail">
  <div class="mbox">
    <div class="mhd">
      <h3>Detail Transaksi</h3>
      <button class="mcls" onclick="closeDetail()"><i class="ri-close-line"></i></button>
    </div>
    <div class="mbody" id="mDetailBody">
      <div style="text-align:center;padding:30px;color:var(--text-muted);">
        <i class="ri-loader-4-line" style="font-size:2rem;display:block;margin-bottom:8px;animation:spin 1s linear infinite;"></i>
        Memuat...
      </div>
    </div>
    <div class="mfoot" id="mDetailFoot" style="display:none;">
      <button class="mfbtn-tutup" onclick="closeDetail()">Tutup</button>
      <button class="mfbtn-cetak" onclick="doPrintModal()">
        <i class="ri-printer-line"></i> Cetak ulang
      </button>
    </div>
  </div>
</div>

<div id="printArea"></div>
<style>@keyframes spin{from{transform:rotate(0)}to{transform:rotate(360deg)}}</style>
@endsection


@push('scripts')
<script>
// ─── Data from server ────────────────────────────
const TXS = @json($txsForJs);

let currentTxId = null;

// ─── Open detail modal ────────────────────────────
function openDetail(id) {
  currentTxId = id;
  const tx = TXS[id];
  if (!tx) return;

  document.getElementById('mDetail').classList.add('show');
  document.getElementById('mDetailFoot').style.display = 'grid';

  const items   = tx.items || [];
  const subtotal = items.reduce((s, i) => s + i.subtotal, 0);
  const ppnAmt  = tx.amount - subtotal; // amount already includes rounding
  const hasPPN  = ppnAmt > 0 && ppnAmt < subtotal; // sanity check

  const meth   = (tx.payment_method || '').toLowerCase();
  const methLbl = { tunai:'Tunai', cash:'Tunai', qris:'QRIS', transfer:'Transfer' }[meth] || meth.toUpperCase();

  const itemsHtml = items.map(i => `
    <div class="modal-item">
      <div>
        <div class="modal-item-name">${i.name}</div>
        <div class="modal-item-sub">Rp ${parseInt(i.price).toLocaleString('id-ID')} × ${i.qty}</div>
      </div>
      <div class="modal-item-price">Rp ${parseInt(i.subtotal).toLocaleString('id-ID')}</div>
    </div>`).join('');

  // Payment rows
  let payRows = '';
  if (meth === 'tunai' || meth === 'cash') {
    const cashGiven = tx.amount + (tx.change_amount || 0);
    payRows = `
      <div class="mp-row"><span class="ml">Tunai</span><span class="mr">Rp ${cashGiven.toLocaleString('id-ID')}</span></div>
      ${tx.change_amount > 0
        ? `<div class="mp-row kembalian"><span class="ml">Kembalian</span><span class="mr">Rp ${parseInt(tx.change_amount).toLocaleString('id-ID')}</span></div>`
        : ''}`;
  }

  // Laba kotor estimate (51% margin)
  const labaEst = Math.round(subtotal * 0.51);

  document.getElementById('mDetailBody').innerHTML = `
    <div class="modal-info-box">
      <div>
        <div class="mib-label">No. Transaksi</div>
        <div class="mib-val" style="font-family:monospace;font-size:.82rem;">${tx.invoice_code}</div>
      </div>
      <div>
        <div class="mib-label">Tanggal/Jam</div>
        <div class="mib-val" style="font-size:.85rem;">${tx.paid_at}</div>
      </div>
      <div>
        <div class="mib-label">Kasir</div>
        <div class="mib-val">${tx.kasir}${tx.customer_name ? ` / <span style="color:var(--teal)">${tx.customer_name}</span>` : ''}</div>
      </div>
      <div>
        <div class="mib-label">Status</div>
        <div class="mib-val"><span class="mib-status"><i class="ri-checkbox-circle-fill" style="font-size:.8rem;"></i> Lunas</span></div>
      </div>
    </div>

    ${tx.notes ? `<div style="background:#fef9ec;border:1px solid #fde68a;border-radius:8px;padding:8px 14px;font-size:.8rem;color:#92400e;margin-bottom:14px;"><i class="ri-file-text-line"></i> Catatan: ${tx.notes}</div>` : ''}

    <div class="modal-section-label">Item Transaksi</div>
    ${itemsHtml}

    <div class="modal-totals">
      <div class="mt-row"><span class="ml">Sub total</span><span class="mr">Rp ${subtotal.toLocaleString('id-ID')}</span></div>
      ${hasPPN ? `<div class="mt-row"><span class="ml">PPN 11%</span><span class="mr">Rp ${Math.round(subtotal*0.11).toLocaleString('id-ID')}</span></div>` : ''}
      ${ppnAmt !== 0 ? `<div class="mt-row"><span class="ml">Pembulatan</span><span class="mr">${ppnAmt < 0 ? '' : ''}Rp ${Math.abs(tx.amount - subtotal - (hasPPN ? Math.round(subtotal*0.11) : 0)).toLocaleString('id-ID')}</span></div>` : ''}
      <div class="mt-row final">
        <span class="ml">Total pembayaran</span>
        <span class="mr">Rp ${parseInt(tx.amount).toLocaleString('id-ID')}</span>
      </div>
    </div>

    ${payRows ? `<div class="modal-payment"><div class="modal-payment-label">Pembayaran</div>${payRows}</div>` : ''}`;
}

function closeDetail() {
  document.getElementById('mDetail').classList.remove('show');
  currentTxId = null;
}

// ─── Print from modal ─────────────────────────────
function doPrintModal() {
  if (!currentTxId) return;
  buildAndPrint(currentTxId);
}

function printTx(id) {
  currentTxId = id;
  buildAndPrint(id);
}

function buildAndPrint(id) {
  const tx = TXS[id];
  if (!tx) return;

  const items = tx.items || [];
  const subtotal = items.reduce((s,i) => s + i.subtotal, 0);
  const meth = (tx.payment_method||'').toLowerCase();
  const methLbl = {tunai:'Tunai',cash:'Tunai',qris:'QRIS',transfer:'Transfer'}[meth]||meth.toUpperCase();
  const cashGiven = (meth==='tunai'||meth==='cash') ? tx.amount + (tx.change_amount||0) : 0;

  const itemRows = items.map(i => `
    <div style="display:flex;justify-content:space-between;padding:3px 0;">
      <span style="font-weight:600;">${i.name}</span>
      <span>Rp ${parseInt(i.subtotal).toLocaleString('id-ID')}</span>
    </div>
    <div style="font-size:11px;color:#666;">Rp ${parseInt(i.price).toLocaleString('id-ID')} × ${i.qty}</div>`).join('');

  document.getElementById('printArea').innerHTML = `
    <div style="text-align:center;margin-bottom:10px;">
      <div style="font-size:16px;font-weight:800;">🍜 DINE POS</div>
      <div style="font-size:11px;color:#666;">Struk Pembayaran</div>
    </div>
    <hr style="border-top:1px dashed #000;margin:8px 0;" />
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:4px;font-size:11px;">
      <span>Invoice</span><span style="text-align:right;font-weight:700;">${tx.invoice_code}</span>
      <span>Tanggal</span><span style="text-align:right;">${tx.paid_at}</span>
      <span>Kasir</span><span style="text-align:right;">${tx.kasir}</span>
      ${tx.customer_name ? `<span>Pelanggan</span><span style="text-align:right;">${tx.customer_name}</span>` : ''}
    </div>
    ${tx.notes ? `<div style="font-size:10px;color:#666;font-style:italic;margin:4px 0;">Catatan: ${tx.notes}</div>` : ''}
    <hr style="border-top:1px dashed #000;margin:8px 0;" />
    ${itemRows}
    <hr style="border-top:1px dashed #000;margin:8px 0;" />
    <div style="display:flex;justify-content:space-between;font-size:11px;">
      <span>Sub Total</span><span>Rp ${subtotal.toLocaleString('id-ID')}</span>
    </div>
    <div style="display:flex;justify-content:space-between;font-weight:800;font-size:13px;margin-top:4px;">
      <span>Total Bayar</span><span>Rp ${parseInt(tx.amount).toLocaleString('id-ID')}</span>
    </div>
    <div style="display:flex;justify-content:space-between;font-size:11px;">
      <span>Metode</span><span>${methLbl}</span>
    </div>
    ${cashGiven > 0 ? `
    <div style="display:flex;justify-content:space-between;font-size:11px;">
      <span>Tunai</span><span>Rp ${cashGiven.toLocaleString('id-ID')}</span>
    </div>
    <div style="display:flex;justify-content:space-between;font-size:11px;">
      <span>Kembalian</span><span style="color:green;">Rp ${parseInt(tx.change_amount||0).toLocaleString('id-ID')}</span>
    </div>` : ''}
    <hr style="border-top:1px dashed #000;margin:8px 0;" />
    <div style="text-align:center;font-size:10px;color:#666;">
      Terima kasih telah berkunjung!<br>
      DINE POS &copy; {{ date('Y') }}
    </div>`;

  window.print();
}

// ─── Date panel toggle ────────────────────────────
function toggleDatePanel() {
  const panel = document.getElementById('datePanel');
  panel.classList.toggle('open');
}

// ─── Search debounce ──────────────────────────────
let st;
function debSearch() {
  clearTimeout(st);
  st = setTimeout(() => document.getElementById('filterForm').submit(), 500);
}

// ─── Close modal on backdrop click ────────────────
document.getElementById('mDetail').addEventListener('click', function(e) {
  if (e.target === this) closeDetail();
});
</script>
@endpush
