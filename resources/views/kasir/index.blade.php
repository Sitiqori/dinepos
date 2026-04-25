@extends('layouts.master')
@section('title', 'Kasir')
@section('page_title', 'Kasir')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}" />
<style>
/* ═══════════════════════════════════════
   KASIR POS
═══════════════════════════════════════ */
.kasir-wrap {
  display: grid;
  grid-template-columns: 1fr 320px;
  gap: 0;
  height: calc(100vh - 60px);
  overflow: hidden;
}

/* LEFT */
.kasir-left {
  display: flex; flex-direction: column;
  overflow: hidden;
  border-right: 1px solid var(--border);
  background: var(--bg);
}
.kasir-top-bar {
  padding: 16px 20px 12px;
  background: var(--bg);
  border-bottom: 1px solid var(--border);
  flex-shrink: 0;
}
.kasir-search {
  display: flex; align-items: center; gap: 8px;
  background: #fff; border: 1.5px solid var(--border);
  border-radius: 10px; padding: 9px 14px; margin-bottom: 12px;
  transition: border-color .2s, box-shadow .2s;
}
.kasir-search:focus-within { border-color: var(--blue); box-shadow: 0 0 0 3px rgba(29,78,216,.1); }
.kasir-search i { color: var(--text-muted); font-size: .95rem; flex-shrink: 0; }
.kasir-search input { border: none; outline: none; background: none; font-family: inherit; font-size: .875rem; color: var(--text); width: 100%; }
.kasir-search input::placeholder { color: var(--text-muted); }
.kasir-search .sh { font-size: .72rem; color: var(--text-muted); white-space: nowrap; flex-shrink: 0; border: 1px solid var(--border); border-radius: 4px; padding: 1px 5px; }

