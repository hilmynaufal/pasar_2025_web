<?php

use App\Http\Controllers\Api\ApiController;
use App\Livewire\ChartPage;
use App\Livewire\Home;
use App\Livewire\Login;
use App\Livewire\Pedagang;
use App\Livewire\Tagihan;
use App\Livewire\Transaksi;
use Illuminate\Support\Facades\Route;

//web

Route::middleware(['checklogin'])->group(function () {
    Route::get('/home', Home::class)->name('home');
    Route::get('/', Home::class)->name('/');
    Route::get('/pedagang', Pedagang::class)->name('pedagang');
    Route::get('/tagihan', Tagihan::class)->name('tagihan');
    Route::get('/transaksi', Transaksi::class)->name('transaksi');
});


Route::get('/login', Login::class)->name('login');  



// Route::get('/chart', function () {
//     return view('livewire/chart-page');
// })->name('chart');
