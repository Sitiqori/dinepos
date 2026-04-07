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

// ──────────────────────────────────────────────
// AUTH routes (guest only)
// ──────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ──────────────────────────────────────────────
// AUTHENTICATED routes
// ──────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Root redirect
    Route::get('/', function () {
        return redirect()->route(
            auth()->user()->isAdmin() ? 'dashboard' : 'kasir.index'
        );
    });

    // ── KASIR & ADMIN: shared routes ──────────
    Route::middleware('role:admin,kasir')->group(function () {
        Route::get('/kasir',               [KasirController::class, 'index'])->name('kasir.index');
        Route::post('/kasir/order',        [KasirController::class, 'createOrder'])->name('kasir.order');
        Route::get('/kasir/payment/{order}',[KasirController::class, 'payment'])->name('kasir.payment');
        Route::post('/kasir/pay/{order}',  [KasirController::class, 'pay'])->name('kasir.pay');

        Route::get('/pesanan',             [PesananController::class, 'index'])->name('pesanan.index');
        Route::get('/pesanan/{order}',     [PesananController::class, 'show'])->name('pesanan.show');
        Route::patch('/pesanan/{order}/status', [PesananController::class, 'updateStatus'])->name('pesanan.status');

        Route::get('/transaksi',           [TransaksiController::class, 'index'])->name('transaksi.index');
        Route::get('/transaksi/{tx}',      [TransaksiController::class, 'show'])->name('transaksi.show');
    });

    // ── ADMIN ONLY ────────────────────────────
    Route::middleware('role:admin')->group(function () {
        Route::get('/dashboard',           [DashboardController::class, 'index'])->name('dashboard');

        // Barang & Stok
        Route::resource('barang',          BarangController::class);

        // Kategori
        Route::resource('kategori',        KategoriController::class);

        // Laporan
        Route::get('/laporan',             [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/download',    [LaporanController::class, 'download'])->name('laporan.download');

        // Manajemen Admin
        Route::resource('admin',           AdminController::class)->except(['show']);

        // Pengaturan
        Route::get('/pengaturan',          [PengaturanController::class, 'index'])->name('pengaturan.index');
        Route::post('/pengaturan',         [PengaturanController::class, 'update'])->name('pengaturan.update');
    });
});
