@extends('layouts.master')
@section('title', 'Tambah Kategori')
@section('page_title', 'Kategori')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}" />
<style>
.form-card { background:#fff;border:1px solid var(--border);border-radius:var(--radius-md);padding:24px;max-width:560px; }
</style>
@endpush

@section('content')
<div class="page-header">
  <div>
    <h1>Tambah Kategori</h1>
    <div class="breadcrumb">
      <a href="{{ route('dashboard') }}">Dashboard</a> /
      <a href="{{ route('kategori.index') }}">Kategori</a> / Tambah
    </div>
  </div>
  <a href="{{ route('kategori.index') }}" class="btn btn-outline">
    <i class="ri-arrow-left-line"></i> Kembali
  </a>
</div>

<form action="{{ route('kategori.store') }}" method="POST">
  @csrf
  <div class="form-card">

    <div class="form-group">
      <label class="form-label">Nama Kategori *</label>
      <input type="text" name="name" class="form-control"
        value="{{ old('name') }}"
        placeholder="Contoh: Dimsum, Minuman, Snack..."
        required />
    </div>

    <div class="form-group">
      <label class="form-label">Deskripsi</label>
      <textarea name="description" class="form-control" rows="3"
        placeholder="Deskripsi kategori (opsional)...">{{ old('description') }}</textarea>
    </div>

    <div style="display:flex;gap:10px;margin-top:8px;">
      <button type="submit" class="btn btn-primary">
        <i class="ri-save-line"></i> Simpan Kategori
      </button>
      <a href="{{ route('kategori.index') }}" class="btn btn-outline">Batal</a>
    </div>
  </div>
</form>
@endsection
