@extends('layouts.master')
@section('title', 'Manajemen Admin')
@section('page_title', 'Manajemen Admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}" />
<style>
/* ─── Stat cards ─────────────────────── */
.am-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:24px; }
@media(max-width:768px){ .am-stats { grid-template-columns:1fr; } }
.am-stat {
  background:#fff; border:1px solid var(--border);
  border-radius:var(--radius-md); padding:16px 20px;
  display:flex; align-items:center; gap:14px;
}
.am-stat-icon { width:42px; height:42px; border-radius:var(--radius-sm); background:var(--navy); color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.1rem; flex-shrink:0; }
.am-stat-label { font-size:.82rem; color:var(--text-muted); font-weight:600; }
.am-stat-val   { font-size:1.6rem; font-weight:800; color:var(--navy); font-family:'Poppins',sans-serif; line-height:1; margin-top:2px; }

/* ─── Filter tabs + toolbar ──────────── */
.am-toolbar { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:20px; flex-wrap:wrap; }
.am-tabs { display:flex; gap:0; border:1.5px solid var(--border); border-radius:10px; overflow:hidden; }
.am-tab { padding:8px 20px; background:#fff; color:var(--text-muted); font-size:.82rem; font-weight:600; cursor:pointer; border:none; font-family:inherit; transition:all .15s; border-right:1px solid var(--border); }
.am-tab:last-child { border-right:none; }
.am-tab.active { background:var(--navy); color:#fff; }
.am-tab:hover:not(.active) { background:var(--bg); color:var(--navy); }

/* ─── User card rows ─────────────────── */
.user-list { background:#fff; border:1px solid var(--border); border-radius:var(--radius-md); overflow:hidden; }
.user-row {
  display:flex; align-items:center; gap:14px;
  padding:16px 20px; border-bottom:1px solid var(--border);
  transition:background .12s;
}
.user-row:last-child { border-bottom:none; }
.user-row.inactive { background:#fff5f5; }
.user-row:hover { background:#f8fafc; }
.user-row.inactive:hover { background:#fff0f0; }

.ur-check { width:18px; height:18px; border-radius:4px; border:1.5px solid var(--border); cursor:pointer; accent-color:var(--navy); flex-shrink:0; }

/* Avatar */
.ur-avatar {
  width:52px; height:52px; border-radius:50%; flex-shrink:0;
  object-fit:cover; border:2px solid var(--border);
}
.ur-avatar-ph {
  width:52px; height:52px; border-radius:50%;
  background:var(--navy); color:#fff; display:flex;
  align-items:center; justify-content:center;
  font-size:.9rem; font-weight:700; flex-shrink:0;
  border:2px solid var(--border);
}

.ur-body { flex:1; min-width:0; }
.ur-name { font-weight:700; font-size:.925rem; color:var(--navy); margin-bottom:2px; }
.ur-phone { font-size:.8rem; color:var(--text-muted); margin-bottom:1px; }
.ur-role  { font-size:.78rem; color:var(--text-muted); }
.ur-role .role-label { font-weight:600; }
.ur-role .role-label.aktif { color:var(--teal); }
.ur-role .role-label.nonaktif { color:var(--red); }
.ur-branch { font-size:.78rem; color:var(--text-muted); margin-top:1px; }

.ur-actions { display:flex; gap:8px; flex-shrink:0; flex-wrap:wrap; justify-content:flex-end; }

/* Action buttons */
.btn-detail-ur {
  padding:7px 18px; border-radius:8px;
  background:var(--navy); color:#fff;
  border:none; font-family:inherit; font-size:.8rem; font-weight:600;
  cursor:pointer; transition:all .15s; white-space:nowrap;
}
.btn-detail-ur:hover { background:var(--navy-mid); }

.btn-aktif {
  padding:7px 16px; border-radius:8px;
  background:#22c55e; color:#fff;
  border:none; font-family:inherit; font-size:.8rem; font-weight:700;
  cursor:pointer; transition:all .15s; white-space:nowrap;
}
.btn-aktif:hover { background:#16a34a; }

.btn-nonaktif {
  padding:7px 16px; border-radius:8px;
  background:var(--red); color:#fff;
  border:none; font-family:inherit; font-size:.8rem; font-weight:700;
  cursor:pointer; transition:all .15s; white-space:nowrap;
}
.btn-nonaktif:hover { background:#dc2626; }

/* Empty */
.empty-state { text-align:center; padding:50px 20px; color:var(--text-muted); }
.empty-state i { font-size:3rem; display:block; margin-bottom:12px; opacity:.3; }

/* ════════════════════════════════════
   MODALS shared
════════════════════════════════════ */
.mbd { display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:400; align-items:center; justify-content:center; padding:20px; backdrop-filter:blur(4px); }
.mbd.show { display:flex; }
.mbox { background:#fff; border-radius:16px; width:100%; max-width:540px; max-height:92vh; overflow-y:auto; box-shadow:0 24px 64px rgba(0,0,0,.2); animation:mIn .22s ease; }
@keyframes mIn { from{opacity:0;transform:scale(.95) translateY(10px)} to{opacity:1;transform:scale(1) translateY(0)} }
.mhd { display:flex; align-items:center; justify-content:space-between; padding:18px 24px; border-bottom:1px solid var(--border); }
.mhd h3 { font-size:1.05rem; color:var(--navy); font-weight:700; }
.mcls { width:30px; height:30px; border-radius:50%; border:none; background:var(--bg); color:var(--text-muted); cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:1rem; transition:all .15s; flex-shrink:0; }
.mcls:hover { background:var(--navy); color:#fff; }
.mbody { padding:20px 24px; }

/* ─── DETAIL MODAL ───────────────────── */
.detail-info-card {
  background:#e8f0fe; border-radius:10px;
  padding:16px 18px;
  display:grid; grid-template-columns:1fr 1fr auto;
  gap:14px; margin-bottom:16px; align-items:start;
}
.dic-field .dic-label { font-size:.7rem; color:#4a6fa5; font-weight:700; text-transform:uppercase; letter-spacing:.04em; margin-bottom:3px; }
.dic-field .dic-val   { font-size:.88rem; font-weight:700; color:var(--navy); }
.dic-status-aktif    { display:inline-block; padding:3px 12px; background:#22c55e; color:#fff; border-radius:99px; font-size:.78rem; font-weight:700; }
.dic-status-nonaktif { display:inline-block; padding:3px 12px; background:var(--red); color:#fff; border-radius:99px; font-size:.78rem; font-weight:700; }
.dic-badge { display:inline-block; padding:3px 12px; background:var(--navy); color:#fff; border-radius:99px; font-size:.78rem; font-weight:700; }

.detail-photo {
  width:72px; height:72px; border-radius:50%;
  object-fit:cover; border:2px solid #fff;
  box-shadow:0 2px 8px rgba(0,0,0,.15);
}
.detail-photo-ph {
  width:72px; height:72px; border-radius:50%;
  background:var(--navy); color:#fff;
  display:flex; align-items:center; justify-content:center;
  font-size:1.2rem; font-weight:700;
  border:2px solid #fff; box-shadow:0 2px 8px rgba(0,0,0,.15);
  flex-shrink:0;
}

.detail-section-label { font-size:.8rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.04em; margin-bottom:10px; }
.detail-extra-box { background:var(--bg); border-radius:10px; padding:14px 16px; }
.de-row { display:flex; flex-direction:column; gap:3px; margin-bottom:12px; }
.de-row:last-child { margin-bottom:0; }
.de-label { font-size:.72rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.04em; }
.de-val   { font-size:.88rem; font-weight:600; color:var(--text); }

/* Modal footer */
.mfoot { display:grid; grid-template-columns:1fr 1fr; gap:10px; padding:0 24px 20px; }
.mfoot button { padding:12px; border-radius:10px; font-family:inherit; font-size:.9rem; font-weight:700; cursor:pointer; transition:all .2s; display:flex; align-items:center; justify-content:center; gap:6px; }
.mfbtn-tutup    { background:#fff; color:var(--text); border:1.5px solid var(--border); }
.mfbtn-tutup:hover { border-color:var(--navy); color:var(--navy); }
.mfbtn-nonaktif { background:var(--red); color:#fff; border:none; }
.mfbtn-nonaktif:hover { background:#dc2626; }
.mfbtn-aktif    { background:#22c55e; color:#fff; border:none; }
.mfbtn-aktif:hover { background:#16a34a; }

/* ─── TAMBAH / EDIT MODAL ────────────── */
.mform-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
.mform-full { grid-column:1/-1; }
.mfg { display:flex; flex-direction:column; gap:5px; }
.mfl { font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; color:var(--text-muted); }
.mfl .req { color:var(--red); }
.mfc { width:100%; padding:9px 13px; border:1.5px solid var(--border); border-radius:8px; font-family:inherit; font-size:.875rem; color:var(--text); outline:none; transition:border-color .2s; }
.mfc:focus { border-color:var(--blue); box-shadow:0 0 0 3px rgba(29,78,216,.1); }
.mfc.err { border-color:var(--red); }
.merr { font-size:.72rem; color:var(--red); margin-top:2px; display:none; }
.merr.show { display:block; }

/* Photo upload */
.photo-upload-area {
  display:flex; gap:14px; align-items:flex-start;
}
.photo-preview-box {
  width:90px; height:90px; border-radius:50%;
  border:2px dashed var(--border);
  display:flex; flex-direction:column; align-items:center; justify-content:center;
  cursor:pointer; overflow:hidden; flex-shrink:0;
  transition:border-color .2s;
}
.photo-preview-box:hover { border-color:var(--blue); }
.photo-preview-box img { width:100%; height:100%; object-fit:cover; border-radius:50%; }
.photo-preview-box .ph-icon { display:flex; flex-direction:column; align-items:center; gap:3px; color:var(--text-muted); font-size:.65rem; text-align:center; }
.photo-preview-box .ph-icon i { font-size:1.4rem; }
.photo-hints { font-size:.72rem; color:var(--text-muted); line-height:1.8; }

.mfoot2 { display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-top:20px; }
.mfoot2 button { padding:12px; border-radius:10px; font-family:inherit; font-size:.9rem; font-weight:700; cursor:pointer; transition:all .2s; border:none; }
.mfbtn-batal { background:var(--bg); color:var(--text); border:1.5px solid var(--border) !important; }
.mfbtn-batal:hover { border-color:var(--navy) !important; color:var(--navy); }
.mfbtn-simpan { background:var(--navy); color:#fff; }
.mfbtn-simpan:hover { background:var(--navy-mid); }
.mfbtn-simpan:disabled { opacity:.5; cursor:not-allowed; }

@keyframes spin { from{transform:rotate(0)}to{transform:rotate(360deg)} }
</style>
@endpush

@section('content')

{{-- ── STAT CARDS ─────────────────────── --}}
<div class="am-stats">
  <div class="am-stat">
    <div class="am-stat-icon"><i class="ri-shield-star-line"></i></div>
    <div>
      <div class="am-stat-label">Owner Aktif</div>
      <div class="am-stat-val">{{ $stats['owner'] }}</div>
    </div>
  </div>
  <div class="am-stat">
    <div class="am-stat-icon"><i class="ri-user-star-line"></i></div>
    <div>
      <div class="am-stat-label">Admin Aktif</div>
      <div class="am-stat-val">{{ $stats['kasir'] }}</div>
    </div>
  </div>
  <div class="am-stat">
    <div class="am-stat-icon"><i class="ri-team-line"></i></div>
    <div>
      <div class="am-stat-label">Total Akun</div>
      <div class="am-stat-val">{{ $stats['total'] }}</div>
    </div>
  </div>
</div>

{{-- ── TOOLBAR ─────────────────────────── --}}
<div class="am-toolbar">
  <div class="am-tabs">
    <button class="am-tab active" data-filter="all"   onclick="filterUsers('all',this)">All</button>
    <button class="am-tab"        data-filter="admin" onclick="filterUsers('admin',this)">Data Owner</button>
    <button class="am-tab"        data-filter="kasir" onclick="filterUsers('kasir',this)">Data Admin</button>
  </div>
  <button class="btn btn-primary" onclick="openAddModal()">
    <i class="ri-user-add-line"></i> Tambah Data
  </button>
</div>

{{-- ── USER LIST ───────────────────────── --}}
<div class="user-list" id="userList">
  @forelse($users as $user)
  <div class="user-row {{ !$user->is_active ? 'inactive' : '' }}"
       id="urow-{{ $user->id }}"
       data-role="{{ $user->role }}">
    <input type="checkbox" class="ur-check" />

    {{-- Photo --}}
    @if($user->profile_photo)
      <img class="ur-avatar" src="{{ asset('storage/'.$user->profile_photo) }}" alt="{{ $user->name }}" />
    @else
      <div class="ur-avatar-ph">{{ $user->initials }}</div>
    @endif

    {{-- Info --}}
    <div class="ur-body">
      <div class="ur-name">{{ $user->name }}</div>
      <div class="ur-phone">{{ $user->phone ?? '+62 000-0000-0000' }}</div>
      <div class="ur-role">
        {{ ucfirst($user->role) }}
        <span class="role-label {{ $user->is_active ? 'aktif' : 'nonaktif' }}">
          {{ $user->is_active ? 'Aktif' : 'NonAktif' }}
        </span>
      </div>
      @if($user->branch)
      <div class="ur-branch">Kantor : {{ $user->branch }}</div>
      @endif
    </div>

    {{-- Actions --}}
    <div class="ur-actions">
      <button class="btn-detail-ur" onclick="openDetailModal({{ $user->id }})">
        Detail
      </button>
      @if($user->is_active)
        <button class="btn-nonaktif" id="toggle-btn-{{ $user->id }}"
          onclick="toggleStatus({{ $user->id }}, this)">
          NonAktif
        </button>
      @else
        <button class="btn-aktif" id="toggle-btn-{{ $user->id }}"
          onclick="toggleStatus({{ $user->id }}, this)">
          Aktif
        </button>
      @endif
    </div>
  </div>
  @empty
  <div class="empty-state">
    <i class="ri-team-line"></i>
    <p>Belum ada pengguna</p>
  </div>
  @endforelse
</div>


{{-- ════ MODAL: DETAIL ════ --}}
<div class="mbd" id="mDetail">
  <div class="mbox">
    <div class="mhd">
      <h3 id="detailTitle">Detail</h3>
      <button class="mcls" onclick="closeModal('mDetail')"><i class="ri-close-line"></i></button>
    </div>
    <div class="mbody" id="mDetailBody">
      <div style="text-align:center;padding:30px;color:var(--text-muted);">
        <i class="ri-loader-4-line" style="font-size:2rem;animation:spin 1s linear infinite;display:block;margin-bottom:8px;"></i>
        Memuat...
      </div>
    </div>
    <div class="mfoot" id="mDetailFoot">
      <button class="mfbtn-tutup" onclick="closeModal('mDetail')">Tutup</button>
      <button class="mfbtn-nonaktif" id="detailToggleBtn" onclick="toggleFromDetail()">NonAktif</button>
    </div>
  </div>
</div>


{{-- ════ MODAL: TAMBAH ════ --}}
<div class="mbd" id="mAdd">
  <div class="mbox">
    <div class="mhd">
      <h3>Tambah</h3>
      <button class="mcls" onclick="closeModal('mAdd')"><i class="ri-close-line"></i></button>
    </div>
    <div class="mbody">
      <form id="addForm" enctype="multipart/form-data">
        @csrf
        <div class="mform-grid">

          {{-- Foto --}}
          <div class="mfg mform-full">
            <label class="mfl">Foto (opsional)</label>
            <div class="photo-upload-area">
              <label class="photo-preview-box" id="addPhotoBox" for="addPhotoInput">
                <div class="ph-icon"><i class="ri-camera-line"></i><span>Klik untuk upload</span></div>
              </label>
              <div class="photo-hints">
                Format: JPG, PNG, atau PDF<br>
                Ukuran maksimal: 2MB
              </div>
            </div>
            <input type="file" name="photo" id="addPhotoInput" accept="image/*" style="display:none;"
              onchange="previewPhoto(this,'addPhotoBox')" />
          </div>

          {{-- Name --}}
          <div class="mfg mform-full">
            <label class="mfl">Nama Lengkap <span class="req">*</span></label>
            <input type="text" name="name" class="mfc" placeholder="Nama lengkap" />
            <span class="merr" id="addErr_name"></span>
          </div>

          {{-- Email + Role --}}
          <div class="mfg">
            <label class="mfl">Email <span class="req">*</span></label>
            <input type="email" name="email" class="mfc" placeholder="email@domain.com" />
            <span class="merr" id="addErr_email"></span>
          </div>
          <div class="mfg">
            <label class="mfl">Jabatan <span class="req">*</span></label>
            <select name="role" class="mfc">
              <option value="kasir">Kasir</option>
              <option value="admin">Admin</option>
            </select>
            <span class="merr" id="addErr_role"></span>
          </div>

          {{-- Password --}}
          <div class="mfg">
            <label class="mfl">Password <span class="req">*</span></label>
            <input type="password" name="password" class="mfc" placeholder="Min. 8 karakter" />
            <span class="merr" id="addErr_password"></span>
          </div>
          <div class="mfg">
            <label class="mfl">Konfirmasi Password <span class="req">*</span></label>
            <input type="password" name="password_confirmation" class="mfc" placeholder="Ulangi password" />
          </div>

          {{-- Join date --}}
          <div class="mfg">
            <label class="mfl">Tanggal Masuk <span class="req">*</span></label>
            <input type="date" name="join_date" class="mfc" />
            <span class="merr" id="addErr_join_date"></span>
          </div>

          {{-- Phone --}}
          <div class="mfg">
            <label class="mfl">No Handphone <span class="req">*</span></label>
            <input type="text" name="phone" class="mfc" placeholder="08" />
            <span class="merr" id="addErr_phone"></span>
          </div>

          {{-- Branch --}}
          <div class="mfg mform-full">
            <label class="mfl">Kantor / Cabang</label>
            <input type="text" name="branch" class="mfc" placeholder="Contoh: Cabang Ciawi" />
          </div>

          {{-- Address --}}
          <div class="mfg mform-full">
            <label class="mfl">Alamat (opsional)</label>
            <textarea name="address" class="mfc" rows="2" placeholder="Domisili"></textarea>
          </div>

        </div>
        <div class="mfoot2">
          <button type="button" class="mfbtn-batal" onclick="closeModal('mAdd')">Batal</button>
          <button type="submit" class="mfbtn-simpan" id="addSaveBtn">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>


{{-- ════ MODAL: EDIT ════ --}}
<div class="mbd" id="mEdit">
  <div class="mbox">
    <div class="mhd">
      <h3 id="editTitle">Edit Akun</h3>
      <button class="mcls" onclick="closeModal('mEdit')"><i class="ri-close-line"></i></button>
    </div>
    <div class="mbody">
      <form id="editForm" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="editId" />
        <div class="mform-grid">

          <div class="mfg mform-full">
            <label class="mfl">Foto</label>
            <div class="photo-upload-area">
              <label class="photo-preview-box" id="editPhotoBox" for="editPhotoInput">
                <div class="ph-icon"><i class="ri-camera-line"></i><span>Ganti foto</span></div>
              </label>
              <div class="photo-hints">Format: JPG, PNG, atau PDF<br>Ukuran maksimal: 2MB</div>
            </div>
            <input type="file" name="photo" id="editPhotoInput" accept="image/*" style="display:none;"
              onchange="previewPhoto(this,'editPhotoBox')" />
          </div>

          <div class="mfg mform-full">
            <label class="mfl">Nama Lengkap <span class="req">*</span></label>
            <input type="text" name="name" id="eName" class="mfc" />
            <span class="merr" id="editErr_name"></span>
          </div>
          <div class="mfg">
            <label class="mfl">Email <span class="req">*</span></label>
            <input type="email" name="email" id="eEmail" class="mfc" />
            <span class="merr" id="editErr_email"></span>
          </div>
          <div class="mfg">
            <label class="mfl">Jabatan <span class="req">*</span></label>
            <select name="role" id="eRole" class="mfc">
              <option value="kasir">Kasir</option>
              <option value="admin">Admin</option>
            </select>
          </div>
          <div class="mfg">
            <label class="mfl">Password Baru</label>
            <input type="password" name="password" class="mfc" placeholder="Kosongkan jika tidak ubah" />
            <span class="merr" id="editErr_password"></span>
          </div>
          <div class="mfg">
            <label class="mfl">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" class="mfc" />
          </div>
          <div class="mfg">
            <label class="mfl">Tanggal Masuk</label>
            <input type="date" name="join_date" id="eJoinDate" class="mfc" />
          </div>
          <div class="mfg">
            <label class="mfl">No Handphone</label>
            <input type="text" name="phone" id="ePhone" class="mfc" />
          </div>
          <div class="mfg mform-full">
            <label class="mfl">Kantor / Cabang</label>
            <input type="text" name="branch" id="eBranch" class="mfc" />
          </div>
          <div class="mfg mform-full">
            <label class="mfl">Alamat</label>
            <textarea name="address" id="eAddress" class="mfc" rows="2"></textarea>
          </div>
        </div>
        <div class="mfoot2">
          <button type="button" class="mfbtn-batal" onclick="closeModal('mEdit')">Batal</button>
          <button type="submit" class="mfbtn-simpan" id="editSaveBtn">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
const CSRF    = '{{ csrf_token() }}';
const baseUrl = '{{ url("/admin") }}';
let currentUserId = null;
let currentIsActive = true;

// ─── Filter tabs ──────────────────────────────
function filterUsers(role, tabEl) {
  document.querySelectorAll('.am-tab').forEach(t => t.classList.remove('active'));
  tabEl.classList.add('active');
  document.querySelectorAll('.user-row').forEach(row => {
    row.style.display = (role === 'all' || row.dataset.role === role) ? '' : 'none';
  });
}

// ─── Modal helpers ────────────────────────────
function openModal(id)  { document.getElementById(id).classList.add('show'); }
function closeModal(id) { document.getElementById(id).classList.remove('show'); }

// ─── Photo preview ────────────────────────────
function previewPhoto(input, boxId) {
  const file = input.files[0]; if (!file) return;
  const reader = new FileReader();
  reader.onload = e => {
    const box = document.getElementById(boxId);
    box.innerHTML = `<img src="${e.target.result}" alt="preview" />`;
  };
  reader.readAsDataURL(file);
}

// ─── DETAIL MODAL ─────────────────────────────
async function openDetailModal(id) {
  currentUserId = id;
  document.getElementById('mDetailBody').innerHTML = `
    <div style="text-align:center;padding:30px;color:var(--text-muted);">
      <i class="ri-loader-4-line" style="font-size:2rem;animation:spin 1s linear infinite;display:block;margin-bottom:8px;"></i>
      Memuat...
    </div>`;
  openModal('mDetail');

  try {
    const res  = await fetch(`${baseUrl}/${id}`, { headers:{'Accept':'application/json','X-CSRF-TOKEN':CSRF} });
    const user = await res.json();
    currentIsActive = user.is_active;
    renderDetail(user);
  } catch(e) {
    document.getElementById('mDetailBody').innerHTML = `<p style="color:var(--red);text-align:center;">Gagal memuat data.</p>`;
  }
}

function renderDetail(user) {
  document.getElementById('detailTitle').textContent = 'Detail ' + user.name;

  // Toggle button in footer
  const toggleBtn = document.getElementById('detailToggleBtn');
  if (user.is_active) {
    toggleBtn.textContent = 'NonAktif';
    toggleBtn.className   = 'mfbtn-nonaktif';
  } else {
    toggleBtn.textContent = 'Aktif';
    toggleBtn.className   = 'mfbtn-aktif';
  }

  const photoHtml = user.photo_url
    ? `<img class="detail-photo" src="${user.photo_url}" alt="${user.name}" />`
    : `<div class="detail-photo-ph">${user.initials}</div>`;

  const statusHtml = user.is_active
    ? `<span class="dic-status-aktif">Aktif</span>`
    : `<span class="dic-status-nonaktif">NonAktif</span>`;

  document.getElementById('mDetailBody').innerHTML = `
    <div class="detail-info-card">
      <div class="dic-field">
        <div class="dic-label">Nama</div>
        <div class="dic-val">${user.name}</div>
      </div>
      <div>
        <div class="dic-field" style="margin-bottom:10px;">
          <div class="dic-label">Status</div>
          <div>${statusHtml}</div>
        </div>
        <div class="dic-field">
          <div class="dic-label">Jabatan</div>
          <div class="dic-val">${ucFirst(user.role)}</div>
        </div>
      </div>
      ${photoHtml}
      <div class="dic-field">
        <div class="dic-label">Cabang</div>
        <div>${user.branch ? `<span class="dic-badge">${user.branch}</span>` : '<span style="color:var(--text-muted);">-</span>'}</div>
      </div>
    </div>

    <div class="detail-section-label">Detail</div>
    <div class="detail-extra-box">
      <div class="de-row">
        <div class="de-label">No Hp</div>
        <div class="de-val">${user.phone || '-'}</div>
      </div>
      <div class="de-row">
        <div class="de-label">Alamat</div>
        <div class="de-val">${user.address || '-'}</div>
      </div>
      ${user.join_date ? `<div class="de-row"><div class="de-label">Tanggal Masuk</div><div class="de-val">${user.join_date}</div></div>` : ''}
      <div class="de-row">
        <div class="de-label">Email</div>
        <div class="de-val">${user.email}</div>
      </div>
    </div>

    <div style="display:flex;justify-content:flex-end;margin-top:14px;">
      <button class="btn btn-outline btn-sm" onclick="openEditFromDetail(${user.id})">
        <i class="ri-edit-line"></i> Edit Data
      </button>
    </div>`;
}

// ─── Toggle status from detail modal ─────────
async function toggleFromDetail() {
  if (!currentUserId) return;
  await doToggle(currentUserId);
  openDetailModal(currentUserId); // re-fetch to update modal
}

// ─── Toggle status from list ──────────────────
async function toggleStatus(id, btn) {
  btn.disabled = true;
  await doToggle(id);
  btn.disabled = false;
}

async function doToggle(id) {
  try {
    const res  = await fetch(`${baseUrl}/${id}/toggle`, {
      method: 'PATCH',
      headers: { 'Content-Type':'application/json', 'Accept':'application/json', 'X-CSRF-TOKEN':CSRF },
    });
    const data = await res.json();

    if (data.success) {
      updateRowStatus(id, data.is_active);
      toast(data.message, 'success');
    } else {
      toast(data.message || 'Gagal.', 'error');
    }
  } catch(e) {
    toast('Terjadi kesalahan.', 'error');
  }
}

function updateRowStatus(id, isActive) {
  const row = document.getElementById(`urow-${id}`);
  if (!row) return;

  row.classList.toggle('inactive', !isActive);

  // Update role-label text
  const roleLabel = row.querySelector('.role-label');
  if (roleLabel) {
    roleLabel.textContent = isActive ? 'Aktif' : 'NonAktif';
    roleLabel.className   = 'role-label ' + (isActive ? 'aktif' : 'nonaktif');
  }

  // Replace toggle button
  const toggleBtn = document.getElementById(`toggle-btn-${id}`);
  if (toggleBtn) {
    if (isActive) {
      toggleBtn.textContent = 'NonAktif';
      toggleBtn.className   = 'btn-nonaktif';
      toggleBtn.setAttribute('onclick', `toggleStatus(${id}, this)`);
    } else {
      toggleBtn.textContent = 'Aktif';
      toggleBtn.className   = 'btn-aktif';
      toggleBtn.setAttribute('onclick', `toggleStatus(${id}, this)`);
    }
  }
}

// ─── ADD MODAL ────────────────────────────────
function openAddModal() {
  document.getElementById('addForm').reset();
  document.getElementById('addPhotoBox').innerHTML =
    '<div class="ph-icon"><i class="ri-camera-line"></i><span>Klik untuk upload</span></div>';
  clearErrors('add');
  openModal('mAdd');
}

document.getElementById('addForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  clearErrors('add');
  const btn = document.getElementById('addSaveBtn');
  btn.disabled = true; btn.textContent = 'Menyimpan…';

  try {
    const fd  = new FormData(this);
    const res = await fetch(baseUrl, { method:'POST', headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}, body:fd });
    const ct  = res.headers.get('content-type') || '';
    if (!ct.includes('application/json')) {
      toast('Server error ' + res.status, 'error');
      console.error(await res.text());
      return;
    }
    const data = await res.json();
    if (data.success) {
      closeModal('mAdd');
      this.reset();
      appendUserRow(data.user);
      toast(data.message, 'success');
    } else {
      if (data.errors) showErrors('add', data.errors);
      else toast(data.message || 'Gagal menyimpan.', 'error');
    }
  } catch(err) {
    console.error(err);
    toast('Terjadi kesalahan.', 'error');
  } finally {
    btn.disabled = false; btn.textContent = 'Simpan';
  }
});

// ─── EDIT MODAL ───────────────────────────────
async function openEditFromDetail(id) {
  closeModal('mDetail');
  openEditModal(id);
}

async function openEditModal(id) {
  clearErrors('edit');
  try {
    const res  = await fetch(`${baseUrl}/${id}`, { headers:{'Accept':'application/json','X-CSRF-TOKEN':CSRF} });
    const user = await res.json();

    document.getElementById('editId').value        = user.id;
    document.getElementById('eName').value         = user.name;
    document.getElementById('eEmail').value        = user.email;
    document.getElementById('eRole').value         = user.role;
    document.getElementById('ePhone').value        = user.phone || '';
    document.getElementById('eAddress').value      = user.address || '';
    document.getElementById('eBranch').value       = user.branch || '';
    document.getElementById('eJoinDate').value     = user.join_date || '';
    document.getElementById('editTitle').textContent = 'Edit: ' + user.name;

    const editPhotoBox = document.getElementById('editPhotoBox');
    if (user.photo_url) {
      editPhotoBox.innerHTML = `<img src="${user.photo_url}" alt="${user.name}" />`;
    } else {
      editPhotoBox.innerHTML = `<div class="ph-icon"><i class="ri-camera-line"></i><span>Ganti foto</span></div>`;
    }

    openModal('mEdit');
  } catch(e) {
    toast('Gagal memuat data.', 'error');
  }
}

document.getElementById('editForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  clearErrors('edit');
  const id  = document.getElementById('editId').value;
  const btn = document.getElementById('editSaveBtn');
  btn.disabled = true; btn.textContent = 'Menyimpan…';

  try {
    const fd = new FormData(this);
    fd.append('_method', 'PUT');
    const res  = await fetch(`${baseUrl}/${id}`, { method:'POST', headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}, body:fd });
    const ct   = res.headers.get('content-type') || '';
    if (!ct.includes('application/json')) { toast('Server error ' + res.status, 'error'); return; }
    const data = await res.json();

    if (data.success) {
      closeModal('mEdit');
      updateUserRow(data.user);
      toast(data.message, 'success');
    } else {
      if (data.errors) showErrors('edit', data.errors);
      else toast(data.message || 'Gagal memperbarui.', 'error');
    }
  } catch(err) {
    toast('Terjadi kesalahan.', 'error');
  } finally {
    btn.disabled = false; btn.textContent = 'Simpan Perubahan';
  }
});

// ─── Row helpers ──────────────────────────────
function appendUserRow(user) {
  const list = document.getElementById('userList');
  const empty = list.querySelector('.empty-state');
  if (empty) empty.remove();

  const div = document.createElement('div');
  div.id = `urow-${user.id}`;
  div.className = 'user-row';
  div.dataset.role = user.role;
  div.innerHTML = buildRowInner(user);
  list.appendChild(div);
}

function updateUserRow(user) {
  const row = document.getElementById(`urow-${user.id}`);
  if (!row) return;
  row.dataset.role = user.role;
  row.innerHTML = buildRowInner(user);
}

function buildRowInner(user) {
  const photoHtml = user.photo_url
    ? `<img class="ur-avatar" src="${user.photo_url}" alt="${user.name}" />`
    : `<div class="ur-avatar-ph">${user.initials}</div>`;
  const isActive = user.is_active;
  const toggleBtnHtml = isActive
    ? `<button class="btn-nonaktif" id="toggle-btn-${user.id}" onclick="toggleStatus(${user.id},this)">NonAktif</button>`
    : `<button class="btn-aktif"    id="toggle-btn-${user.id}" onclick="toggleStatus(${user.id},this)">Aktif</button>`;

  return `
    <input type="checkbox" class="ur-check" />
    ${photoHtml}
    <div class="ur-body">
      <div class="ur-name">${user.name}</div>
      <div class="ur-phone">${user.phone || '-'}</div>
      <div class="ur-role">${ucFirst(user.role)} <span class="role-label ${isActive?'aktif':'nonaktif'}">${isActive?'Aktif':'NonAktif'}</span></div>
      ${user.branch ? `<div class="ur-branch">Kantor : ${user.branch}</div>` : ''}
    </div>
    <div class="ur-actions">
      <button class="btn-detail-ur" onclick="openDetailModal(${user.id})">Detail</button>
      ${toggleBtnHtml}
    </div>`;
}

// ─── Error helpers ────────────────────────────
function clearErrors(prefix) {
  document.querySelectorAll(`[id^="${prefix}Err_"]`).forEach(el => { el.textContent=''; el.classList.remove('show'); });
  document.querySelectorAll('.mfc.err').forEach(el => el.classList.remove('err'));
}
function showErrors(prefix, errors) {
  Object.entries(errors).forEach(([field, msgs]) => {
    const errEl = document.getElementById(`${prefix}Err_${field}`);
    if (errEl) { errEl.textContent = msgs[0]; errEl.classList.add('show'); }
    const inp = document.querySelector(`#${prefix}Form [name="${field}"]`);
    if (inp) inp.classList.add('err');
  });
}

// ─── Utilities ────────────────────────────────
function ucFirst(str) { return str ? str.charAt(0).toUpperCase() + str.slice(1) : ''; }

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

// Close on backdrop click
['mDetail','mAdd','mEdit'].forEach(id => {
  document.getElementById(id).addEventListener('click', function(e) {
    if (e.target === this) closeModal(id);
  });
});
</script>
@endpush