@extends('layouts.master')
@section('title', 'Manajemen Produk')
@section('page_title', 'Barang & Stok')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}" />
<style>
/* ─── Page header ────────────────────────── */
.bp-header { display:flex; align-items:center; justify-content:flex-end; gap:10px; margin-bottom:20px; flex-wrap:wrap; }

/* ─── Low stock alert bar ────────────────── */
.low-alert {
  display:flex; align-items:center; gap:10px;
  padding:11px 18px;
  background:#fefce8; border:1.5px solid #fde68a;
  border-radius:var(--radius-sm); margin-bottom:16px;
  font-size:.875rem; font-weight:600; color:#92400e;
}
.low-alert i { font-size:1.1rem; }

/* ─── Filter bar ─────────────────────────── */
.filter-bar { display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin-bottom:16px; }
.filter-bar .form-control { max-width:200px; }
.search-wrap { display:flex; align-items:center; gap:8px; background:#fff; border:1.5px solid var(--border); border-radius:10px; padding:7px 14px; flex:1; max-width:380px; transition:border-color .2s; }
.search-wrap:focus-within { border-color:var(--blue); }
.search-wrap i { color:var(--text-muted); }
.search-wrap input { border:none; outline:none; background:none; font-family:inherit; font-size:.875rem; color:var(--text); width:100%; }
.search-wrap input::placeholder { color:var(--text-muted); }

/* ─── Table ──────────────────────────────── */
.bp-card { background:#fff; border:1px solid var(--border); border-radius:var(--radius-md); overflow:hidden; }
.bp-table-wrap { overflow-x:auto; }
.bp-table { width:100%; border-collapse:collapse; font-size:.875rem; }
.bp-table thead th { background:var(--bg); padding:11px 14px; text-align:left; font-weight:600; font-size:.75rem; text-transform:uppercase; letter-spacing:.04em; color:var(--text-muted); border-bottom:1px solid var(--border); white-space:nowrap; }
.bp-table tbody tr { border-bottom:1px solid var(--border); transition:background .12s; }
.bp-table tbody tr:last-child { border-bottom:none; }
.bp-table tbody tr:hover { background:#f8fafc; }
.bp-table td { padding:11px 14px; vertical-align:middle; }
.bp-table .stock-low { color:var(--red); font-weight:700; }
.bp-table .stock-ok  { color:var(--text); font-weight:600; }
.status-badge { display:inline-flex; align-items:center; padding:4px 12px; border-radius:99px; font-size:.75rem; font-weight:700; }
.status-aktif { background:var(--navy); color:#fff; }
.status-off   { background:var(--bg); color:var(--text-muted); border:1px solid var(--border); }
.tbl-footer { padding:12px 18px; border-top:1px solid var(--border); font-size:.8rem; color:var(--text-muted); display:flex; align-items:center; justify-content:space-between; }

/* ─── Action buttons ─────────────────────── */
.act-btns { display:flex; gap:6px; }
.act-btn { width:28px; height:28px; border-radius:7px; border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:.82rem; transition:all .15s; }
.act-edit { background:#eff6ff; color:var(--blue); }
.act-edit:hover { background:var(--blue); color:#fff; }
.act-del  { background:#fff1f2; color:var(--red); }
.act-del:hover { background:var(--red); color:#fff; }

/* ─── MODALS ──────────────────────────────── */
.mbd { display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:400; align-items:center; justify-content:center; padding:20px; backdrop-filter:blur(4px); }
.mbd.show { display:flex; }
.mbox { background:#fff; border-radius:16px; width:100%; max-width:560px; max-height:92vh; overflow-y:auto; box-shadow:0 24px 64px rgba(0,0,0,.2); animation:mIn .22s ease; }
.mbox-sm { max-width:400px; }
@keyframes mIn { from{opacity:0;transform:scale(.95) translateY(10px)} to{opacity:1;transform:scale(1) translateY(0)} }
.mhd { display:flex; align-items:flex-start; justify-content:space-between; padding:20px 24px 12px; }
.mhd h3 { font-size:1.05rem; color:var(--navy); margin-bottom:3px; }
.mhd .msub { font-size:.8rem; color:var(--text-muted); }
.mcls { width:30px; height:30px; border-radius:50%; border:none; background:var(--bg); color:var(--text-muted); cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:1rem; transition:all .15s; flex-shrink:0; }
.mcls:hover { background:var(--navy); color:#fff; }
.mbody { padding:4px 24px 24px; }

/* Form inside modal */
.mform-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
.mform-full { grid-column:1/-1; }
.mfg { display:flex; flex-direction:column; gap:5px; margin-bottom:2px; }
.mfl { font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; color:var(--text-muted); }
.mfl .req { color:var(--red); }
.mfc { width:100%; padding:9px 13px; border:1.5px solid var(--border); border-radius:8px; font-family:inherit; font-size:.875rem; color:var(--text); outline:none; transition:border-color .2s; }
.mfc:focus { border-color:var(--blue); box-shadow:0 0 0 3px rgba(29,78,216,.1); }
.mfc.err { border-color:var(--red); }
.merr { font-size:.72rem; color:var(--red); margin-top:2px; display:none; }
.merr.show { display:block; }

/* Image upload */
.img-upload-area {
  display:flex; gap:14px; align-items:flex-start;
}
.img-preview-box {
  width:100px; height:100px; border-radius:10px;
  border:2px dashed var(--border);
  display:flex; flex-direction:column; align-items:center; justify-content:center;
  cursor:pointer; overflow:hidden; flex-shrink:0;
  transition:border-color .2s; position:relative;
}
.img-preview-box:hover { border-color:var(--blue); }
.img-preview-box img { width:100%; height:100%; object-fit:cover; }
.img-preview-box .img-ph { display:flex; flex-direction:column; align-items:center; gap:4px; color:var(--text-muted); font-size:.7rem; text-align:center; padding:8px; }
.img-preview-box .img-ph i { font-size:1.5rem; }
.img-hints { font-size:.72rem; color:var(--text-muted); line-height:1.7; }

/* Toggle */
.tog-row { display:flex; align-items:center; gap:10px; margin-top:6px; }
.tog-label { font-size:.875rem; font-weight:500; color:var(--text); }
.tog { position:relative; width:38px; height:22px; }
.tog input { display:none; }
.togsl { position:absolute; inset:0; background:var(--border); border-radius:99px; cursor:pointer; transition:background .2s; }
.togsl::after { content:''; position:absolute; width:16px; height:16px; border-radius:50%; background:#fff; top:3px; left:3px; transition:transform .2s; box-shadow:0 1px 3px rgba(0,0,0,.2); }
.tog input:checked + .togsl { background:var(--teal); }
.tog input:checked + .togsl::after { transform:translateX(16px); }

/* Modal footer */
.mfoot { display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-top:20px; }
.mfoot-btn { padding:12px; border-radius:10px; font-family:inherit; font-size:.9rem; font-weight:700; cursor:pointer; transition:all .2s; border:none; }
.mfoot-cancel { background:var(--bg); color:var(--text); border:1.5px solid var(--border) !important; }
.mfoot-cancel:hover { border-color:var(--navy) !important; color:var(--navy); }
.mfoot-save { background:var(--navy); color:#fff; }
.mfoot-save:hover { background:var(--navy-mid); }
.mfoot-save:disabled { opacity:.5; cursor:not-allowed; }
.mfoot-del { background:var(--red); color:#fff; }
.mfoot-del:hover { background:#dc2626; }

/* Success modal */
.succ-wrap { text-align:center; padding:16px 24px 32px; }
.succ-icon { width:80px; height:80px; border-radius:50%; background:var(--blue); color:#fff; display:flex; align-items:center; justify-content:center; font-size:2.5rem; margin:0 auto 16px; animation:popIn .4s cubic-bezier(.34,1.56,.64,1); }
@keyframes popIn { from{transform:scale(0);opacity:0} to{transform:scale(1);opacity:1} }
.succ-title { font-size:1.1rem; font-weight:700; color:var(--blue); margin-bottom:6px; }
.succ-sub   { font-size:.82rem; color:var(--text-muted); }
</style>
@endpush

@section('content')

{{-- ── PAGE HEADER ──────────────────────────── --}}
<div class="bp-header">
  <div style="flex:1;">
    <h1 style="font-size:1.4rem;color:var(--navy);">Manajemen Produk</h1>
  </div>
  <div style="position:relative;">
    <button class="btn btn-outline" id="exportBtn" onclick="toggleExportMenu()">
      <i class="ri-download-2-line"></i> Ekspor PDF
    </button>
    <div id="exportMenu" style="display:none;position:absolute;right:0;top:40px;background:#fff;border:1px solid var(--border);border-radius:10px;box-shadow:var(--shadow-md);min-width:160px;z-index:10;overflow:hidden;">
      <a href="{{ route('barang.export.pdf') }}" target="_blank" style="display:flex;align-items:center;gap:8px;padding:10px 16px;font-size:.875rem;font-weight:600;color:var(--text);border-bottom:1px solid var(--border);">
        <i class="ri-file-pdf-line" style="color:var(--red);"></i> Export PDF
      </a>
      <a href="{{ route('barang.export.csv') }}" style="display:flex;align-items:center;gap:8px;padding:10px 16px;font-size:.875rem;font-weight:600;color:var(--text);">
        <i class="ri-file-excel-line" style="color:var(--green);"></i> Export CSV/Excel
      </a>
    </div>
  </div>
  <button class="btn btn-primary" onclick="openAddModal()">
    <i class="ri-add-line"></i> Tambah Barang
  </button>
</div>

{{-- ── LOW STOCK ALERT ──────────────────────── --}}
@if($lowStockCount > 0)
<div class="low-alert">
  <i class="ri-error-warning-line"></i>
  {{ $lowStockCount }} barang memiliki stok di bawah minimum
</div>
@endif

{{-- ── FILTER BAR ──────────────────────────── --}}
<div class="filter-bar">
  <select id="filterCat" class="form-control" onchange="applyFilter()">
    <option value="">Semua kategori</option>
    @foreach($categories as $cat)
      <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected':'' }}>{{ $cat->name }}</option>
    @endforeach
  </select>
  <div class="search-wrap" style="flex:1;max-width:400px;">
    <i class="ri-search-line"></i>
    <input type="text" id="searchInput" placeholder="Cari nama atau kode barang..."
      value="{{ request('search') }}" oninput="debSearch()" />
  </div>
</div>

{{-- ── TABLE ───────────────────────────────── --}}
<div class="bp-card">
  <div class="bp-table-wrap">
    <table class="bp-table" id="barangTable">
      <thead>
        <tr>
          <th>Kode barang</th>
          <th>Nama barang</th>
          <th>Kategori</th>
          <th>Satuan</th>
          <th>HPP</th>
          <th>Harga jual</th>
          <th>Stok</th>
          <th>Min. stok</th>
          <th>Kadaluarsa</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody id="barangTbody">
        @forelse($products as $p)
        <tr id="row-{{ $p->id }}">
          <td style="font-family:monospace;font-size:.82rem;font-weight:600;">{{ $p->sku ?? '-' }}</td>
          <td style="font-weight:600;">{{ $p->name }}</td>
          <td>{{ $p->category?->name ?? '-' }}</td>
          <td>{{ $p->unit ?? 'Pcs' }}</td>
          <td>Rp {{ number_format($p->cost_price,0,',','.') }}</td>
          <td style="font-weight:700;">Rp {{ number_format($p->price,0,',','.') }}</td>
          <td class="{{ $p->isLowStock() ? 'stock-low' : 'stock-ok' }}">{{ $p->stock }}</td>
          <td>{{ $p->min_stock ?? 0 }}</td>
          <td>{{ $p->expiry_date ? $p->expiry_date->format('d/m/Y') : '-' }}</td>
          <td>
            <span class="status-badge {{ $p->is_active ? 'status-aktif' : 'status-off' }}">
              {{ $p->is_active ? 'Aktif' : 'Nonaktif' }}
            </span>
          </td>
          <td>
            <div class="act-btns">
              <button class="act-btn act-edit" onclick="openEditModal({{ $p->id }})" title="Edit">
                <i class="ri-pencil-line"></i>
              </button>
              <button class="act-btn act-del" onclick="openDelModal({{ $p->id }}, '{{ addslashes($p->name) }}')" title="Hapus">
                <i class="ri-delete-bin-line"></i>
              </button>
            </div>
          </td>
        </tr>
        @empty
        <tr id="emptyRow">
          <td colspan="10" style="text-align:center;padding:40px;color:var(--text-muted);">
            <i class="ri-inbox-line" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
            Belum ada produk
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="tbl-footer">
    <span id="countLabel">Menampilkan {{ $products->firstItem() }}–{{ $products->lastItem() }} dari {{ $products->total() }} data</span>
    <div>{{ $products->links() }}</div>
  </div>
</div>


{{-- ════ MODAL: TAMBAH BARANG ════ --}}
<div class="mbd" id="mAdd">
  <div class="mbox">
    <div class="mhd">
      <div>
        <h3>Tambah Barang Baru</h3>
        <div class="msub">Masukkan detail barang baru</div>
      </div>
      <button class="mcls" onclick="closeM('mAdd')"><i class="ri-close-line"></i></button>
    </div>
    <div class="mbody">
      <form id="addForm" enctype="multipart/form-data">
        @csrf

        <div class="mform-grid">
          {{-- Name --}}
          <div class="mfg mform-full">
            <label class="mfl">Nama barang <span class="req">*</span></label>
            <input type="text" name="name" class="mfc" placeholder="Contoh: Roti Mochi" />
            <span class="merr" id="addErr_name"></span>
          </div>

          {{-- SKU + Barcode --}}
          <div class="mfg">
            <label class="mfl">Kode barang <span class="req">*</span></label>
            <input type="text" name="sku" class="mfc" placeholder="Contoh: BR-01" />
            <span class="merr" id="addErr_sku"></span>
          </div>
          <div class="mfg">
            <label class="mfl">Barcode (opsional)</label>
            <input type="text" name="barcode" class="mfc" placeholder="Contoh: 00000000" />
          </div>

          {{-- Description --}}
          <div class="mfg mform-full">
            <label class="mfl">Deskripsi (opsional)</label>
            <textarea name="description" class="mfc" rows="2" placeholder="Deskripsi singkat barang (opsional)"></textarea>
          </div>

          {{-- Image --}}
          <div class="mfg mform-full">
            <label class="mfl">Gambar produk (opsional)</label>
            <div class="img-upload-area">
              <label class="img-preview-box" id="addImgBox" for="addImgInput">
                <div class="img-ph"><i class="ri-image-add-line"></i><span>Klik untuk upload</span></div>
              </label>
              <div class="img-hints">
                Format: JPG, PNG, atau GIF<br>
                Ukuran maksimal: 2MB<br>
                Rasio: 1:1 (persegi)
              </div>
            </div>
            <input type="file" name="image" id="addImgInput" accept="image/*" style="display:none;" onchange="previewImg(this,'addImgBox')" />
          </div>

          {{-- Category + Unit --}}
          <div class="mfg">
            <label class="mfl">Kategori <span class="req">*</span></label>
            <select name="category_id" class="mfc">
              <option value="">Pilih kategori</option>
              @foreach($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
              @endforeach
            </select>
            <span class="merr" id="addErr_category_id"></span>
          </div>
          <div class="mfg">
            <label class="mfl">Satuan <span class="req">*</span></label>
            <select name="unit" class="mfc">
              <option value="">Pilih satuan</option>
              <option value="Pcs">Pcs</option>
              <option value="Kg">Kg</option>
              <option value="Gram">Gram</option>
              <option value="Liter">Liter</option>
              <option value="Box">Box</option>
              <option value="Porsi">Porsi</option>
            </select>
            <span class="merr" id="addErr_unit"></span>
          </div>

          {{-- HPP + Harga Jual --}}
          <div class="mfg">
            <label class="mfl">Harga beli (Rp) <span class="req">*</span></label>
            <input type="number" name="cost_price" class="mfc" placeholder="Rp 0" min="0" />
            <span class="merr" id="addErr_cost_price"></span>
          </div>
          <div class="mfg">
            <label class="mfl">Harga jual (Rp) <span class="req">*</span></label>
            <input type="number" name="price" class="mfc" placeholder="Rp 0" min="0" />
            <span class="merr" id="addErr_price"></span>
          </div>

          {{-- Stok + Min stok --}}
          <div class="mfg">
            <label class="mfl">Stok awal <span class="req">*</span></label>
            <input type="number" name="stock" class="mfc" placeholder="Rp 0" min="0" />
            <span class="merr" id="addErr_stock"></span>
          </div>
          <div class="mfg">
            <label class="mfl">Minimum stok <span class="req">*</span></label>
            <input type="number" name="min_stock" class="mfc" placeholder="Rp 0" min="0" />
            <span class="merr" id="addErr_min_stock"></span>
          </div>

          {{-- Kadaluarsa --}}
          <div class="mfg">
            <label class="mfl">Tanggal kadaluarsa (opsional)</label>
            <input type="date" name="expiry_date" class="mfc" />
            <span class="merr" id="addErr_expiry_date"></span>
          </div>

          {{-- Status --}}
          <div class="mfg mform-full">
            <label class="mfl">Status aktif</label>
            <div class="tog-row">
              <label class="tog">
                <input type="checkbox" name="is_active" value="1" checked />
                <span class="togsl"></span>
              </label>
              <span class="tog-label">Aktif</span>
            </div>
          </div>
        </div>

        <div class="mfoot">
          <button type="button" class="mfoot-btn mfoot-cancel" onclick="closeM('mAdd')">Batal</button>
          <button type="submit" class="mfoot-btn mfoot-save" id="addSaveBtn">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>


{{-- ════ MODAL: EDIT BARANG ════ --}}
<div class="mbd" id="mEdit">
  <div class="mbox">
    <div class="mhd">
      <div>
        <h3>Edit Barang</h3>
        <div class="msub">Ubah detail barang</div>
      </div>
      <button class="mcls" onclick="closeM('mEdit')"><i class="ri-close-line"></i></button>
    </div>
    <div class="mbody">
      <form id="editForm" enctype="multipart/form-data">
        @csrf @method('PUT')
        <input type="hidden" id="editId" />

        <div class="mform-grid">
          <div class="mfg mform-full">
            <label class="mfl">Nama barang <span class="req">*</span></label>
            <input type="text" name="name" id="eName" class="mfc" />
            <span class="merr" id="editErr_name"></span>
          </div>
          <div class="mfg">
            <label class="mfl">Kode barang <span class="req">*</span></label>
            <input type="text" name="sku" id="eSku" class="mfc" />
            <span class="merr" id="editErr_sku"></span>
          </div>
          <div class="mfg">
            <label class="mfl">Barcode</label>
            <input type="text" name="barcode" id="eBarcode" class="mfc" />
          </div>
          <div class="mfg mform-full">
            <label class="mfl">Deskripsi</label>
            <textarea name="description" id="eDesc" class="mfc" rows="2"></textarea>
          </div>
          <div class="mfg mform-full">
            <label class="mfl">Gambar produk</label>
            <div class="img-upload-area">
              <label class="img-preview-box" id="editImgBox" for="editImgInput">
                <div class="img-ph"><i class="ri-image-add-line"></i><span>Ganti foto</span></div>
              </label>
              <div class="img-hints">Format: JPG, PNG, atau GIF<br>Ukuran maksimal: 2MB</div>
            </div>
            <input type="file" name="image" id="editImgInput" accept="image/*" style="display:none;" onchange="previewImg(this,'editImgBox')" />
          </div>
          <div class="mfg">
            <label class="mfl">Kategori <span class="req">*</span></label>
            <select name="category_id" id="eCat" class="mfc">
              <option value="">Pilih kategori</option>
              @foreach($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
              @endforeach
            </select>
            <span class="merr" id="editErr_category_id"></span>
          </div>
          <div class="mfg">
            <label class="mfl">Satuan <span class="req">*</span></label>
            <select name="unit" id="eUnit" class="mfc">
              <option value="Pcs">Pcs</option>
              <option value="Kg">Kg</option>
              <option value="Gram">Gram</option>
              <option value="Liter">Liter</option>
              <option value="Box">Box</option>
              <option value="Porsi">Porsi</option>
            </select>
          </div>
          <div class="mfg">
            <label class="mfl">Harga beli (Rp) <span class="req">*</span></label>
            <input type="number" name="cost_price" id="eCost" class="mfc" min="0" />
            <span class="merr" id="editErr_cost_price"></span>
          </div>
          <div class="mfg">
            <label class="mfl">Harga jual (Rp) <span class="req">*</span></label>
            <input type="number" name="price" id="ePrice" class="mfc" min="0" />
            <span class="merr" id="editErr_price"></span>
          </div>
          <div class="mfg">
            <label class="mfl">Stok <span class="req">*</span></label>
            <input type="number" name="stock" id="eStock" class="mfc" min="0" />
            <span class="merr" id="editErr_stock"></span>
          </div>
          <div class="mfg">
            <label class="mfl">Minimum stok <span class="req">*</span></label>
            <input type="number" name="min_stock" id="eMinStock" class="mfc" min="0" />
          </div>
          <div class="mfg">
            <label class="mfl">Tanggal kadaluarsa</label>
            <input type="date" name="expiry_date" id="eExpiryDate" class="mfc" />
          </div>
          <div class="mfg mform-full">
            <label class="mfl">Status aktif</label>
            <div class="tog-row">
              <label class="tog">
                <input type="checkbox" name="is_active" id="eActive" value="1" />
                <span class="togsl"></span>
              </label>
              <span class="tog-label">Aktif</span>
            </div>
          </div>
        </div>

        <div class="mfoot">
          <button type="button" class="mfoot-btn mfoot-cancel" onclick="closeM('mEdit')">Batal</button>
          <button type="submit" class="mfoot-btn mfoot-save" id="editSaveBtn">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>


{{-- ════ MODAL: HAPUS ════ --}}
<div class="mbd" id="mDel">
  <div class="mbox mbox-sm">
    <div class="mhd">
      <div>
        <h3>Hapus Barang</h3>
        <div class="msub" id="delSubtitle">Apakah kamu yakin ingin menghapus?</div>
      </div>
      <button class="mcls" onclick="closeM('mDel')"><i class="ri-close-line"></i></button>
    </div>
    <div class="mbody">
      <div style="background:#fef2f2;border:1.5px solid #fecaca;border-radius:10px;padding:14px 16px;font-size:.875rem;color:#991b1b;margin-bottom:16px;">
        <i class="ri-error-warning-line"></i>
        Data yang dihapus tidak dapat dikembalikan. Barang yang sudah pernah masuk transaksi tidak bisa dihapus.
      </div>
      <input type="hidden" id="delId" />
      <div class="mfoot">
        <button type="button" class="mfoot-btn mfoot-cancel" onclick="closeM('mDel')">Batal</button>
        <button type="button" class="mfoot-btn mfoot-del" onclick="doDelete()" id="delBtn">Hapus</button>
      </div>
    </div>
  </div>
</div>


{{-- ════ MODAL: SUCCESS ════ --}}
<div class="mbd" id="mSucc">
  <div class="mbox mbox-sm">
    <button class="mcls" onclick="closeM('mSucc')" style="position:absolute;top:16px;right:16px;z-index:1;"><i class="ri-close-line"></i></button>
    <div class="succ-wrap">
      <div class="succ-icon"><i class="ri-check-line"></i></div>
      <div class="succ-title" id="succTitle">Barang Berhasil Ditambahkan</div>
      <div class="succ-sub" id="succSub">Data telah disimpan ke database</div>
    </div>
  </div>
</div>
@endsection


@push('scripts')
<script>
const CSRF  = '{{ csrf_token() }}';
const BASE  = '{{ url("/barang") }}';

// ── Modal helpers ──────────────────────────────
function openM(id)  { document.getElementById(id).classList.add('show'); }
function closeM(id) { document.getElementById(id).classList.remove('show'); }

// ── Export dropdown ────────────────────────────
function toggleExportMenu() {
  const m = document.getElementById('exportMenu');
  m.style.display = m.style.display === 'none' ? 'block' : 'none';
}
document.addEventListener('click', e => {
  if (!e.target.closest('#exportBtn') && !e.target.closest('#exportMenu')) {
    document.getElementById('exportMenu').style.display = 'none';
  }
});

// ── Image preview ──────────────────────────────
function previewImg(input, boxId) {
  const file = input.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = e => {
    const box = document.getElementById(boxId);
    box.innerHTML = `<img src="${e.target.result}" alt="preview" style="width:100%;height:100%;object-fit:cover;" />`;
  };
  reader.readAsDataURL(file);
}

// ── Clear form errors ──────────────────────────
function clearErrors(prefix) {
  document.querySelectorAll(`[id^="${prefix}Err_"]`).forEach(el => {
    el.textContent = ''; el.classList.remove('show');
  });
  document.querySelectorAll('.mfc.err').forEach(el => el.classList.remove('err'));
}

function showErrors(prefix, errors) {
  Object.entries(errors).forEach(([field, msgs]) => {
    const errEl = document.getElementById(`${prefix}Err_${field}`);
    const inp   = document.querySelector(`[name="${field}"]`);
    if (errEl) { errEl.textContent = msgs[0]; errEl.classList.add('show'); }
    if (inp)   { inp.classList.add('err'); }
  });
}

// ── Debounce search ────────────────────────────
let searchTimer;
function debSearch() {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(applyFilter, 400);
}

function applyFilter() {
  const search = document.getElementById('searchInput').value;
  const cat    = document.getElementById('filterCat').value;
  const url = new URL(window.location.href);
  url.searchParams.set('search', search);
  url.searchParams.set('category_id', cat);
  url.searchParams.delete('page');
  window.location.href = url.toString();
}

// ── ADD MODAL ──────────────────────────────────
function openAddModal() {
  document.getElementById('addForm').reset();
  document.getElementById('addImgBox').innerHTML =
    '<div class="img-ph"><i class="ri-image-add-line"></i><span>Klik untuk upload</span></div>';
  clearErrors('add');
  openM('mAdd');
}

document.getElementById('addForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  clearErrors('add');
  const btn = document.getElementById('addSaveBtn');
  btn.disabled = true; btn.textContent = 'Menyimpan…';

  const fd = new FormData(this);
  try {
    const res = await fetch(BASE, { method:'POST', headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}, body:fd });

    // If server returned non-JSON (e.g. 500 error page), handle gracefully
    const contentType = res.headers.get('content-type') || '';
    if (!contentType.includes('application/json')) {
      toast('Server error ' + res.status + '. Cek console untuk detail.', 'error');
      console.error('Non-JSON response:', res.status, await res.text());
      return;
    }

    const data = await res.json();

    if (data.success) {
      closeM('mAdd');
      this.reset();
      appendRow(data.product);
      showSucc('Barang Berhasil Ditambahkan', `${data.product.name} telah disimpan ke database.`);
    } else {
      if (data.errors) showErrors('add', data.errors);
      else toast(data.message || 'Gagal menyimpan.', 'error');
    }
  } catch(err) {
    console.error('Fetch error:', err);
    toast('Terjadi kesalahan jaringan. Cek console.', 'error');
  } finally {
    btn.disabled = false; btn.textContent = 'Simpan';
  }
});

// ── EDIT MODAL ─────────────────────────────────
async function openEditModal(id) {
  clearErrors('edit');
  try {
    const res  = await fetch(`${BASE}/${id}`, { headers:{'Accept':'application/json','X-CSRF-TOKEN':CSRF} });
    const p    = await res.json();

    document.getElementById('editId').value         = p.id;
    document.getElementById('eName').value          = p.name;
    document.getElementById('eSku').value           = p.sku || '';
    document.getElementById('eBarcode').value       = p.barcode || '';
    document.getElementById('eDesc').value          = p.description || '';
    document.getElementById('eCat').value           = p.category_id;
    document.getElementById('eUnit').value          = p.unit || 'Pcs';
    document.getElementById('eCost').value          = p.cost_price;
    document.getElementById('ePrice').value         = p.price;
    document.getElementById('eStock').value         = p.stock;
    document.getElementById('eMinStock').value      = p.min_stock || 0;
    document.getElementById('eExpiryDate').value    = p.expiry_date ? p.expiry_date.substring(0,10) : '';
    document.getElementById('eActive').checked      = p.is_active;

    // Image preview
    const imgBox = document.getElementById('editImgBox');
    if (p.image) {
      imgBox.innerHTML = `<img src="/storage/${p.image}" alt="${p.name}" style="width:100%;height:100%;object-fit:cover;" />`;
    } else {
      imgBox.innerHTML = '<div class="img-ph"><i class="ri-image-add-line"></i><span>Ganti foto</span></div>';
    }

    openM('mEdit');
  } catch(err) {
    toast('Gagal memuat data barang.', 'error');
  }
}

document.getElementById('editForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  clearErrors('edit');
  const id  = document.getElementById('editId').value;
  const btn = document.getElementById('editSaveBtn');
  btn.disabled = true; btn.textContent = 'Menyimpan…';

  const fd = new FormData(this);
  fd.append('_method', 'PUT');

  try {
    const res = await fetch(`${BASE}/${id}`, { method:'POST', headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}, body:fd });

    const contentType = res.headers.get('content-type') || '';
    if (!contentType.includes('application/json')) {
      toast('Server error ' + res.status + '. Cek console untuk detail.', 'error');
      console.error('Non-JSON response:', res.status, await res.text());
      return;
    }

    const data = await res.json();

    if (data.success) {
      closeM('mEdit');
      updateRow(data.product);
      showSucc('Barang Berhasil Diperbarui', `${data.product.name} telah diperbarui.`);
    } else {
      if (data.errors) showErrors('edit', data.errors);
      else toast(data.message || 'Gagal memperbarui.', 'error');
    }
  } catch(err) {
    console.error('Fetch error:', err);
    toast('Terjadi kesalahan jaringan.', 'error');
  } finally {
    btn.disabled = false; btn.textContent = 'Simpan Perubahan';
  }
});

// ── DELETE MODAL ───────────────────────────────
function openDelModal(id, name) {
  document.getElementById('delId').value = id;
  document.getElementById('delSubtitle').textContent = `Hapus "${name}"?`;
  openM('mDel');
}

async function doDelete() {
  const id  = document.getElementById('delId').value;
  const btn = document.getElementById('delBtn');
  btn.disabled = true; btn.textContent = 'Menghapus…';

  try {
    const res  = await fetch(`${BASE}/${id}`, { method:'DELETE', headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'} });
    const data = await res.json();

    if (data.success) {
      closeM('mDel');
      const row = document.getElementById(`row-${id}`);
      if (row) { row.style.opacity='0'; row.style.transition='opacity .3s'; setTimeout(()=>row.remove(),300); }
      showSucc('Barang Berhasil Dihapus', 'Data telah dihapus dari database.');
    } else {
      toast(data.message || 'Gagal menghapus.', 'error');
    }
  } catch(err) {
    toast('Terjadi kesalahan.', 'error');
  } finally {
    btn.disabled = false; btn.textContent = 'Hapus';
  }
}

// ── ROW HELPERS ────────────────────────────────
function appendRow(p) {
  const tbody = document.getElementById('barangTbody');
  const empty = document.getElementById('emptyRow');
  if (empty) empty.remove();

  const isLow  = p.stock <= (p.min_stock || 0);
  const tr     = document.createElement('tr');
  tr.id        = `row-${p.id}`;
  tr.innerHTML = buildRow(p, isLow);
  tbody.prepend(tr);
}

function updateRow(p) {
  const row = document.getElementById(`row-${p.id}`);
  if (!row) return;
  const isLow = p.stock <= (p.min_stock || 0);
  row.innerHTML = buildRow(p, isLow);
}

function buildRow(p, isLow) {
  const fmt = n => 'Rp '+parseInt(n).toLocaleString('id-ID');
  const escName = (p.name||'').replace(/'/g,"\\'");
  const expiry = p.expiry_date ? new Date(p.expiry_date).toLocaleDateString('id-ID',{day:'2-digit',month:'2-digit',year:'numeric'}) : '-';
  return `
    <td style="font-family:monospace;font-size:.82rem;font-weight:600;">${p.sku||'-'}</td>
    <td style="font-weight:600;">${p.name}</td>
    <td>${p.category?.name||'-'}</td>
    <td>${p.unit||'Pcs'}</td>
    <td>${fmt(p.cost_price)}</td>
    <td style="font-weight:700;">${fmt(p.price)}</td>
    <td class="${isLow?'stock-low':'stock-ok'}">${p.stock}</td>
    <td>${p.min_stock||0}</td>
    <td>${expiry}</td>
    <td><span class="status-badge ${p.is_active?'status-aktif':'status-off'}">${p.is_active?'Aktif':'Nonaktif'}</span></td>
    <td><div class="act-btns">
      <button class="act-btn act-edit" onclick="openEditModal(${p.id})" title="Edit"><i class="ri-pencil-line"></i></button>
      <button class="act-btn act-del"  onclick="openDelModal(${p.id},'${escName}')" title="Hapus"><i class="ri-delete-bin-line"></i></button>
    </div></td>`;
}

// ── SUCCESS MODAL ──────────────────────────────
function showSucc(title, sub) {
  document.getElementById('succTitle').textContent = title;
  document.getElementById('succSub').textContent   = sub;
  openM('mSucc');
  setTimeout(() => closeM('mSucc'), 2800);
}

// ── TOAST ──────────────────────────────────────
function toast(msg, type='success') {
  const t = document.createElement('div');
  Object.assign(t.style, {
    position:'fixed', bottom:'24px', right:'24px', zIndex:'9999',
    background: type==='error' ? '#ef4444':'#10b981',
    color:'#fff', padding:'10px 18px', borderRadius:'10px',
    fontWeight:'600', fontSize:'.875rem',
    boxShadow:'0 4px 16px rgba(0,0,0,.15)',
  });
  t.textContent = msg;
  document.body.appendChild(t);
  setTimeout(()=>{ t.style.opacity='0'; t.style.transition='opacity .3s'; setTimeout(()=>t.remove(),300); }, 3000);
}
</script>
@endpush