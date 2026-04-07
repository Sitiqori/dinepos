<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PengaturanController;
use Illuminate\Support\Facades\Route;

// ── AUTH (guest only) ──────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [AuthController::class, 'login'])->name('login.post');
    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ── AUTHENTICATED ───────────────────────────────
Route::middleware('auth')->group(function () {

    Route::get('/', function () {
        return redirect()->route(auth()->user()->isAdmin() ? 'dashboard' : 'kasir.index');
    });

    // ── KASIR & ADMIN ──────────────────────────
    Route::middleware('role:admin,kasir')->group(function () {
        Route::get('/kasir',                        [KasirController::class, 'index'])->name('kasir.index');
        Route::post('/kasir/order',                 [KasirController::class, 'createOrder'])->name('kasir.order');

        Route::get('/pesanan',                      [PesananController::class, 'index'])->name('pesanan.index');
        Route::get('/pesanan/{pesanan}',            [PesananController::class, 'show'])->name('pesanan.show');
        Route::patch('/pesanan/{pesanan}/status',   [PesananController::class, 'updateStatus'])->name('pesanan.status');

        Route::get('/transaksi',                    [TransaksiController::class, 'index'])->name('transaksi.index');
        Route::get('/transaksi/{transaksi}',        [TransaksiController::class, 'show'])->name('transaksi.show');
    });

    // ── ADMIN ONLY ─────────────────────────────
    Route::middleware('role:admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // ── BARANG ──────────────────────────────
        // IMPORTANT: static routes MUST be declared before {barang} param route
        Route::get('/barang/export/pdf', [BarangController::class, 'exportPdf'])->name('barang.export.pdf');
        Route::get('/barang/export/csv', [BarangController::class, 'exportCsv'])->name('barang.export.csv');

        Route::get('/barang',            [BarangController::class, 'index'])->name('barang.index');
        Route::post('/barang',           [BarangController::class, 'store'])->name('barang.store');
        Route::get('/barang/{barang}',   [BarangController::class, 'show'])->name('barang.show');
        Route::put('/barang/{barang}',   [BarangController::class, 'update'])->name('barang.update');
        Route::delete('/barang/{barang}',[BarangController::class, 'destroy'])->name('barang.destroy');

        // ── KATEGORI ─────────────────────────────
        Route::resource('kategori', KategoriController::class);

        // ── LAPORAN ──────────────────────────────
        Route::get('/laporan',          [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/download', [LaporanController::class, 'download'])->name('laporan.download');

        // ── ADMIN MANAGEMENT ─────────────────────
        Route::get('/admin',                      [AdminController::class, 'index'])->name('admin.index');
        Route::post('/admin',                     [AdminController::class, 'store'])->name('admin.store');
        Route::get('/admin/{admin}',              [AdminController::class, 'show'])->name('admin.show');
        Route::put('/admin/{admin}',              [AdminController::class, 'update'])->name('admin.update');
        Route::delete('/admin/{admin}',           [AdminController::class, 'destroy'])->name('admin.destroy');
        Route::patch('/admin/{admin}/toggle',     [AdminController::class, 'toggleActive'])->name('admin.toggle');

        // ── PENGATURAN ───────────────────────────
        Route::get('/pengaturan',  [PengaturanController::class, 'index'])->name('pengaturan.index');
        Route::post('/pengaturan', [PengaturanController::class, 'update'])->name('pengaturan.update');
    });
});
