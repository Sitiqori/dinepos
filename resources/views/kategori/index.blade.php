@extends('layouts.master')
@section('title', 'Kategori')
@section('page_title', 'Kategori')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}" />
@endpush

@section('content')
<div class="page-header">
  <div>
    <h1>Kategori</h1>
    <div class="breadcrumb"><a href="{{ route('dashboard') }}">Dashboard</a> / Kategori</div>
  </div>
  <a href="{{ route('kategori.create') }}" class="btn btn-primary">
    <i class="ri-add-line"></i> Tambah Kategori
  </a>
</div>

<div class="card" style="overflow:hidden;">
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Nama Kategori</th>
          <th>Slug</th>
          <th>Deskripsi</th>
          <th>Jumlah Produk</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($categories as $i => $cat)
        <tr>
          <td style="color:var(--text-muted);">{{ $categories->firstItem() + $i }}</td>
          <td style="font-weight:600;">{{ $cat->name }}</td>
          <td><code style="background:var(--bg);padding:2px 8px;border-radius:4px;font-size:.78rem;">{{ $cat->slug }}</code></td>
          <td style="color:var(--text-muted);">{{ $cat->description ?? '-' }}</td>
          <td>
            <span class="badge badge-navy">{{ $cat->products_count }} produk</span>
          </td>
          <td>
            <div style="display:flex;gap:6px;">
              <a href="{{ route('kategori.edit', $cat) }}" class="btn-icon" title="Edit">
                <i class="ri-edit-line"></i>
              </a>
              <form action="{{ route('kategori.destroy', $cat) }}" method="POST" style="display:inline;">
                @csrf @method('DELETE')
                <button type="submit" class="btn-icon btn-delete-confirm" title="Hapus"
                  style="color:var(--red);">
                  <i class="ri-delete-bin-line"></i>
                </button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted);">
            <i class="ri-folder-open-line" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
            Belum ada kategori
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($categories->hasPages())
  <div style="padding:14px 18px;border-top:1px solid var(--border);">
    {{ $categories->links() }}
  </div>
  @endif
</div>
@endsection
