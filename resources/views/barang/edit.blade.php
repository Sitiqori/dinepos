@extends('layouts.master')
@section('title', 'Edit Produk')
@section('page_title', 'Barang & Stok')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}" />
<style>
.form-card { background:#fff;border:1px solid var(--border);border-radius:var(--radius-md);padding:24px;max-width:700px; }
.img-preview { width:100%;height:180px;border-radius:var(--radius-sm);border:2px dashed var(--border);display:flex;align-items:center;justify-content:center;flex-direction:column;gap:8px;color:var(--text-muted);font-size:.85rem;cursor:pointer;transition:border-color .2s;overflow:hidden; }
.img-preview:hover { border-color:var(--blue); }
.img-preview img { width:100%;height:100%;object-fit:cover; }
.form-row { display:grid;grid-template-columns:1fr 1fr;gap:16px; }
</style>
@endpush

@section('content')
<div class="page-header">
  <div>
    <h1>Edit Produk</h1>
    <div class="breadcrumb"><a href="{{ route('dashboard') }}">Dashboard</a> / <a href="{{ route('barang.index') }}">Barang</a> / Edit</div>
  </div>
  <a href="{{ route('barang.index') }}" class="btn btn-outline"><i class="ri-arrow-left-line"></i> Kembali</a>
</div>

<form action="{{ route('barang.update', $barang) }}" method="POST" enctype="multipart/form-data">
  @csrf @method('PUT')
  <div class="form-card">
    <h3 style="margin-bottom:20px;font-size:1rem;color:var(--navy);">Edit: {{ $barang->name }}</h3>

    <div class="form-group">
      <label class="form-label">Nama Produk *</label>
      <input type="text" name="name" class="form-control" value="{{ old('name', $barang->name) }}" required />
    </div>

    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Kategori *</label>
        <select name="category_id" class="form-control" required>
          @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ old('category_id', $barang->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Stok *</label>
        <input type="number" name="stock" class="form-control" value="{{ old('stock', $barang->stock) }}" min="0" required />
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Harga Jual (Rp) *</label>
        <input type="number" name="price" class="form-control" value="{{ old('price', $barang->price) }}" min="0" required />
      </div>
      <div class="form-group">
        <label class="form-label">Harga Pokok (Rp) *</label>
        <input type="number" name="cost_price" class="form-control" value="{{ old('cost_price', $barang->cost_price) }}" min="0" required />
      </div>
    </div>

    <div class="form-group">
      <label class="form-label">Deskripsi</label>
      <textarea name="description" class="form-control" rows="3">{{ old('description', $barang->description) }}</textarea>
    </div>

    <div class="form-group">
      <label class="form-label">Foto Produk</label>
      <label class="img-preview" id="imgPreview" for="imageInput">
        @if($barang->image)
          <img src="{{ asset('storage/'.$barang->image) }}" alt="{{ $barang->name }}" />
        @else
          <i class="ri-image-add-line" style="font-size:2rem;"></i>
          <span>Klik untuk ganti foto</span>
        @endif
        <input type="file" name="image" id="imageInput" accept="image/*" style="display:none;" />
      </label>
    </div>

    <div class="form-group">
      <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $barang->is_active) ? 'checked' : '' }} style="width:16px;height:16px;accent-color:var(--navy);" />
        <span style="font-weight:500;">Produk Aktif</span>
      </label>
    </div>

    <div style="display:flex;gap:10px;margin-top:8px;">
      <button type="submit" class="btn btn-primary"><i class="ri-save-line"></i> Simpan Perubahan</button>
      <a href="{{ route('barang.index') }}" class="btn btn-outline">Batal</a>
    </div>
  </div>
</form>
@endsection
