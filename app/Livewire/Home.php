<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class Home extends Component
{
    public $chart;
    public $date;
    public $data;
    public $colors = [];
    public $start_date;
    public $end_date;
    public $selected_pasar = "";
    public $pasar_list = [];
    public $role;
    public $latitude;
    public $longitude;

    public function __construct()
    {
        // Initialize $data with default structure to prevent undefined errors
        $this->data = (object) [
            'users_stat' => [],
            'total_nominal_hari_ini' => 0,
            'jumlah_transaksi_hari_ini' => 0,
            'total_nominal_30_hari_terakhir' => 0,
            'jumlah_transaksi_30_hari_terakhir' => 0
        ];
    }

    public function mount()
    {
        $date = date('Y-m-d');
        $this->date = $date;
        $this->role = session('role');
        $this->start_date = date('Y-m-d', strtotime('-30 days'));
        $this->end_date = date('Y-m-d');

        // Load pasar list jika superadmin
        if ($this->role === 'superadmin') {
            $this->pasar_list = DB::table('pasar')->orderBy('nama', 'asc')->get();
        }

        // Ambil koordinat dari tabel pasar berdasarkan nama_pasar
        $this->loadKoordinat();

        $this->loadData();
    }

    public function loadData()
    {
        $params = [
            "tanggal" => $this->date,
            "nama_pasar" => $this->selected_pasar != '' ? $this->selected_pasar : session('nama_pasar'),
            "role" => $this->role,
            "start_date" => $this->start_date,
            "end_date" => $this->end_date
        ];

        // dd($params);

        try {
            $response = Http::post(env('API_BASE_URL') . '/home', $params);
            $responseData = json_decode($response->body());

            if ($responseData && isset($responseData->data)) {
                $this->data = $responseData->data;
            }
        } catch (\Exception $e) {
            // Keep default data structure if API call fails
            \Log::error('Failed to load home data: ' . $e->getMessage());
        }

        // Ensure users_stat is always an array
        if (!isset($this->data->users_stat) || !is_array($this->data->users_stat)) {
            $this->data->users_stat = [];
        }

        // Membuat warna random dengan format 'rgba(255, 99, 132, 0.2)' sebanyak jumlah data di data->users_stat dan masukkan ke $colors
        $colors = [];
        $jumlah = count($this->data->users_stat);
        for ($i = 0; $i < $jumlah; $i++) {
            $r = mt_rand(0, 255);
            $g = mt_rand(0, 255);
            $b = mt_rand(0, 255);
            $warna = "rgba($r, $g, $b, 1)";
            $colors[] = $warna;
        }
        $this->colors = $colors;

        $this->dispatch('dataLoaded');
    }

    public function loadKoordinat()
    {
        // Tentukan nama pasar yang akan digunakan
        $nama_pasar = $this->selected_pasar != '' ? $this->selected_pasar : session('nama_pasar');

        // Query koordinat dari tabel pasar
        $pasar = DB::table('pasar')
            ->where('nama', $nama_pasar)
            ->first();

        if ($pasar && !empty($pasar->koordinat)) {
            // Parse koordinat (format: "latitude, longitude")
            $koordinat = explode(',', $pasar->koordinat);
            if (count($koordinat) == 2) {
                $this->latitude = trim($koordinat[0]);
                $this->longitude = trim($koordinat[1]);
            } else {
                // Default koordinat jika format tidak sesuai
                $this->latitude = -6.974991181293871;
                $this->longitude = 107.56163059688262;
            }
        } else {
            // Default koordinat jika pasar tidak ditemukan atau koordinat kosong
            $this->latitude = -6.974991181293871;
            $this->longitude = 107.56163059688262;
        }
    }

    public function applyFilters()
    {
        $this->loadKoordinat(); // Update koordinat saat filter berubah
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.home', ['data' => $this->data]);
    }
}
