<?php

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


//api
Route::get('/test', [ApiController::class, 'test']);
Route::post('/cari_pedagang', [ApiController::class, 'cari_pedagang']);
Route::post('/pedagang', [ApiController::class, 'pedagang']);
Route::post('/generate-qr', [ApiController::class, 'generateQrCode']);
Route::post('/tagihan', [ApiController::class, 'tagihan']);
Route::post('/tagihan_backup', [ApiController::class, 'tagihan_backup']);
Route::post('/bayar', [ApiController::class, 'bayar']);
Route::post('/hapus_transaksi', [ApiController::class, 'hapus_transaksi']);
Route::post('/dashboard', [ApiController::class, 'dashboard']); // Rute baru untuk dashboard
Route::post('/laporan', [ApiController::class, 'laporan']); // Rute baru untuk laporan
Route::post('/laporan_transaksi', [ApiController::class, 'laporan_transaksi']); // Rute baru untuk laporan transaksi dengan tarif

Route::post('/login', [ApiController::class, 'login']);
Route::post('/revenue_chart', [ApiController::class, 'revenue_chart']);
Route::post('/home', [ApiController::class, 'home']);
Route::post('/admin', [ApiController::class, 'admin']);
Route::post('/tagihan_stat', [ApiController::class, 'tagihan_stat']);
Route::post('/home_users_stat', [ApiController::class, 'home_users_stat']);
Route::post('/filter_options', [ApiController::class, 'getFilterOptions']);
Route::post('/filter_options_tagihan', [ApiController::class, 'getFilterOptionsTagihan']);
Route::post('/payment_methods_chart', [ApiController::class, 'payment_methods_chart']);
Route::post('/markets_chart', [ApiController::class, 'markets_chart']);
Route::post('/tagihan_status_chart', [ApiController::class, 'tagihan_status_chart']);
Route::post('/pasar_list', [ApiController::class, 'getPasarList']);
Route::post('/update-password', [ApiController::class, 'updatePassword']);
Route::post('/update-petugas-password', [ApiController::class, 'updatePetugasPassword']);
Route::post('/get-qris-code', [ApiController::class, 'getQrisCode']);