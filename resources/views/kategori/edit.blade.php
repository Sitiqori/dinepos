@extends('layouts.master')
@section('title', 'Edit Kategori')
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
    <h1>Edit Kategori</h1>
    <div class="breadcrumb">
      <a href="{{ route('dashboard') }}">Dashboard</a> /
      <a href="{{ route('kategori.index') }}">Kategori</a> / Edit
    </div>
  </div>
  <a href="{{ route('kategori.index') }}" class="btn btn-outline">
    <i class="ri-arrow-left-line"></i> Kembali
  </a>
</div>

<form action="{{ route('kategori.update', $kategori) }}" method="POST">
  @csrf @method('PUT')
  <div class="form-card">

    <div class="form-group">
      <label class="form-label">Nama Kategori *</label>
      <input type="text" name="name" class="form-control"
        value="{{ old('name', $kategori->name) }}" required />
    </div>

    <div class="form-group">
      <label class="form-label">Deskripsi</label>
      <textarea name="description" class="form-control" rows="3">{{ old('description', $kategori->description) }}</textarea>
    </div>

    <div style="display:flex;gap:10px;margin-top:8px;">
      <button type="submit" class="btn btn-primary">
        <i class="ri-save-line"></i> Simpan Perubahan
      </button>
      <a href="{{ route('kategori.index') }}" class="btn btn-outline">Batal</a>
    </div>
  </div>
</form>
@endsection
