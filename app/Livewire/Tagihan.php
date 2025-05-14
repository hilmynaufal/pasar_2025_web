<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http; // Tambahkan ini
class Tagihan extends Component
{

    public  $tagihan;
    public $date;
    public function mount() {
        $this->date = now()->toDateString();
        // var_dump($this->date);
        $this->fetchDataPedagang();
    }
    
    public function fetchDataPedagang() { // Tambahkan metode baru
        $response = Http::post("https://hirumi.xyz/pasar_2025_web/api" . '/tagihan', ["tanggal" => "2025-04-17"]); // Ganti 'URL_API' dengan URL API yang sesuai
        $this->tagihan = $response->json()['data'];
        // print($this->pedagang[0]['alamat']);
    }
    public function render()
    {
        return view('livewire.tagihan');
    }
}
