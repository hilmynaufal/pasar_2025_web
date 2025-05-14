<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http; // Tambahkan ini
use Illuminate\Support\Facades\Log;

class Transaksi extends Component
{
    public $transaksi;
    public string $date;
    public string $total_transaksi;
    public string $total_nominal;
    // public function mount() {
    //     $this->fetchDataPedagang();
    // }

    public function fetchDataTransaksi()
    { // Tambahkan metode baru
        Log::info('Tanggal yang digunakan: ' . $this->date); // Menambahkan log untuk mencetak tanggal

        $response = Http::post("https://hirumi.xyz/pasar_2025_web/api" . '/laporan', ["tanggal" => $this->date]); // Ganti 'URL_API' dengan URL API yang sesuai
        $this->transaksi = $response->json()['data'];

        $response = Http::post("https://hirumi.xyz/pasar_2025_web/api" . '/dashboard', ["tanggal" => $this->date]); // Ganti 'URL_API' dengan URL API yang sesuai
        $this->total_transaksi = $response->json()['data']['jumlah_transaksi'];
        $this->total_nominal = $response->json()['data']['total_nominal'];
    }

    public function mount()
    {
        $this->date = date('Y-m-d');
        $this->fetchDataTransaksi();
        Log::info('Tanggal yang digunakan: ' . $this->date); // Menambahkan log untuk mencetak tanggal
    }

    public function render()
    {
        return view('livewire.transaksi');
    }
}
