<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http; // Tambahkan ini

class Home extends Component
{
    public $chart;
    public $date;
    public $data;
    public $colors;
    public function mount()
    {
        $date = date('Y-m-d');
        $this->date = $date;
        $response = Http::post("https://hirumi.xyz/pasar_2025_web/api" . '/home', ["tanggal" => $date, "nama_pasar" => session('nama_pasar')]); // Ganti 'URL_API' dengan URL API yang sesuai



        $this->data = json_decode($response)->data;

        // Membuat warna random dengan format 'rgba(255, 99, 132, 0.2)' sebanyak jumlah data di data->users_stat dan masukkan ke $colors
        $colors = [];
        $jumlah = isset($this->data->users_stat) ? count($this->data->users_stat) : 0;
        for ($i = 0; $i < $jumlah; $i++) {
            $r = mt_rand(0, 255);
            $g = mt_rand(0, 255);
            $b = mt_rand(0, 255);
            $warna = "rgba($r, $g, $b, 1)";
            $colors[] = $warna;
        }
        $this->colors = $colors;
    }
    public function render()
    {
        return view('livewire.home');
    }


}
