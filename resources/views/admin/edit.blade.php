@extends('layouts.master')
@section('title', 'Edit Pengguna')
@section('page_title', 'Manajemen Admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}" />
<style>
.form-card { background:#fff;border:1px solid var(--border);border-radius:var(--radius-md);padding:24px;max-width:560px; }
</style>
@endpush

@section('content')
<div class="page-header">
  <div>
    <h1>Edit Pengguna</h1>
    <div class="breadcrumb">
      <a href="{{ route('dashboard') }}">Dashboard</a> /
      <a href="{{ route('admin.index') }}">Manajemen Admin</a> / Edit
    </div>
  </div>
  <a href="{{ route('admin.index') }}" class="btn btn-outline">
    <i class="ri-arrow-left-line"></i> Kembali
  </a>
</div>

<form action="{{ route('admin.update', $admin) }}" method="POST">
  @csrf @method('PUT')
  <div class="form-card">

    <div class="form-group">
      <label class="form-label">Nama Lengkap *</label>
      <input type="text" name="name" class="form-control"
        value="{{ old('name', $admin->name) }}" required />
    </div>

    <div class="form-group">
      <label class="form-label">Email *</label>
      <input type="email" name="email" class="form-control"
        value="{{ old('email', $admin->email) }}" required />
    </div>

    <div class="form-group">
      <label class="form-label">Role *</label>
      <select name="role" class="form-control" required>
        <option value="kasir" {{ old('role', $admin->role) === 'kasir' ? 'selected' : '' }}>Kasir</option>
        <option value="admin" {{ old('role', $admin->role) === 'admin' ? 'selected' : '' }}>Admin</option>
      </select>
    </div>

    <div class="form-group">
      <label class="form-label">Password Baru</label>
      <input type="password" name="password" class="form-control"
        placeholder="Kosongkan jika tidak diubah" />
    </div>

    <div class="form-group">
      <label class="form-label">Konfirmasi Password</label>
      <input type="password" name="password_confirmation" class="form-control"
        placeholder="Ulangi password baru" />
    </div>

    <div style="display:flex;gap:10px;margin-top:8px;">
      <button type="submit" class="btn btn-primary">
        <i class="ri-save-line"></i> Simpan Perubahan
      </button>
      <a href="{{ route('admin.index') }}" class="btn btn-outline">Batal</a>
    </div>
  </div>
</form>
@endsection
