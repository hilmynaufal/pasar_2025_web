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

    // Filter options
    public $pasar_options = [];
    public $petugas_options = [];
    public $status_options = [];

    // Filter values
    public $filter_pasar = '';
    public $filter_petugas = '';
    public $filter_status = '';

    // public function mount() {
    //     $this->fetchDataPedagang();
    // }

    public function fetchDataTransaksi()
    { // Tambahkan metode baru
        Log::info('Tanggal yang digunakan: ' . $this->date); // Menambahkan log untuk mencetak tanggal

        $response = Http::post(env('API_BASE_URL') . '/laporan', ["tanggal" => $this->date]); // Mengambil URL API dari .env
        $this->transaksi = $response->json()['data'];

        $response = Http::post(env('API_BASE_URL') . '/dashboard', ["tanggal" => $this->date]); // Mengambil URL API dari .env
        $this->total_transaksi = $response->json()['data']['jumlah_transaksi'];
        $this->total_nominal = $response->json()['data']['total_nominal'];
    }

    public function fetchFilterOptions()
    {
        $response = Http::post(env('API_BASE_URL') . '/filter_options');
        $data = $response->json()['data'];

        $this->pasar_options = $data['pasar'];
        $this->petugas_options = $data['petugas'];
        $this->status_options = $data['status'];
    }

    public function mount()
    {
        $this->date = date('Y-m-d');
        $this->fetchFilterOptions();
        $this->fetchDataTransaksi();
        Log::info('Tanggal yang digunakan: ' . $this->date); // Menambahkan log untuk mencetak tanggal
    }

    public function render()
    {
        return view('livewire.transaksi');
    }
}
