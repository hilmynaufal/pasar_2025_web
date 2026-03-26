<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http; // Tambahkan ini

class Tagihan extends Component
{

    public $tagihan;
    public $date;

    // Filter options
    public $pasar_options = [];
    public $petugas_options = [];
    public $distrik_options = [];

    // Filter values
    public $filter_pasar = '';
    public $filter_petugas = '';
    public $filter_distrik = '';
    public function mount()
    {
        $this->date = now()->toDateString();
        // var_dump($this->date);
        $this->fetchFilterOptions();
        $this->fetchDataPedagang();
    }

    public function fetchDataPedagang()
    { // Tambahkan metode baru
        $response = Http::post(env('API_BASE_URL') . '/tagihan', ["tanggal" => "2025-04-17"]); // Ganti 'URL_API' dengan URL API yang sesuai

        $this->tagihan = $response->json()['data'];
        // print($this->pedagang[0]['alamat']);
    }

    public function fetchFilterOptions()
    {
        $data = [];
        if (session('nama_pasar') != "All") {
            $data['nama_pasar'] = session('nama_pasar');
        } else {
            $data['nama_pasar'] = null;
        }
        $response = Http::post(env('API_BASE_URL') . '/filter_options_tagihan', $data);
        $data = $response->json()['data'];

        $this->pasar_options = $data['pasar'];
        $this->petugas_options = $data['petugas'];
        $this->distrik_options = $data['distrik'];
    }
    public function render()
    {
        return view('livewire.tagihan');
    }
}