.cat-tabs { display: flex; gap: 6px; overflow-x: auto; padding-bottom: 2px; }
.cat-tabs::-webkit-scrollbar { height: 0; }
.cat-tab { flex-shrink: 0; padding: 6px 18px; border-radius: 99px; border: 1.5px solid var(--border); background: #fff; font-size: .82rem; font-weight: 600; color: var(--text-muted); cursor: pointer; transition: all .15s; }
.cat-tab.active { background: var(--navy); color: #fff; border-color: var(--navy); }
.cat-tab:hover:not(.active) { border-color: var(--navy); color: var(--navy); }

.kasir-products { flex: 1; overflow-y: auto; padding: 16px 20px; }
.product-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 12px; }
@media(max-width:1300px){ .product-grid { grid-template-columns: repeat(3,1fr); } }
@media(max-width:1000px){ .product-grid { grid-template-columns: repeat(2,1fr); } }

.pcard {
  background: #fff; border: 1.5px solid var(--border);
  border-radius: var(--radius-md); overflow: hidden;
  cursor: pointer; transition: all .2s; position: relative;
}
.pcard:hover { border-color: var(--navy); box-shadow: 0 4px 16px rgba(15,30,60,.12); transform: translateY(-2px); }
.pcard.out-of-stock { opacity: .5; pointer-events: none; }
.pcard.in-cart { border-color: var(--teal); }
.pcard .catbadge { position: absolute; top: 8px; right: 8px; background: var(--navy); color: #fff; font-size: .6rem; font-weight: 700; padding: 2px 7px; border-radius: 99px; }
.pcard img { width: 100%; height: 120px; object-fit: cover; display: block; }
.pcard .pimgph { width: 100%; height: 120px; background: var(--bg); display: flex; align-items: center; justify-content: center; font-size: 2rem; color: var(--text-muted); }
.pcard .pinfo { padding: 10px 12px 12px; }
.pcard .pname { font-weight: 700; font-size: .82rem; color: var(--text); line-height: 1.3; margin-bottom: 1px; }
.pcard .pcode { font-size: .68rem; color: var(--text-muted); margin-bottom: 6px; }
.pcard .pbottom { display: flex; align-items: center; justify-content: space-between; gap: 4px; }
.pcard .pprice { font-size: .88rem; font-weight: 800; color: var(--navy); font-family: 'Poppins',sans-serif; }
.pcard .pstock { font-size: .7rem; font-weight: 600; }
.pcard .pstock.low { color: var(--red); }
.pcard .pstock.ok  { color: var(--text-muted); }
.pcard .paddbtn { width: 28px; height: 28px; border-radius: 8px; background: var(--navy); color: #fff; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 1rem; font-weight: 700; transition: all .15s; flex-shrink: 0; }
.pcard .paddbtn:hover { background: var(--blue); transform: scale(1.1); }

/* In-card qty control (GoFood style) */
.pcard .pqty-ctrl { display: flex; align-items: center; gap: 0; border: 1.5px solid var(--navy); border-radius: 8px; overflow: hidden; flex-shrink: 0; }
.pcard .pqty-ctrl button { width: 26px; height: 26px; border: none; background: var(--navy); color: #fff; cursor: pointer; font-size: .95rem; font-weight: 700; display: flex; align-items: center; justify-content: center; transition: background .12s; flex-shrink: 0; }
.pcard .pqty-ctrl button:hover { background: var(--blue); }
.pcard .pqty-ctrl .pqval { min-width: 24px; text-align: center; font-size: .82rem; font-weight: 800; color: var(--navy); background: #fff; padding: 0 2px; line-height: 26px; }

.no-results { text-align: center; padding: 50px 20px; color: var(--text-muted); }
.no-results i { font-size: 2.5rem; display: block; margin-bottom: 10px; opacity: .3; }

/* RIGHT CART */
.kasir-right { display: flex; flex-direction: column; background: #fff; overflow: hidden; }
.cart-hdr { padding: 14px 18px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; }
.cart-hdr h3 { font-size: .95rem; color: var(--navy); font-weight: 700; }
.ccnt { background: var(--navy); color: #fff; font-size: .72rem; font-weight: 700; padding: 2px 8px; border-radius: 99px; }

.order-type { display: grid; grid-template-columns: 1fr 1fr; gap: 6px; padding: 12px 18px; border-bottom: 1px solid var(--border); flex-shrink: 0; }
.otype-btn { padding: 8px; border-radius: 8px; border: 1.5px solid var(--border); background: #fff; font-family: inherit; font-size: .82rem; font-weight: 600; color: var(--text-muted); cursor: pointer; transition: all .15s; text-align: center; }
.otype-btn.active { background: var(--navy); color: #fff; border-color: var(--navy); }

.cart-lbl { padding: 10px 18px 4px; font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: var(--text-muted); flex-shrink: 0; }
.cart-items { flex: 1; overflow-y: auto; padding: 4px 18px 8px; }
.cart-empty { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px 20px; color: var(--text-muted); gap: 10px; height: 100%; }
.cart-empty i { font-size: 2.5rem; opacity: .35; }
.cart-empty p { font-size: .82rem; text-align: center; }

.citem { background: #fff; border: 1.5px solid var(--border); border-radius: 8px; padding: 10px 12px; margin-bottom: 8px; }
.citem:hover { border-color: var(--navy); }
.citem-name { font-weight: 700; font-size: .875rem; color: var(--text); margin-bottom: 2px; }
.citem-price { font-size: .78rem; color: var(--text-muted); }
.citem-sub { font-size: .88rem; font-weight: 800; color: var(--teal); margin-top: 2px; }
.citem-bot { display: flex; align-items: center; justify-content: space-between; margin-top: 8px; }
.qtyc { display: flex; align-items: center; gap: 8px; }
.qbtn { width: 22px; height: 22px; border-radius: 6px; border: 1.5px solid var(--border); background: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: .85rem; font-weight: 700; transition: all .15s; }
.qbtn:hover { background: var(--navy); color: #fff; border-color: var(--navy); }
.qval { font-weight: 700; font-size: .875rem; min-width: 18px; text-align: center; }
.cdel { width: 22px; height: 22px; border-radius: 6px; border: none; background: #fee2e2; color: var(--red); cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: .8rem; transition: all .15s; }
.cdel:hover { background: var(--red); color: #fff; }

.cart-summ { padding: 12px 18px; border-top: 1px solid var(--border); flex-shrink: 0; }
.srow { display: flex; justify-content: space-between; align-items: center; font-size: .82rem; padding: 3px 0; }
.srow .sl { color: var(--text-muted); }
.srow .sv { font-weight: 600; color: var(--text); }
.ppnrow { display: flex; justify-content: space-between; align-items: center; padding: 5px 0; }
.ppnl { font-size: .82rem; color: var(--text-muted); }
.ppntog { display: flex; align-items: center; gap: 8px; }
.ppnamt { font-size: .82rem; font-weight: 600; color: var(--text); }
.tog { position: relative; width: 36px; height: 20px; }
.tog input { display: none; }
.togsl { position: absolute; inset: 0; background: var(--border); border-radius: 99px; cursor: pointer; transition: background .2s; }
.togsl::after { content: ''; position: absolute; width: 14px; height: 14px; border-radius: 50%; background: #fff; top: 3px; left: 3px; transition: transform .2s; box-shadow: 0 1px 3px rgba(0,0,0,.2); }
.tog input:checked + .togsl { background: var(--teal); }
.tog input:checked + .togsl::after { transform: translateX(16px); }
.sdiv { border: none; border-top: 1.5px solid var(--border); margin: 8px 0; }
.stotal { padding: 6px 0; }
.stotal .sl { font-size: .82rem; font-weight: 600; color: var(--text); }
.stotal .sv { font-size: 1.15rem; font-weight: 800; color: var(--red); font-family: 'Poppins',sans-serif; }

.cart-act { padding: 0 18px 16px; flex-shrink: 0; }
.paybtn { width: 100%; padding: 13px; border-radius: 8px; background: var(--navy); color: #fff; border: none; font-family: inherit; font-size: .95rem; font-weight: 700; cursor: pointer; transition: all .2s; margin-bottom: 8px; display: flex; align-items: center; justify-content: center; gap: 8px; }
.paybtn:hover:not(:disabled) { background: var(--navy-mid); box-shadow: 0 4px 16px rgba(15,30,60,.25); }
.paybtn:disabled { opacity: .45; cursor: not-allowed; }
.clearbtn { width: 100%; padding: 10px; border-radius: 8px; background: #fff; color: var(--red); border: 1.5px solid #fca5a5; font-family: inherit; font-size: .85rem; font-weight: 600; cursor: pointer; transition: all .2s; display: flex; align-items: center; justify-content: center; gap: 6px; }
.clearbtn:hover { background: #fee2e2; }

/* MODALS */
.mbackdrop { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.52); z-index: 500; align-items: center; justify-content: center; padding: 20px; backdrop-filter: blur(4px); }
.mbackdrop.show { display: flex; }
.mbox { background: #fff; border-radius: 16px; width: 100%; max-width: 480px; max-height: 92vh; overflow-y: auto; box-shadow: 0 24px 64px rgba(0,0,0,.22); animation: mIn .24s ease; }
@keyframes mIn { from { opacity:0; transform:scale(.95) translateY(12px); } to { opacity:1; transform:scale(1) translateY(0); } }
.mhdr { display: flex; align-items: center; justify-content: space-between; padding: 18px 24px; border-bottom: 1px solid var(--border); }
.mhdr h3 { font-size: 1.05rem; color: var(--navy); }
.mhdr .msub { font-size: .78rem; color: var(--text-muted); margin-top: 2px; }
.mclose { width: 30px; height: 30px; border-radius: 50%; border: none; background: var(--bg); color: var(--text-muted); cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 1rem; transition: all .15s; }
.mclose:hover { background: var(--navy); color: #fff; }
.mbody { padding: 20px 24px; }

.pay-total-box { background: var(--navy); color: #fff; border-radius: 10px; padding: 14px 20px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.pay-total-box .ptl { font-size: .82rem; opacity: .8; }
.pay-total-box .pta { font-size: 1.2rem; font-weight: 800; font-family: 'Poppins',sans-serif; }

.meth-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 20px; }
.meth-card { display: flex; flex-direction: column; align-items: center; gap: 6px; padding: 16px 12px; border-radius: 10px; border: 2px solid var(--border); background: #fff; cursor: pointer; transition: all .2s; font-size: .82rem; font-weight: 600; color: var(--text-muted); }
.meth-card i { font-size: 1.5rem; }
.meth-card.active { border-color: var(--navy); background: var(--navy); color: #fff; }
.meth-card:hover:not(.active) { border-color: var(--navy); color: var(--navy); }

.mpanel { display: none; }
.mpanel.show { display: block; }

.biginput { width: 100%; padding: 13px 16px; font-size: 1.1rem; font-weight: 800; font-family: 'Poppins',sans-serif; border: 2px solid var(--border); border-radius: 10px; outline: none; text-align: right; transition: border-color .2s; margin-bottom: 10px; }
.biginput:focus { border-color: var(--blue); }
.qcash { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 14px; }
.qcbtn { padding: 5px 11px; border-radius: 8px; border: 1.5px solid var(--border); background: #fff; font-family: inherit; font-size: .75rem; font-weight: 600; cursor: pointer; color: var(--text-muted); transition: all .15s; }
.qcbtn:hover { border-color: var(--navy); color: var(--navy); }
.changebox { background: #ecfdf5; border: 1.5px solid #a7f3d0; border-radius: 10px; padding: 12px 16px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; }
.changebox .cl { font-size: .82rem; font-weight: 600; color: #065f46; }
.changebox .ca { font-size: 1rem; font-weight: 800; color: var(--teal); font-family: 'Poppins',sans-serif; }
.confirmbtn { width: 100%; padding: 14px; border-radius: 10px; background: var(--navy); color: #fff; border: none; font-family: inherit; font-size: .95rem; font-weight: 700; cursor: pointer; transition: all .2s; display: flex; align-items: center; justify-content: center; gap: 8px; }
.confirmbtn:hover { background: var(--navy-mid); }
.confirmbtn:disabled { opacity: .45; cursor: not-allowed; }

/* QRIS */
.qrisbox { text-align: center; padding: 10px 0 16px; }
.qrisbox .qsname { font-weight: 800; font-size: 1rem; color: var(--navy); }
.qrisbox .qsphone { font-size: .8rem; color: var(--text-muted); margin-bottom: 12px; }
.qrisbox .qrimg { width: 180px; height: 180px; margin: 0 auto 12px; border: 1px solid var(--border); border-radius: 8px; padding: 8px; background: #fff; }
.qrisbox .qrimg img { width: 100%; height: 100%; }
.qcountdown { font-size: .85rem; color: var(--text-muted); margin-bottom: 4px; }
.qcountdown strong { color: var(--navy); font-family: 'Poppins',sans-serif; font-size: 1rem; }
.qinfo { font-size: .75rem; color: var(--text-muted); }
.qmeta { display: grid; grid-template-columns: 1fr 1fr; gap: 3px; font-size: .72rem; color: var(--text-muted); margin-top: 10px; }

/* SUCCESS */
.succbox { text-align: center; padding: 10px 24px 8px; }
.succcheck { width: 80px; height: 80px; border-radius: 50%; background: var(--blue); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; margin: 24px auto 16px; animation: popIn .4s cubic-bezier(.34,1.56,.64,1); }
@keyframes popIn { from { transform:scale(0); opacity:0; } to { transform:scale(1); opacity:1; } }
.succtitle { font-size: 1.1rem; font-weight: 700; color: var(--blue); margin-bottom: 8px; }
.succsub { font-size: .82rem; color: var(--text-muted); margin-bottom: 20px; }
.succacts { display: flex; gap: 10px; padding: 0 0 24px; }
.succacts button { flex: 1; padding: 12px; border-radius: 10px; font-family: inherit; font-size: .88rem; font-weight: 700; cursor: pointer; transition: all .2s; }
.sbtnprint { background: var(--navy); color: #fff; border: none; }
.sbtnprint:hover { background: var(--navy-mid); }
.sbtnnew { background: var(--bg); color: var(--text); border: 1.5px solid var(--border); }
.sbtnnew:hover { border-color: var(--navy); color: var(--navy); }

/* RECEIPT */
.rct { padding: 20px 24px 8px; }
.rlogoarea { text-align: center; margin-bottom: 8px; }
.ricon { font-size: 2rem; }
.rstore { font-weight: 800; font-size: .95rem; color: var(--text); }
.rphone { font-size: .75rem; color: var(--text-muted); }
.rdash { border: none; border-top: 1px dashed var(--border); margin: 10px 0; }
.rmeta { display: grid; grid-template-columns: 1fr 1fr; gap: 3px; font-size: .72rem; color: var(--text-muted); margin-bottom: 4px; }
.ritem { display: flex; justify-content: space-between; font-size: .82rem; }
.ritem .rin { font-weight: 700; }
.ritemsub { font-size: .72rem; color: var(--text-muted); padding-bottom: 4px; }
.rsrow { display: flex; justify-content: space-between; font-size: .78rem; padding: 2px 0; color: var(--text-muted); }
.rttl { display: flex; justify-content: space-between; font-size: .88rem; font-weight: 800; padding: 5px 0; }
.rqrarea { text-align: center; margin: 10px 0; }
.rqrarea img { width: 80px; height: 80px; }
.rthanks { text-align: center; font-size: .75rem; color: var(--text-muted); margin-top: 8px; }
.rpromo { text-align: center; font-size: .7rem; color: var(--text-muted); margin-top: 6px; border: 1px dashed var(--border); border-radius: 6px; padding: 5px 8px; }
.ract { display: flex; gap: 8px; padding: 12px 24px 20px; }
.ract button { flex: 1; padding: 11px; border-radius: 10px; font-family: inherit; font-size: .85rem; font-weight: 700; cursor: pointer; border: none; transition: all .2s; }

@media print {
  body > *:not(#printArea) { display: none !important; }
  #printArea { display: block !important; font-family: monospace; font-size: 12px; }
}
#printArea { display: none; }
</style>
@endpush

@section('content')
<style>.page-body { padding: 0 !important; }</style>

<div class="kasir-wrap">

  {{-- LEFT: PRODUCTS --}}
  <div class="kasir-left">
    <div class="kasir-top-bar">
      <div class="kasir-search">
        <i class="ri-search-line"></i>
        <input type="text" id="productSearch" placeholder="Cari produk (F2)" autocomplete="off" />
        <span class="sh">F2</span>
      </div>
      <div class="cat-tabs" id="catTabs">
        <div class="cat-tab active" data-cat="all">Semua produk</div>
        @foreach($categories as $cat)
          <div class="cat-tab" data-cat="{{ $cat->id }}">{{ $cat->name }}</div>
        @endforeach
      </div>
    </div>

    <div class="kasir-products">
      <div class="product-grid" id="productGrid">
        @foreach($products as $p)
        <div class="pcard {{ $p->stock <= 0 ? 'out-of-stock' : '' }}"
             id="pc-{{ $p->id }}"
             data-id="{{ $p->id }}"
             data-name="{{ $p->name }}"
             data-code="{{ $p->sku ?? 'BC-0'.$loop->iteration }}"
             data-price="{{ $p->price }}"
             data-stock="{{ $p->stock }}"
             data-cat="{{ $p->category_id }}"
             data-catname="{{ $p->category?->name ?? 'Produk' }}"
             onclick="addToCart(this)">
          <span class="catbadge">{{ $p->category?->name ?? 'Produk' }}</span>
          @if($p->image)
            <img src="{{ asset('storage/'.$p->image) }}" alt="{{ $p->name }}" loading="lazy" />
          @else
            <div class="pimgph">🍽️</div>
          @endif
          <div class="pinfo">
            <div class="pname">{{ $p->name }}</div>
            <div class="pcode">{{ $p->sku ?? 'BC-0'.$loop->iteration }}</div>
            <div class="pbottom">
              <div>
                <div class="pprice">Rp {{ number_format($p->price,0,',','.') }}</div>
                <div class="pstock {{ $p->stock <= 5 ? 'low' : 'ok' }}">Stok: {{ $p->stock }}</div>
              </div>
              {{-- tombol + akan di-inject JS via updateCardControl() --}}
            </div>
          </div>
        </div>
        @endforeach
      </div>
      <div class="no-results" id="noResults" style="display:none;">
        <i class="ri-search-line"></i><p>Produk tidak ditemukan</p>
      </div>
    </div>
  </div>

  {{-- RIGHT: CART --}}
  <div class="kasir-right">
    <div class="cart-hdr">
      <h3>Ringkasan Pembayaran</h3>
      <span class="ccnt" id="cartCount" style="display:none;">0 Item</span>
    </div>
    <div class="order-type">
      <button class="otype-btn active" id="btnDineIn"   onclick="setOType('dine_in')">Dine In</button>
      <button class="otype-btn"        id="btnTakeAway" onclick="setOType('take_away')">Take Away</button>
    </div>
    <div class="cart-lbl">Item Dipilih</div>
    <div class="cart-items" id="cartItems">
      <div class="cart-empty" id="cartEmpty">
        <i class="ri-shopping-cart-line"></i>
        <p>Keranjang masih kosong</p>
      </div>
    </div>
    <div class="cart-summ">
      <div class="srow"><span class="sl">Sub total</span><span class="sv" id="sSubtotal">Rp 0</span></div>
      <div class="ppnrow">
        <span class="ppnl">PPN 11%</span>
        <div class="ppntog">
          <span class="ppnamt" id="sPPN">Rp 0</span>
          <label class="tog"><input type="checkbox" id="ppnTog" onchange="recalc()" /><span class="togsl"></span></label>
        </div>
      </div>
      <div class="srow"><span class="sl">Total</span><span class="sv" id="sTotal">Rp 0</span></div>
      <div class="srow"><span class="sl">Pembulatan</span><span class="sv" id="sRound">Rp 0</span></div>
      <hr class="sdiv" />
      <div class="srow stotal"><span class="sl">Total Pembayaran</span><span class="sv" id="sFinal">Rp 0</span></div>

      {{-- Customer info --}}
      <hr class="sdiv" />
      <div style="display:flex;flex-direction:column;gap:8px;margin-top:2px;">
        <div class="mfg" style="margin-bottom:0;">
          <label style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);margin-bottom:4px;display:block;">
            <i class="ri-user-line"></i> Nama Pelanggan
          </label>
          <input type="text" id="custName"
            class="form-control" style="padding:7px 10px;font-size:.82rem;"
            placeholder="Opsional..." autocomplete="off" />
        </div>
        <div class="mfg" style="margin-bottom:0;">
          <label style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);margin-bottom:4px;display:block;">
            <i class="ri-file-text-line"></i> Catatan Pesanan
          </label>
          <input type="text" id="custNotes"
            class="form-control" style="padding:7px 10px;font-size:.82rem;"
            placeholder="Contoh: tidak pedas, tanpa bawang..." autocomplete="off" />
        </div>
      </div>
    </div>
    <div class="cart-act">
      <button class="paybtn" id="payBtn" disabled onclick="openPay()">
        <i class="ri-secure-payment-line"></i> Bayar (F9)
      </button>
      <button class="clearbtn" onclick="clearCart()">
        <i class="ri-delete-bin-line"></i> Hapus keranjang
      </button>
    </div>
  </div>
</div>

{{-- ═════ MODAL: PAYMENT ═════ --}}
<div class="mbackdrop" id="mPay">
  <div class="mbox">
    <div class="mhdr">
      <div>
        <h3>Pembayaran</h3>
        <div class="msub" id="mOrderInfo">Order #001 · Dine In</div>
        <div style="font-size:.75rem;color:var(--text-muted);margin-top:2px;">
          Silakan pilih metode pembayaran dan masukkan jumlah yang dibayarkan
        </div>
      </div>
      <button class="mclose" onclick="closeM('mPay')"><i class="ri-close-line"></i></button>
    </div>
    <div class="mbody">
      <div class="pay-total-box">
        <span class="ptl">Total yang harus dibayar:</span>
        <span class="pta" id="mTotal">Rp 0</span>
      </div>
      <p style="font-size:.78rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:var(--text-muted);margin-bottom:10px;">Metode pembayaran</p>
      <div class="meth-grid">
        <div class="meth-card" id="mTunai" onclick="selMeth('tunai')"><i class="ri-money-dollar-circle-line"></i> Tunai</div>
        <div class="meth-card active" id="mQris"  onclick="selMeth('qris')"><i class="ri-qr-code-line"></i> QRIS</div>
      </div>

      {{-- Cash panel --}}
      <div class="mpanel" id="pCash">
        <label style="font-size:.78rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:var(--text-muted);display:block;margin-bottom:8px;">Jumlah bayar</label>
        <input type="number" id="cashIn" class="biginput" placeholder="Rp 0" oninput="calcChg()" />
        <div class="qcash" id="qcashBtns"></div>
        <div class="changebox" id="chgBox" style="display:none;">
          <span class="cl">Kembalian</span>
          <span class="ca" id="chgAmt">Rp 0</span>
        </div>
        <button class="confirmbtn" id="cashPayBtn" onclick="doCash()" disabled>
          <i class="ri-printer-line"></i> Bayar &amp; Cetak Resi
        </button>
      </div>

      {{-- QRIS panel --}}
      <div class="mpanel show" id="pQris">
        <label style="font-size:.78rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:var(--text-muted);display:block;margin-bottom:8px;">Jumlah bayar</label>
        <input type="number" id="qrisIn" class="biginput" placeholder="Rp 0" readonly />
        <button class="confirmbtn" onclick="doQris()">
          <i class="ri-qr-code-line"></i> Generate QR &amp; Bayar
        </button>
      </div>
    </div>
  </div>
</div>

{{-- ═════ MODAL: QRIS QR ═════ --}}
<div class="mbackdrop" id="mQrisQR">
  <div class="mbox" style="max-width:360px;">
    <div class="mhdr">
      <h3>&nbsp;</h3>
      <button class="mclose" onclick="closeM('mQrisQR');clearInterval(qrInt)"><i class="ri-close-line"></i></button>
    </div>
    <div class="mbody" style="padding-top:8px;">
      <div class="qrisbox">
        <div class="ricon">🍜</div>
        <div class="qsname">Resto DimsumGo Jl. xxx</div>
        <div class="qsphone">No. Telp +62 895-3387-2036-8</div>
        <div class="qrimg"><img id="qrImg" src="" alt="QR" /></div>
        <div class="qcountdown">Menunggu pembayaran… <strong id="qrTimer">15</strong>s</div>
        <div class="qinfo">Scan QR code menggunakan aplikasi dompet digital</div>
      </div>
      <div class="qmeta" id="qrMeta"></div>
    </div>
  </div>
</div>

{{-- ═════ MODAL: SUCCESS ═════ --}}
<div class="mbackdrop" id="mSuccess">
  <div class="mbox" style="max-width:360px;">
    <div class="succbox">
      <div class="succcheck"><i class="ri-check-line"></i></div>
      <div class="succtitle">Pembayaran Berhasil</div>
      <div class="succsub" id="succSub">Transaksi telah selesai</div>
    </div>
    <div class="succacts">
      <button class="sbtnprint" onclick="openReceipt()"><i class="ri-printer-line"></i> Cetak Struk</button>
      <button class="sbtnnew"  onclick="newOrder()"><i class="ri-add-line"></i> Transaksi Baru</button>
    </div>
  </div>
</div>

{{-- ═════ MODAL: RECEIPT ═════ --}}
<div class="mbackdrop" id="mReceipt">
  <div class="mbox" style="max-width:400px;">
    <div class="mhdr">
      <h3>Struk Pembayaran</h3>
      <button class="mclose" onclick="closeM('mReceipt')"><i class="ri-close-line"></i></button>
    </div>
    <div class="rct" id="rctContent"></div>
    <div class="ract">
      <button style="background:var(--navy);color:#fff;" onclick="doPrint()"><i class="ri-printer-line"></i> Cetak</button>
      <button style="background:var(--bg);color:var(--text);border:1.5px solid var(--border);" onclick="newOrder()"><i class="ri-add-line"></i> Transaksi Baru</button>
    </div>
  </div>
</div>

<div id="printArea"></div>
@endsection

@push('scripts')
<script>
// ─── STATE ──────────────────────────────────────
let cart = {};
let otype = 'dine_in';
let ordNum = String(Math.floor(Math.random()*900+100)).padStart(3,'0');
let totalFinal = 0;
let lastMeth = 'qris';
let qrInt = null;
let lastReceipt = null;

// ─── CART ────────────────────────────────────────
function addToCart(el) {
  const id = el.dataset.id, name = el.dataset.name,
        price = parseInt(el.dataset.price), stock = parseInt(el.dataset.stock),
        code = el.dataset.code || '';
  if (cart[id]) {
    if (cart[id].qty >= stock) { toast('Stok tidak cukup!','error'); return; }
    cart[id].qty++;
  } else {
    cart[id] = { id, name, code, price, qty: 1, stock };
  }
  el.classList.add('in-cart');
  updateCardControl(id);
  renderCart();
}

function chgQty(id, d) {
  if (!cart[id]) return;
  const stock = cart[id].stock;
  if (d > 0 && cart[id].qty >= stock) { toast('Stok tidak cukup!','error'); return; }
  cart[id].qty += d;
  if (cart[id].qty <= 0) {
    delete cart[id];
    const card = document.querySelector(`[data-id="${id}"]`);
    card?.classList.remove('in-cart');
    updateCardControl(id);
  } else {
    updateCardControl(id);
  }
  renderCart();
}

function rmItem(id) {
  delete cart[id];
  const card = document.querySelector(`[data-id="${id}"]`);
  card?.classList.remove('in-cart');
  updateCardControl(id);
  renderCart();
}

function clearCart() {
  Object.keys(cart).forEach(id => {
    document.querySelector(`[data-id="${id}"]`)?.classList.remove('in-cart');
    updateCardControl(id);
  });
  cart = {};
  renderCart();
}

// Update the + button OR - qty + control on the product card
function updateCardControl(id) {
  const card = document.querySelector(`[data-id="${id}"]`);
  if (!card) return;
  const bottom = card.querySelector('.pbottom');
  const existing = bottom.querySelector('.paddbtn, .pqty-ctrl');
  if (existing) existing.remove();

  if (cart[id]) {
    const ctrl = document.createElement('div');
    ctrl.className = 'pqty-ctrl';
    ctrl.innerHTML = `
      <button onclick="event.stopPropagation();chgQty('${id}',-1)">−</button>
      <span class="pqval" id="pqval-${id}">${cart[id].qty}</span>
      <button onclick="event.stopPropagation();chgQty('${id}',1)">+</button>`;
    bottom.appendChild(ctrl);
  } else {
    const btn = document.createElement('button');
    btn.className = 'paddbtn';
    btn.innerHTML = '<i class="ri-add-line"></i>';
    btn.onclick = function(e) { e.stopPropagation(); addToCart(card); };
    bottom.appendChild(btn);
  }
}

function renderCart() {
  const items = Object.values(cart);
  const count = items.reduce((s,i) => s+i.qty, 0);
  const el = document.getElementById('cartItems');
  const ccnt = document.getElementById('cartCount');

  ccnt.textContent = count + ' Item';
  ccnt.style.display = count > 0 ? '' : 'none';

  if (!items.length) {
    el.innerHTML = '<div class="cart-empty" id="cartEmpty"><i class="ri-shopping-cart-line"></i><p>Keranjang masih kosong</p></div>';
    document.getElementById('payBtn').disabled = true;
  } else {
    el.innerHTML = items.map(i => {
      const sub = i.price * i.qty;
      return `<div class="citem">
        <div class="citem-name">${i.name}</div>
        <div class="citem-price">Rp ${i.price.toLocaleString('id-ID')}</div>
        <div class="citem-sub">Rp ${sub.toLocaleString('id-ID')}</div>
        <div class="citem-bot">
          <div class="qtyc">
            <button class="qbtn" onclick="chgQty('${i.id}',-1)">−</button>
            <span class="qval">${i.qty}</span>
            <button class="qbtn" onclick="chgQty('${i.id}',1)">+</button>
          </div>
          <button class="cdel" onclick="rmItem('${i.id}')"><i class="ri-delete-bin-line"></i></button>
        </div>
      </div>`;
    }).join('');
    document.getElementById('payBtn').disabled = false;
  }
  recalc();
}

function recalc() {
  const items = Object.values(cart);
  const sub = items.reduce((s,i) => s+i.price*i.qty, 0);
  const ppn = document.getElementById('ppnTog').checked ? Math.round(sub*0.11) : 0;
  const tot = sub + ppn;
  const rem = tot % 500;
  const rounded = rem === 0 ? tot : tot + (500-rem);
  const rounding = rounded - tot;
  totalFinal = rounded;

  document.getElementById('sSubtotal').textContent = 'Rp '+sub.toLocaleString('id-ID');
  document.getElementById('sPPN').textContent       = ppn > 0 ? 'Rp '+ppn.toLocaleString('id-ID') : 'Rp 0';
  document.getElementById('sTotal').textContent     = 'Rp '+tot.toLocaleString('id-ID');
  document.getElementById('sRound').textContent     = rounding > 0 ? '- Rp '+rounding.toLocaleString('id-ID') : 'Rp 0';
  document.getElementById('sFinal').textContent     = 'Rp '+rounded.toLocaleString('id-ID');
  if(document.getElementById('mTotal')) document.getElementById('mTotal').textContent = 'Rp '+rounded.toLocaleString('id-ID');
  if(document.getElementById('qrisIn')) document.getElementById('qrisIn').value = rounded;
}

function setOType(t) {
  otype = t;
  document.getElementById('btnDineIn').classList.toggle('active', t==='dine_in');
  document.getElementById('btnTakeAway').classList.toggle('active', t==='take_away');
}

// ─── MODALS ──────────────────────────────────────
function openM(id) { document.getElementById(id).classList.add('show'); }
function closeM(id) { document.getElementById(id).classList.remove('show'); }

function openPay() {
  if (!Object.keys(cart).length) return;
  document.getElementById('mOrderInfo').textContent = `Order #${ordNum} · ${otype==='dine_in'?'Dine In':'Take Away'}`;
  document.getElementById('mTotal').textContent = 'Rp '+totalFinal.toLocaleString('id-ID');
  document.getElementById('qrisIn').value = totalFinal;
  document.getElementById('cashIn').value = '';
  document.getElementById('chgBox').style.display = 'none';
  document.getElementById('cashPayBtn').disabled = true;
  buildQcash();
  selMeth('qris');
  openM('mPay');
}

function selMeth(m) {
  lastMeth = m;
  document.getElementById('mTunai').classList.toggle('active', m==='tunai');
  document.getElementById('mQris').classList.toggle('active', m==='qris');
  document.getElementById('pCash').classList.toggle('show', m==='tunai');
  document.getElementById('pQris').classList.toggle('show', m==='qris');
}

function buildQcash() {
  const t = totalFinal;
  const vals = [...new Set([t, rUp(t,10000), rUp(t,50000), rUp(t,100000)])];
  document.getElementById('qcashBtns').innerHTML = vals.map(v =>
    `<button class="qcbtn" onclick="setCash(${v})">Rp ${v.toLocaleString('id-ID')}</button>`).join('');
}

function rUp(v, m) { return Math.ceil(v/m)*m; }
function setCash(v) { document.getElementById('cashIn').value = v; calcChg(); }

function calcChg() {
  const given = parseInt(document.getElementById('cashIn').value)||0;
  const chg = given - totalFinal;
  document.getElementById('chgBox').style.display = given >= totalFinal ? 'flex' : 'none';
  document.getElementById('chgAmt').textContent = 'Rp '+(Math.max(0,chg)).toLocaleString('id-ID');
  document.getElementById('cashPayBtn').disabled = given < totalFinal;
}

function doCash() {
  const given = parseInt(document.getElementById('cashIn').value)||totalFinal;
  closeM('mPay');
  finishOrder('tunai', given);
}

function doQris() {
  closeM('mPay');
  const qdata = `DINEPOS-${ordNum}-${totalFinal}`;
  document.getElementById('qrImg').src = `https://api.qrserver.com/v1/create-qr-code/?size=164x164&data=${encodeURIComponent(qdata)}&color=0f1e3c`;
  const now = new Date();
  const ts = now.toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit',second:'2-digit'});
  const ds = now.toLocaleDateString('id-ID',{day:'2-digit',month:'2-digit',year:'numeric'});
  document.getElementById('qrMeta').innerHTML = `
    <span>${ds}</span><span style="text-align:right">Admin Qori</span>
    <span>${ts}</span><span style="text-align:right">${otype==='dine_in'?'Dine In':'Take Away'}</span>
    <span>#${ordNum}</span>`;
  openM('mQrisQR');
  let sec = 15;
  document.getElementById('qrTimer').textContent = sec;
  clearInterval(qrInt);
  qrInt = setInterval(() => {
    sec--;
    document.getElementById('qrTimer').textContent = sec;
    if (sec <= 0) {
      clearInterval(qrInt);
      closeM('mQrisQR');
      finishOrder('qris', totalFinal);
    }
  }, 1000);
}

async function finishOrder(meth, cashGiven) {
  const items = Object.values(cart);
  const ppnOn = document.getElementById('ppnTog').checked;
  const sub   = items.reduce((s,i)=>s+i.price*i.qty,0);
  const ppn   = ppnOn ? Math.round(sub*0.11) : 0;

  const custName  = document.getElementById('custName')?.value?.trim() || null;
  const custNotes = document.getElementById('custNotes')?.value?.trim() || null;

  // Save receipt data for printing
  lastReceipt = {
    ordNum, otype, meth, cashGiven,
    items: JSON.parse(JSON.stringify(items)),
    ppnOn, sub, ppn, total: totalFinal,
    custName, custNotes,
    dt: new Date()
  };

  // Show loading state on buttons
  const payBtnEl = document.getElementById('payBtn');
  if (payBtnEl) { payBtnEl.disabled = true; payBtnEl.textContent = 'Menyimpan...'; }

  // POST to Laravel — MUST succeed before showing success modal
  try {
    const payload = {
      _token: '{{ csrf_token() }}',
      order_type: otype,
      payment_method: meth,
      cash_given: cashGiven,
      ppn_on: ppnOn ? 1 : 0,
      customer_name: document.getElementById('custName')?.value?.trim() || null,
      notes: document.getElementById('custNotes')?.value?.trim() || null,
      items: items.map(i => ({ id: i.id, qty: i.qty })),
    };

    const res = await fetch('{{ route("kasir.order") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify(payload),
    });

    // Handle non-JSON response (e.g. 500 error page)
    const contentType = res.headers.get('content-type') || '';
    if (!contentType.includes('application/json')) {
      const text = await res.text();
      console.error('Server error (non-JSON):', res.status, text);
      toast(`Server error ${res.status}. Cek console untuk detail.`, 'error');
      if (payBtnEl) { payBtnEl.disabled = false; payBtnEl.innerHTML = '<i class="ri-secure-payment-line"></i> Bayar (F9)'; }
      return; // STOP — do not show success
    }

    const data = await res.json();

    if (!data.success) {
      toast(data.message || 'Gagal menyimpan transaksi.', 'error');
      console.error('Order failed:', data);
      if (payBtnEl) { payBtnEl.disabled = false; payBtnEl.innerHTML = '<i class="ri-secure-payment-line"></i> Bayar (F9)'; }
      return; // STOP — do not show success
    }

    // SUCCESS — update receipt with real codes from DB
    lastReceipt.invoiceCode = data.invoice_code;
    lastReceipt.orderCode   = data.order_code;
    lastReceipt.ordNum      = data.order_code?.replace('ORD-','') || ordNum;

    // Fix 1: Update stok kartu produk langsung tanpa reload
    if (data.updated_stock) {
      data.updated_stock.forEach(({ id, stock }) => {
        const card = document.querySelector(`[data-id="${id}"]`);
        if (!card) return;
        card.dataset.stock = stock;
        const stockEl = card.querySelector('.pstock');
        if (stockEl) {
          stockEl.textContent = `Stok: ${stock}`;
          stockEl.className   = `pstock ${stock <= 5 ? 'low' : 'ok'}`;
        }
        if (stock <= 0) {
          card.classList.add('out-of-stock');
          card.style.pointerEvents = 'none';
          card.style.opacity = '0.5';
        }
      });
    }

  } catch(err) {
    console.error('Network/fetch error:', err);
    toast('Koneksi gagal. Cek internet dan coba lagi.', 'error');
    if (payBtnEl) { payBtnEl.disabled = false; payBtnEl.innerHTML = '<i class="ri-secure-payment-line"></i> Bayar (F9)'; }
    return; // STOP — do not show success
  }

  // Only reach here if save was successful
  document.getElementById('succSub').textContent =
    `Order #${ordNum} · ${otype==='dine_in'?'Dine In':'Take Away'} · ${meth.toUpperCase()} · Rp ${totalFinal.toLocaleString('id-ID')}`;

  // Toast global langsung tanpa tunggu polling
  if (window.DinePOS?.showToast) {
    window.DinePOS.showToast({
      type:  'success',
      icon:  'ri-checkbox-circle-line',
      title: 'Pembayaran Berhasil 💰',
      msg:   `${data.invoice_code} · Rp ${totalFinal.toLocaleString('id-ID')} · ${meth.toUpperCase()}`,
      duration: 5000,
    });
  }

  openM('mSuccess');
}

function openReceipt() {
  closeM('mSuccess');
  buildReceipt();
  openM('mReceipt');
}

function buildReceipt() {
  const r = lastReceipt; if(!r) return;
  const tot = r.sub + r.ppn;
  const rnd = r.total - tot;
  const dt = r.dt;
  const ds = dt.toLocaleDateString('id-ID',{day:'2-digit',month:'2-digit',year:'numeric'});
  const ts = dt.toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit',second:'2-digit'});

  const ihtml = r.items.map(i=>`
    <div class="ritem"><span class="rin">${i.name}</span><span>Rp ${(i.price*i.qty).toLocaleString('id-ID')}</span></div>
    <div class="ritemsub">Rp ${i.price.toLocaleString('id-ID')} X${i.qty}</div>`).join('');

  const qrHtml = r.meth==='qris'
    ? `<div class="rqrarea"><img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=DINEPOS-${r.ordNum}&color=0f1e3c" /></div>` : '';
  const payHtml = r.meth==='tunai'
    ? `<div class="rsrow"><span>Bayar (Tunai)</span><span>Rp ${r.cashGiven.toLocaleString('id-ID')}</span></div>
       <div class="rsrow"><span>Kembalian</span><span>Rp ${(r.cashGiven-r.total).toLocaleString('id-ID')}</span></div>`
    : `<div class="rsrow"><span>Bayar (QRIS)</span><span>Rp ${r.total.toLocaleString('id-ID')}</span></div>`;

  document.getElementById('rctContent').innerHTML = `
    <div class="rlogoarea"><div class="ricon">🍜</div>
      <div class="rstore">Resto DimsumGo Jl. xxx</div>
      <div class="rphone">No. Telp +62 895-3387-2036-8</div>
    </div>
    <hr class="rdash"/>
    <div class="rmeta">
      <span>${ds}</span><span style="text-align:right">${'{{ auth()->user()->name }}'}</span>
      <span>${ts}</span><span style="text-align:right">${r.otype==='dine_in'?'Dine In':'Take Away'}</span>
      <span>#${r.ordNum}</span>${r.custName ? `<span style="text-align:right">${r.custName}</span>` : '<span></span>'}
    </div>
    ${r.custNotes ? `<div style="font-size:.72rem;color:#666;margin:6px 0;font-style:italic;">Catatan: ${r.custNotes}</div>` : ''}
    <hr class="rdash"/>
    ${ihtml}
    <hr class="rdash"/>
    <div class="rsrow"><span>Total QTY</span><span>${r.items.reduce((s,i)=>s+i.qty,0)}</span></div>
    <div class="rsrow"><span>Sub Total</span><span>Rp ${r.sub.toLocaleString('id-ID')}</span></div>
    ${r.ppnOn?`<div class="rsrow"><span>PPN</span><span>Rp ${r.ppn.toLocaleString('id-ID')}</span></div>`:''}
    <div class="rsrow"><span>Total</span><span>Rp ${tot.toLocaleString('id-ID')}</span></div>
    ${rnd>0?`<div class="rsrow"><span>Pembulatan</span><span>- Rp ${rnd.toLocaleString('id-ID')}</span></div>`:''}
    <div class="rttl"><span>Total Pembayaran</span><span>Rp ${r.total.toLocaleString('id-ID')}</span></div>
    ${payHtml}
    ${qrHtml}
    <div class="rthanks">Terimakasih Telah Berbelanja</div>
    <div class="rpromo">Diskon makan hingga 30%<br>Untuk pembelian lebih dari Rp 100.000</div>`;
}

function doPrint() {
  document.getElementById('printArea').innerHTML = `
    <style>body{font-family:monospace;font-size:12px;}.rdash{border-top:1px dashed #000;margin:8px 0}
    .ritem,.rsrow,.rttl{display:flex;justify-content:space-between;}.rin{font-weight:700}
    .ritemsub{font-size:11px;color:#666}.rlogoarea,.rthanks,.rpromo,.rqrarea{text-align:center}
    .rmeta{display:grid;grid-template-columns:1fr 1fr;gap:2px;font-size:11px}</style>
    ${document.getElementById('rctContent').innerHTML}`;
  window.print();
}

function newOrder() {
  closeM('mReceipt'); closeM('mSuccess');
  clearCart();
  ordNum = String(parseInt(ordNum)+1).padStart(3,'0');

  // Clear customer fields
  const custName  = document.getElementById('custName');
  const custNotes = document.getElementById('custNotes');
  if (custName)  custName.value  = '';
  if (custNotes) custNotes.value = '';

  // Re-enable pay button
  const payBtnEl = document.getElementById('payBtn');
  if (payBtnEl) { payBtnEl.disabled = true; payBtnEl.innerHTML = '<i class="ri-secure-payment-line"></i> Bayar (F9)'; }

  // Refresh page stock data after 500ms so product cards show updated stock
  setTimeout(() => window.location.reload(), 600);
}

// ─── FILTER ──────────────────────────────────────
function filterP() {
  const q = document.getElementById('productSearch').value.toLowerCase();
  const cat = document.querySelector('.cat-tab.active')?.dataset.cat || 'all';
  let v = 0;
  document.querySelectorAll('.pcard').forEach(c => {
    const show = (cat==='all'||c.dataset.cat===cat) && c.dataset.name.toLowerCase().includes(q);
    c.style.display = show ? '' : 'none';
    if(show) v++;
  });
  document.getElementById('noResults').style.display = v===0?'flex':'none';
}

document.querySelectorAll('.cat-tab').forEach(t => {
  t.addEventListener('click', function() {
    document.querySelectorAll('.cat-tab').forEach(x=>x.classList.remove('active'));
    this.classList.add('active');
    filterP();
  });
});
document.getElementById('productSearch').addEventListener('input', filterP);

// ─── KEYBOARD ────────────────────────────────────
document.addEventListener('keydown', e => {
  if(e.key==='F2'){e.preventDefault();document.getElementById('productSearch').focus();}
  if(e.key==='F9'){e.preventDefault();if(!document.getElementById('payBtn').disabled)openPay();}
  if(e.key==='Escape'){['mPay','mQrisQR','mSuccess','mReceipt'].forEach(closeM);clearInterval(qrInt);}
});

// ─── TOAST ───────────────────────────────────────
function toast(msg, type='success') {
  const t = document.createElement('div');
  Object.assign(t.style,{position:'fixed',bottom:'24px',right:'24px',zIndex:'9999',
    background:type==='error'?'#ef4444':'#10b981',color:'#fff',
    padding:'10px 18px',borderRadius:'10px',fontWeight:'600',fontSize:'.875rem',
    boxShadow:'0 4px 16px rgba(0,0,0,.15)'});
  t.textContent=msg; document.body.appendChild(t);
  setTimeout(()=>{t.style.opacity='0';t.style.transition='all .3s';setTimeout(()=>t.remove(),300);},3000);
}

// Init
renderCart();
recalc();
// Render tombol + di semua kartu produk
document.querySelectorAll('.pcard').forEach(card => updateCardControl(card.dataset.id));
</script>
@endpush