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
Route::post('/bayar', [ApiController::class, 'bayar']);
Route::post('/dashboard', [ApiController::class, 'dashboard']); // Rute baru untuk dashboard
Route::post('/laporan', [ApiController::class, 'laporan']); // Rute baru untuk laporan

Route::post('/login', [ApiController::class, 'login']);
Route::post('/revenue_chart', [ApiController::class, 'revenue_chart']);
Route::post('/home', [ApiController::class, 'home']);
Route::post('/admin', [ApiController::class, 'admin']);
Route::post('/tagihan_stat', [ApiController::class, 'tagihan_stat']);
Route::post('/home_users_stat', [ApiController::class, 'home_users_stat']);