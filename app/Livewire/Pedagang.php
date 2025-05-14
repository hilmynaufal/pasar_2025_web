<?php

namespace App\Livewire;
use Illuminate\Support\Facades\Http; // Tambahkan ini

use Livewire\Component;

class Pedagang extends Component
{
    public $pedagang;
    // public $date;
    
    public function mount() {
        // $this->date = date("Y-m-d");
        $this->fetchDataPedagang();
    }
    
    public function fetchDataPedagang() { // Tambahkan metode baru
        $response = Http::post("https://hirumi.xyz/pasar_2025_web/api" . '/pedagang', ['nama_pasar' => session('nama_pasar')]); // Ganti 'URL_API' dengan URL API yang sesuai

        $this->pedagang = $response->json()['data'];
        // print($this->pedagang[0]['alamat']);
    }
    public function render()
    {
        return view('livewire.pedagang');
    }


}
