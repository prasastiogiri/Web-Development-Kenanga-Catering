<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthAdminController;
use App\Http\Controllers\Auth\AuthUserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PaketController;
use App\Http\Controllers\Admin\ProdukController;
use App\Http\Controllers\User\HomepageController;
use App\Http\Controllers\User\PaketUserController;
use App\Http\Controllers\User\ProdukUserController;
use App\Http\Controllers\User\KeranjangController;
use App\Http\Controllers\User\PemesananController;
use App\Http\Controllers\Admin\TransaksiController;
use App\Http\Controllers\Admin\LaporanController;
// Homepage
Route::get('/', [HomepageController::class, 'index'])->name('homepage');
Route::get('/paket', [PaketUserController::class, 'index'])->name('paketUser.index');
Route::get('/produk', [ProdukUserController::class, 'index'])->name('produkUser.index');

// Routes for User Authentication
Route::group(['middleware' => 'guest'], function () {
    Route::get('/login', [AuthUserController::class, 'showLoginForm'])->name('user.login');
    Route::post('/login', [AuthUserController::class, 'login'])->name('user.login.submit');
    Route::get('/register', [AuthUserController::class, 'showRegisterForm'])->name('user.register');
    Route::post('/register', [AuthUserController::class, 'register'])->name('user.register.submit');
});

// User logout (POST for security)
Route::post('/logout', [AuthUserController::class, 'logout'])->name('user.logout');

// Routes for Authenticated Users
Route::group(['middleware' => 'auth'], function () {

    Route::post('/keranjang/update-jumlah/{id}', [KeranjangController::class, 'updateJumlah'])->name('keranjang.updateJumlah');
    Route::delete('/keranjang/{id}', [KeranjangController::class, 'destroy'])->name('keranjang.delete');
    Route::get('/keranjang/get-items', [KeranjangController::class, 'getKeranjangItems'])->name('keranjang.get-items');
    Route::get('/keranjang/item-count', [KeranjangController::class, 'getItemCount'])->name('keranjang.item-count');
    Route::resource('keranjang', KeranjangController::class);
    Route::resource('pemesanan', PemesananController::class, );
    Route::post('/pemesanan/store', [PemesananController::class, 'store'])->name('pemesanan.store');
    Route::get('/pemesanan/status/{orderId}', [PemesananController::class, 'showPaymentStatus'])
        ->name('pemesanan.status')
        ->middleware('auth');
    Route::get('/profil', [AuthUserController::class, 'showProfile'])->name('user.profile');
    Route::put('/profil', [AuthUserController::class, 'updateProfile'])->name('user.profile.update');
    Route::get('/pemesanan/checkout', [PemesananController::class, 'checkout'])->name('pemesanan.checkout');
    Route::post('/pemesanan/store', [PemesananController::class, 'store'])->name('pemesanan.store');
    Route::get('/pemesanan/status/{orderId}', [PemesananController::class, 'showPaymentStatus'])->name('pemesanan.status');
    Route::get('/riwayat-pemesanan', [PemesananController::class, 'riwayatPemesanan'])->name('pemesanan.riwayatPemesanan');
    Route::get('/pemesanan/{orderId}', [PemesananController::class, 'show'])->name('pemesanan.show');
    Route::post('midtrans/notification', [PemesananController::class, 'handleNotification'])
    ->name('midtrans.notification')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
    Route::get('/pemesanan/detail/{orderId}', [PemesananController::class, 'show'])->name('pemesanan.detail');
    Route::get('/pemesanan/print-pdf/{orderId}', [PemesananController::class, 'printPDF'])
    ->name('pemesanan.print-pdf')
    ->middleware(['auth', 'check.order.owner']);
    Route::get('/keranjang/check-quantity/{produkId}', [KeranjangController::class, 'checkQuantity']);
});


// Routes for Admin Authentication
Route::group(['middleware' => 'guest:usersAdmin'], function () {
    Route::get('/admin', [AuthAdminController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/admin', [AuthAdminController::class, 'login'])->name('admin.login.submit');
    Route::get('/register-admin', [AuthAdminController::class, 'showRegisterForm'])->name('admin.register');
    Route::post('/register-admin', [AuthAdminController::class, 'register'])->name('admin.register.submit');
});

// Admin logout (POST for security)
Route::post('/admin-logout', [AuthAdminController::class, 'logout'])->name('admin.logout');

// Routes for Authenticated Admins
Route::group(['middleware' => 'auth:usersAdmin'], function () {
    Route::get('/admin-dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::resource('admin-produk', ProdukController::class);
    Route::resource('admin-paket', PaketController::class);
    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
    Route::get('/transaksi/{orderId}', [TransaksiController::class, 'show'])->name('transaksi.show');
    Route::get('/admin/transaksi/datatables', [TransaksiController::class, 'datatables'])
    ->name('transaksi.datatables');
    Route::post('/admin/transaksi/update-status', [TransaksiController::class, 'updateStatus'])
    ->name('transaksi.update-status');
    Route::get('/admin/transaksi/{id}', [PemesananController::class, 'show'])->name('transaksi.show');
    Route::get('/admin/laporan', [LaporanController::class, 'index'])->name('admin.laporan');
    Route::get('/admin/laporan/chart-data', [LaporanController::class, 'getChartData'])->name('admin.laporan.chart-data');
    Route::get('/admin/laporan/export', [LaporanController::class, 'exportData'])->name('admin.laporan.export');
    // routes/web.php
Route::get('/admin/dashboard/revenue-data', [DashboardController::class, 'getRevenueData'])
->name('admin.dashboard.revenue-data');
});

