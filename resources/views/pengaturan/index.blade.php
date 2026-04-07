@extends('layouts.master')
@section('title', 'Pengaturan')
@section('page_title', 'Pengaturan')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}" />
<style>
/* ─── Tab switcher ───────────────────── */
.pg-tabs {
  display: flex; gap: 8px; margin-bottom: 24px;
}
.pg-tab {
  display: flex; align-items: center; gap: 8px;
  padding: 10px 22px; border-radius: 10px;
  border: 1.5px solid var(--border); background: #fff;
  font-family: inherit; font-size: .875rem; font-weight: 600;
  color: var(--text-muted); cursor: pointer; transition: all .2s;
}
.pg-tab i { font-size: 1rem; }
.pg-tab.active { background: #e8f0fe; border-color: #93c5fd; color: var(--navy); }
.pg-tab:hover:not(.active) { border-color: var(--navy); color: var(--navy); }

/* ─── Panel ──────────────────────────── */
.pg-panel { display: none; }
.pg-panel.active { display: block; }

/* ─── Section card ───────────────────── */
.pg-card {
  background: #fff; border: 1px solid var(--border);
  border-radius: var(--radius-md); padding: 24px;
  margin-bottom: 16px;
}
.pg-card-title { font-size: 1.05rem; font-weight: 700; color: var(--navy); margin-bottom: 20px; }

/* ─── Form elements ──────────────────── */
.pg-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 18px; }
.pg-group:last-child { margin-bottom: 0; }
.pg-label {
  font-size: .875rem; font-weight: 600; color: var(--text);
}
.pg-label .req { color: var(--red); }
.pg-hint { font-size: .78rem; color: var(--text-muted); margin-top: 3px; }
.pg-input {
  width: 100%; padding: 10px 14px;
  border: 1.5px solid var(--border); border-radius: 8px;
  font-family: inherit; font-size: .875rem; color: var(--text);
  outline: none; transition: border-color .2s, box-shadow .2s;
  background: #fff;
}
.pg-input:focus { border-color: var(--blue); box-shadow: 0 0 0 3px rgba(29,78,216,.1); }
.pg-input::placeholder { color: var(--text-muted); }
.pg-textarea { resize: vertical; min-height: 90px; }
.pg-select { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%2364748b' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; padding-right: 36px; cursor: pointer; }

.pg-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
@media(max-width:640px){ .pg-row { grid-template-columns: 1fr; } }

/* ─── Logo / file upload ─────────────── */
.logo-upload-wrap { display: flex; gap: 16px; align-items: flex-start; }
.logo-preview-box {
  width: 110px; height: 90px; flex-shrink: 0;
  border: 2px dashed var(--border); border-radius: 10px;
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  gap: 4px; cursor: pointer; transition: border-color .2s; overflow: hidden;
}
.logo-preview-box:hover { border-color: var(--blue); }
.logo-preview-box img { width: 100%; height: 100%; object-fit: contain; }
.logo-ph { display: flex; flex-direction: column; align-items: center; gap: 4px; color: var(--text-muted); font-size: .72rem; text-align: center; padding: 8px; }
.logo-ph i { font-size: 1.5rem; }
.logo-hints { font-size: .78rem; color: var(--text-muted); line-height: 1.8; }

/* ─── Toggle switch ──────────────────── */
.toggle-row {
  display: flex; align-items: center; justify-content: space-between;
  background: var(--bg); border: 1px solid var(--border);
  border-radius: 10px; padding: 14px 16px; margin-bottom: 16px;
}
.toggle-row-info .tl { font-size: .875rem; font-weight: 700; color: var(--text); margin-bottom: 2px; }
.toggle-row-info .ts { font-size: .78rem; color: var(--text-muted); }
.tog { position: relative; width: 42px; height: 24px; flex-shrink: 0; }
.tog input { display: none; }
.togsl { position: absolute; inset: 0; background: #cbd5e1; border-radius: 99px; cursor: pointer; transition: background .2s; }
.togsl::after { content: ''; position: absolute; width: 18px; height: 18px; border-radius: 50%; background: #fff; top: 3px; left: 3px; transition: transform .2s; box-shadow: 0 1px 3px rgba(0,0,0,.2); }
.tog input:checked + .togsl { background: var(--blue); }
.tog input:checked + .togsl::after { transform: translateX(18px); }

/* ─── Save button ────────────────────── */
.pg-save-btn {
  display: inline-flex; align-items: center; gap: 8px;
  padding: 10px 24px; border-radius: 8px;
  background: var(--bg); border: 1.5px solid var(--border);
  font-family: inherit; font-size: .875rem; font-weight: 700;
  color: var(--text-muted); cursor: pointer; transition: all .2s;
}
.pg-save-btn:hover { background: var(--navy); color: #fff; border-color: var(--navy); }
.pg-save-btn.primary { background: var(--navy); color: #fff; border-color: var(--navy); }
.pg-save-btn.primary:hover { background: var(--navy-mid); }

/* ─── Success alert ──────────────────── */
.pg-success {
  display: flex; align-items: center; gap: 10px;
  padding: 12px 16px; border-radius: 8px;
  background: #ecfdf5; border: 1px solid #a7f3d0;
  color: #065f46; font-size: .875rem; font-weight: 600;
  margin-bottom: 16px; animation: fadeIn .3s ease;
}
@keyframes fadeIn { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:translateY(0)} }
</style>
@endpush

@section('content')

{{-- ── TAB SWITCHER ────────────────────── --}}
<div class="pg-tabs">
  <button class="pg-tab active" id="tab-profil" onclick="switchTab('profil')">
    <i class="ri-store-2-line"></i> Profil Toko
  </button>
  <button class="pg-tab" id="tab-struk" onclick="switchTab('struk')">
    <i class="ri-receipt-line"></i> Struk &amp; Pajak
  </button>
</div>

{{-- ══════════════════════════════════════
     PANEL 1: PROFIL TOKO
══════════════════════════════════════ --}}
<div class="pg-panel active" id="panel-profil">

  @if(session('success_profil'))
    <div class="pg-success"><i class="ri-checkbox-circle-line"></i> {{ session('success_profil') }}</div>
  @endif

  <form action="{{ route('pengaturan.profil') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="pg-card">
      <div class="pg-card-title">Informasi Toko</div>

      {{-- Nama Toko --}}
      <div class="pg-group">
        <label class="pg-label">Nama toko <span class="req">*</span></label>
        <input type="text" name="store_name" class="pg-input"
          placeholder="Contoh: Sumber Rezeki 5"
          value="{{ $settings['store_name'] ?? '' }}" />
        @error('store_name')<div class="pg-hint" style="color:var(--red);">{{ $message }}</div>@enderror
      </div>

      {{-- Alamat --}}
      <div class="pg-group">
        <label class="pg-label">Alamat lengkap <span class="req">*</span></label>
        <textarea name="store_address" class="pg-input pg-textarea"
          placeholder="Contoh: Jl. Singaparna">{{ $settings['store_address'] ?? '' }}</textarea>
        @error('store_address')<div class="pg-hint" style="color:var(--red);">{{ $message }}</div>@enderror
      </div>

      {{-- Phone + WA --}}
      <div class="pg-row">
        <div class="pg-group">
          <label class="pg-label">Nomor telepon <span class="req">*</span></label>
          <input type="text" name="store_phone" class="pg-input"
            placeholder="+62"
            value="{{ $settings['store_phone'] ?? '' }}" />
          @error('store_phone')<div class="pg-hint" style="color:var(--red);">{{ $message }}</div>@enderror
        </div>
        <div class="pg-group">
          <label class="pg-label">Nomor WhatsApp <span class="req">*</span></label>
          <input type="text" name="store_whatsapp" class="pg-input"
            placeholder="+62"
            value="{{ $settings['store_whatsapp'] ?? '' }}" />
          @error('store_whatsapp')<div class="pg-hint" style="color:var(--red);">{{ $message }}</div>@enderror
        </div>
      </div>

      {{-- NPWP --}}
      <div class="pg-group">
        <label class="pg-label">NPWP (opsional)</label>
        <input type="text" name="store_npwp" class="pg-input"
          placeholder="Contoh: 01.000.000.000"
          value="{{ $settings['store_npwp'] ?? '' }}" />
      </div>

      {{-- Logo --}}
      <div class="pg-group">
        <label class="pg-label">Logo toko (opsional)</label>
        <div class="logo-upload-wrap">
          <label class="logo-preview-box" id="logoBox" for="logoInput">
            @if(!empty($settings['store_logo']))
              <img src="{{ asset('storage/'.$settings['store_logo']) }}" alt="Logo" />
            @else
              <div class="logo-ph"><i class="ri-image-add-line"></i><span>Klik untuk upload</span></div>
            @endif
          </label>
          <div class="logo-hints">
            Format: JPG, PNG, atau SVG<br>
            Ukuran maksimal: 2MB
          </div>
        </div>
        <input type="file" name="store_logo" id="logoInput" accept="image/*" style="display:none;"
          onchange="previewLogo(this)" />
      </div>

      <div style="margin-top:8px;">
        <button type="submit" class="pg-save-btn primary">
          <i class="ri-save-line"></i> Simpan Profil Toko
        </button>
      </div>
    </div>
  </form>
</div>


{{-- ══════════════════════════════════════
     PANEL 2: STRUK & PAJAK
══════════════════════════════════════ --}}
<div class="pg-panel" id="panel-struk">

  @if(session('success_pajak'))
    <div class="pg-success"><i class="ri-checkbox-circle-line"></i> {{ session('success_pajak') }}</div>
  @endif
  @if(session('success_format'))
    <div class="pg-success"><i class="ri-checkbox-circle-line"></i> {{ session('success_format') }}</div>
  @endif
  @if(session('success_printer'))
    <div class="pg-success"><i class="ri-checkbox-circle-line"></i> {{ session('success_printer') }}</div>
  @endif

  {{-- ── Pengaturan Pajak ─────────────── --}}
  <form action="{{ route('pengaturan.pajak') }}" method="POST">
    @csrf
    <div class="pg-card">
      <div class="pg-card-title">Pengaturan Pajak</div>

      {{-- PPN Toggle --}}
      <div class="toggle-row">
        <div class="toggle-row-info">
          <div class="tl">PPN 11%</div>
          <div class="ts">Aktifkan PPN secara default pada transaksi baru</div>
        </div>
        <label class="tog">
          <input type="checkbox" name="ppn_enabled" value="1"
            {{ ($settings['ppn_enabled'] ?? '1') === '1' ? 'checked' : '' }} />
          <span class="togsl"></span>
        </label>
      </div>

      {{-- Max Diskon --}}
      <div class="pg-group">
        <label class="pg-label">Diskon Maksimal (%)</label>
        <input type="number" name="max_discount" class="pg-input"
          placeholder="Contoh: 50"
          min="0" max="100"
          value="{{ $settings['max_discount'] ?? '50' }}" />
        <div class="pg-hint">Batas maksimal diskon yang bisa diberikan kasir</div>
      </div>

      {{-- Pembulatan --}}
      <div class="pg-group">
        <label class="pg-label">Pembulatan Harga</label>
        <select name="rounding" class="pg-input pg-select">
          <option value="none"  {{ ($settings['rounding'] ?? 'none') === 'none'  ? 'selected':'' }}>Tidak ada pembulatan</option>
          <option value="100"   {{ ($settings['rounding'] ?? '') === '100'   ? 'selected':'' }}>Bulatkan ke 100</option>
          <option value="500"   {{ ($settings['rounding'] ?? '') === '500'   ? 'selected':'' }}>Bulatkan ke 500</option>
          <option value="1000"  {{ ($settings['rounding'] ?? '') === '1000'  ? 'selected':'' }}>Bulatkan ke 1.000</option>
        </select>
      </div>

      <button type="submit" class="pg-save-btn">
        Simpan Pengaturan Pajak
      </button>
    </div>
  </form>

  {{-- ── Nomor Dokumen ────────────────── --}}
  <form action="{{ route('pengaturan.format') }}" method="POST">
    @csrf
    <div class="pg-card">
      <div class="pg-card-title">Nomor Dokumen</div>

      {{-- Format nomor --}}
      <div class="pg-group">
        <label class="pg-label">Format Nomor Transaksi</label>
        <input type="text" name="invoice_format" class="pg-input"
          placeholder="Contoh: DD-MM-YYYY"
          value="{{ $settings['invoice_format'] ?? 'DD-MM-YYYY' }}" />
        <div class="pg-hint">(DD) = Tanggal, (MM) = Bulan, (YYYY) = Tahun</div>
      </div>

      {{-- Reset nomor --}}
      <div class="pg-group">
        <label class="pg-label">Reset Nomor Urut</label>
        <select name="invoice_reset" class="pg-input pg-select">
          <option value="daily"   {{ ($settings['invoice_reset'] ?? 'monthly') === 'daily'   ? 'selected':'' }}>Reset setiap hari</option>
          <option value="monthly" {{ ($settings['invoice_reset'] ?? 'monthly') === 'monthly' ? 'selected':'' }}>Reset setiap bulan</option>
          <option value="yearly"  {{ ($settings['invoice_reset'] ?? 'monthly') === 'yearly'  ? 'selected':'' }}>Reset setiap tahun</option>
          <option value="never"   {{ ($settings['invoice_reset'] ?? 'monthly') === 'never'   ? 'selected':'' }}>Tidak pernah reset</option>
        </select>
      </div>

      <button type="submit" class="pg-save-btn">
        Simpan Format Nomor
      </button>
    </div>
  </form>

  {{-- ── Printer ──────────────────────── --}}
  <form action="{{ route('pengaturan.printer') }}" method="POST">
    @csrf
    <div class="pg-card">
      <div class="pg-card-title">Printer</div>

      {{-- Tipe Printer --}}
      <div class="pg-group">
        <label class="pg-label">Tipe Printer</label>
        <select name="printer_type" class="pg-input pg-select">
          <option value="thermal_58" {{ ($settings['printer_type'] ?? 'thermal_80') === 'thermal_58' ? 'selected':'' }}>Thermal 58mm</option>
          <option value="thermal_80" {{ ($settings['printer_type'] ?? 'thermal_80') === 'thermal_80' ? 'selected':'' }}>Thermal 80mm</option>
          <option value="a4"         {{ ($settings['printer_type'] ?? '') === 'a4'         ? 'selected':'' }}>Printer A4</option>
        </select>
      </div>

      {{-- Jumlah Cetak --}}
      <div class="pg-group">
        <label class="pg-label">Jumlah Cetak</label>
        <input type="number" name="printer_copies" class="pg-input"
          placeholder="Contoh: 5"
          min="1" max="5"
          value="{{ $settings['printer_copies'] ?? '1' }}"
          style="max-width:120px;" />
      </div>

      {{-- Auto print toggle --}}
      <div class="toggle-row">
        <div class="toggle-row-info">
          <div class="tl">Cetak Otomatis</div>
          <div class="ts">Cetak struk secara otomatis setelah pembayaran</div>
        </div>
        <label class="tog">
          <input type="checkbox" name="auto_print" value="1"
            {{ ($settings['auto_print'] ?? '1') === '1' ? 'checked' : '' }} />
          <span class="togsl"></span>
        </label>
      </div>

      <div style="display:flex;gap:8px;">
        <button type="submit" class="pg-save-btn">
          Simpan Format Nomor
        </button>
        <button type="button" class="pg-save-btn" onclick="window.print()">
          <i class="ri-printer-line"></i> Tes Cetak
        </button>
      </div>
    </div>
  </form>

</div>
@endsection

@push('scripts')
<script>
// ─── Tab switcher ─────────────────────────────
function switchTab(tab) {
  // Update buttons
  document.querySelectorAll('.pg-tab').forEach(t => t.classList.remove('active'));
  document.getElementById('tab-' + tab).classList.add('active');

  // Update panels
  document.querySelectorAll('.pg-panel').forEach(p => p.classList.remove('active'));
  document.getElementById('panel-' + tab).classList.add('active');

  // Persist tab in URL hash
  history.replaceState(null, '', '#' + tab);
}

// ─── Restore tab from URL hash ────────────────
document.addEventListener('DOMContentLoaded', () => {
  const hash = window.location.hash.replace('#', '');
  if (hash === 'struk') switchTab('struk');

  // Also restore if there's a success message from struk panel
  @if(session('success_pajak') || session('success_format') || session('success_printer'))
    switchTab('struk');
  @endif
});

// ─── Logo preview ─────────────────────────────
function previewLogo(input) {
  const file = input.files[0]; if (!file) return;
  const reader = new FileReader();
  reader.onload = e => {
    const box = document.getElementById('logoBox');
    box.innerHTML = `<img src="${e.target.result}" alt="Logo" />`;
  };
  reader.readAsDataURL(file);
}

// ─── Auto-dismiss success alerts ─────────────
document.querySelectorAll('.pg-success').forEach(el => {
  setTimeout(() => {
    el.style.transition = 'opacity .4s ease';
    el.style.opacity = '0';
    setTimeout(() => el.remove(), 400);
  }, 4000);
});
</script>
@endpush
