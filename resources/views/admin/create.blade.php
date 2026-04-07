@extends('layouts.master')
@section('title', 'Tambah Pengguna')
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
    <h1>Tambah Pengguna</h1>
    <div class="breadcrumb">
      <a href="{{ route('dashboard') }}">Dashboard</a> /
      <a href="{{ route('admin.index') }}">Manajemen Admin</a> / Tambah
    </div>
  </div>
  <a href="{{ route('admin.index') }}" class="btn btn-outline">
    <i class="ri-arrow-left-line"></i> Kembali
  </a>
</div>

<form action="{{ route('admin.store') }}" method="POST">
  @csrf
  <div class="form-card">

    <div class="form-group">
      <label class="form-label">Nama Lengkap *</label>
      <input type="text" name="name" class="form-control"
        value="{{ old('name') }}" placeholder="Nama pengguna" required />
    </div>

    <div class="form-group">
      <label class="form-label">Email *</label>
      <input type="email" name="email" class="form-control"
        value="{{ old('email') }}" placeholder="email@example.com" required />
    </div>

    <div class="form-group">
      <label class="form-label">Role *</label>
      <select name="role" class="form-control" required>
        <option value="kasir" {{ old('role') === 'kasir' ? 'selected' : '' }}>Kasir</option>
        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
      </select>
    </div>

    <div class="form-group">
      <label class="form-label">Password *</label>
      <input type="password" name="password" class="form-control"
        placeholder="Min. 8 karakter" required />
    </div>

    <div class="form-group">
      <label class="form-label">Konfirmasi Password *</label>
      <input type="password" name="password_confirmation" class="form-control"
        placeholder="Ulangi password" required />
    </div>

    <div style="display:flex;gap:10px;margin-top:8px;">
      <button type="submit" class="btn btn-primary">
        <i class="ri-user-add-line"></i> Tambah Pengguna
      </button>
      <a href="{{ route('admin.index') }}" class="btn btn-outline">Batal</a>
    </div>
  </div>
</form>
@endsection
