<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ApiController extends Controller
{
    public function test()
    {
        // $produk = Produk::all(); // Ambil semua data produk
        return response()->json("ahay"); // Kembalikan dalam format JSON
    }

    public function cari_pedagang(Request $request)
    {
        $id_kios = $request->id_kios;
        $string = $request->keyword;

        if (!empty($id_kios)) {
            $pedagang = DB::table('pedagang')
                ->select('*')
                ->whereRaw('BINARY id_kios = ?', $id_kios)
                ->orderBy('nama', 'asc')
                ->paginate(10);
        } else if (empty($string)) {
            $pedagang = DB::table('pedagang')
                ->select('*')
                ->orderBy('nama', 'asc')
                ->paginate(10);
        } else {
            $pedagang = DB::table('pedagang')
                ->select('*')
                ->where('nama', 'like', '%' . $string . '%')
                ->orderBy('nama', 'asc')
                ->paginate(10);
        }

        $array = ['status' => 1, 'data' => $pedagang->items(), 'pages' => $pedagang->lastPage()];
        return response()->json($array, 200);
    }

    public function pedagang(Request $request)
    {
        $nama_pasar = $request->nama_pasar;

        if (empty($nama_pasar)) {
            $pedagang = DB::table('pedagang')
                ->select('*')
                ->orderBy('nama', 'asc')
                ->get();
        } else {
            $pedagang = DB::table('pedagang')
                ->select('*')
                ->where('nama_pasar', $nama_pasar)
                ->orderBy('nama', 'asc')
                ->get();
        }
        
        $array = ['status' => 1, 'data' => $pedagang->toArray(), 'pages' => 0];
        return response()->json($array, 200);
    }

    public function tagihan(Request $request)
    {

        // $id_kios = $request->id_kios;
        // $id_pedagang = $request->id_kios;
        $tanggal = $request->tanggal;

        // Jika id_kios kosong, gunakan id_pedagang
        $id_kios = $request->id_kios;
        $id_pedagang = $request->id_pedagang;
        $nama_pasar = $request->nama_pasar;

        $query = DB::table('tagihan')
            ->select('*')
            ->orderBy('id', 'asc');

        if (!empty($id_kios)) {
            $query->whereRaw('BINARY id_kios = ?', [$id_kios]);
        } else if (!empty($id_pedagang)) {
            $query->whereRaw('BINARY pedagang_id = ?', [$id_pedagang]);
        }

        // Tambahan: jika nama pasar tidak kosong
        if (!empty($nama_pasar)) {
            $query->where('merchant_id', $nama_pasar);
        }

        if (!empty($tanggal)) {
            $query->where('tanggal_tagihan', $tanggal);
        }

        $tagihan = $query->get()->toArray();

        $array = ['status' => 1, 'data' => $tagihan, 'pages' => 0];
        return response()->json($array, 200);
    }



    public function bayar(Request $request)
    {
        $now = now();

        $id_tagihan = $request->id_tagihan;
        $id_petugas = $request->id_petugas;

        $id = 'RTB-' . str_replace(' ', '', str_replace(':', '', str_replace('-', '', $now))) . rand(1000, 9999);

        $transaksi = DB::table('transaksi')->insert([
            'id' => $id,
            'nominal_transaksi' => $request->nominal_transaksi,
            'tanggal_transaksi' => $now,
            'metode_pembayaran' => $request->metode_pembayaran,
            'nama_pedagang' => $request->nama_pedagang,
            'kode_kios' => $request->kode_kios,
            'jenis_akun' => $request->jenis_akun,
            'nama_pasar' => $request->nama_pasar,
            'nama_petugas' => $request->nama_petugas,
            'nama_distrik' => $request->nama_distrik,
            'id_petugas' => $request->id_petugas,
            'status' => "SUCCESS"
        ]);

        DB::table('tagihan')->where('id', $id_tagihan)->update([
            'status' => 1,
            'salesman' => $request->nama_petugas,
            'transaction_id' => $id
        ]);

        if ($transaksi) {
            $data_transaksi = DB::table('transaksi')->where('id', $id)->first();
            return response()->json(['status' => 1, 'message' => 'Pembayaran berhasil', 'data' => [$data_transaksi]], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Gagal melakukan pembayaran'], 200);
        }
    }

    public function laporan(Request $request)
    {

        $tanggal = $request->tanggal;
        $id = $request->id_petugas;
        $nama_pasar = $request->nama_pasar;

        $query = DB::table('transaksi')
            ->select('*')
            ->where(DB::raw('DATE(tanggal_transaksi)'), $tanggal);

        if (!empty($id)) {
            $query->where('id_petugas', $id);
        }

        if (!empty($nama_pasar)) {
            $query->where('nama_pasar', $nama_pasar);
        }

        $laporan = $query->get()->toArray();

        $array = ['status' => 1, 'data' => $laporan, 'pages' => 0];
        return response()->json($array, 200);
    }

    public function login(Request $request)
    {

        $username = $request->input("username");
        $password = $request->input("password");

        $petugas = DB::table('petugas')->where('username', $username)->where('password', $password)->first();

        if ($petugas) {
            return response()->json(['status' => 1, 'message' => 'Login berhasil', 'data' => $petugas], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Login gagal'], 200);
        }
    }

    public function admin(Request $request)
    {

        $username = $request->input("username");
        $password = $request->input("password");

        $admin = DB::table('admin')->where('username', $username)->where('password', $password)->first();

        if ($admin) {
            return response()->json(['status' => 1, 'message' => 'Login berhasil', 'data' => $admin], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Login gagal'], 200);
        }
    }

    public function tagihan_stat(Request $request)
    {

        // $id = $request->input("id");
        $tanggal = $request->input('tanggal');
        $nama_pasar = $request->nama_pasar;

        $query = DB::table('tagihan')
            ->select(DB::raw('id, status'))
            ->where('tanggal_tagihan', $tanggal);

        // Jika nama pasar tidak kosong, tambahkan kondisi where
        if (!empty($nama_pasar)) {
            $query->where('merchant_id', $nama_pasar);
        }
            // ->groupBy('status');

        $result = $query->get();

        // Inisialisasi variabel total tagihan
        $totalSudahDibayar = 0;
        $totalBelumDibayar = 0;

        foreach ($result as $row) {
            if ($row->status == 'sudah_dibayar' || $row->status == 1) {
                $totalSudahDibayar += 1;
            } else if ($row->status == 'belum_dibayar' || $row->status == 0) {
                $totalBelumDibayar += 1;
            }
        }

        return response()->json([
            'status' => 1,
            'message' => 'Data dashboard berhasil diambil',
            'data' => [
                'totalSudahDibayar' => $totalSudahDibayar,
                'totalBelumDibayar' => $totalBelumDibayar
            ]
        ], 200);
    }

    public function dashboard(Request $request)
    {

        $id = $request->input("id");
        $tanggal = $request->input('tanggal');
        $nama_pasar = $request->input('nama_pasar');

        $query = DB::table('transaksi')
            ->select(DB::raw('id_petugas, SUM(nominal_transaksi) as total_nominal'))
            ->where(DB::raw('DATE(tanggal_transaksi)'), $tanggal)
            ->groupBy('id_petugas');

        if (!empty($id)) {
            $query->where('id_petugas', $id);
        }

        // Tambahkan kondisi jika nama_pasar tidak kosong
        if (!empty($nama_pasar)) {
            $query->where('nama_pasar', $nama_pasar);
        }

        $totalNominal = $query->get()->toArray();

        if (empty($totalNominal)) {
            $totalNominal = "0";
        } else {
            $totalNominal = $totalNominal[0]->total_nominal;
        }

        $queryJumlahTransaksi = DB::table('transaksi')
            ->where(DB::raw('DATE(tanggal_transaksi)'), $tanggal);

        if (!empty($id)) {
            $queryJumlahTransaksi->where('id_petugas', $id);
        }

        // Tambahkan kondisi jika nama_pasar tidak kosong
        if (!empty($nama_pasar)) {
            $queryJumlahTransaksi->where('nama_pasar', $nama_pasar);
        }

        $jumlahTransaksi = $queryJumlahTransaksi->count();

        return response()->json([
            'status' => 1,
            'message' => 'Data dashboard berhasil diambil',
            'data' => [
                "total_nominal" => $totalNominal,
                "jumlah_transaksi" => $jumlahTransaksi
            ]
        ], 200);
    }

    public function home(Request $request)
    {
        // $id = $request->input("id");
        $tanggal = $request->input('tanggal');
        $nama_pasar = $request->input('nama_pasar');

        // Query untuk total nominal dan jumlah transaksi untuk hari ini
        $queryHariIni = DB::table('transaksi')
            ->select(DB::raw('SUM(nominal_transaksi) as total_nominal, status'))
            ->where(DB::raw('DATE(tanggal_transaksi)'), $tanggal)
            ->groupBy('status');

        // Tambahkan kondisi jika nama_pasar tidak kosong
        if (!empty($nama_pasar)) {
            $queryHariIni->where('nama_pasar', $nama_pasar);
        }

        $totalNominalHariIni = $queryHariIni->get()->toArray();

        if (empty($totalNominalHariIni)) {
            $totalNominalHariIni = "0";
        } else {
            $totalNominalHariIni = $totalNominalHariIni[0]->total_nominal;
        }

        $queryJumlahTransaksiHariIni = DB::table('transaksi')
            ->where(DB::raw('DATE(tanggal_transaksi)'), $tanggal);

        // Tambahkan kondisi jika nama_pasar tidak kosong
        if (!empty($nama_pasar)) {
            $queryJumlahTransaksiHariIni->where('nama_pasar', $nama_pasar);
        }

        $jumlahTransaksiHariIni = $queryJumlahTransaksiHariIni->count();

        // Query untuk total nominal dan jumlah transaksi untuk 30 hari terakhir
        $query30HariTerakhir = DB::table('transaksi')
            ->select(DB::raw('SUM(nominal_transaksi) as total_nominal, status'))
            ->where('tanggal_transaksi', '>=', Carbon::now()->subDays(30)->startOfDay())
            ->groupBy('status');

        // Tambahkan kondisi jika nama_pasar tidak kosong
        if (!empty($nama_pasar)) {
            $query30HariTerakhir->where('nama_pasar', $nama_pasar);
        }

        $totalNominal30HariTerakhir = $query30HariTerakhir->get()->toArray();

        if (empty($totalNominal30HariTerakhir)) {
            $totalNominal30HariTerakhir = "0";
        } else {
            $totalNominal30HariTerakhir = $totalNominal30HariTerakhir[0]->total_nominal;
        }

        $queryJumlahTransaksi30HariTerakhir = DB::table('transaksi')
            ->where('tanggal_transaksi', '>=', Carbon::now()->subDays(30)->startOfDay());

        // Tambahkan kondisi jika nama_pasar tidak kosong
        if (!empty($nama_pasar)) {
            $queryJumlahTransaksi30HariTerakhir->where('nama_pasar', $nama_pasar);
        }

        $jumlahTransaksi30HariTerakhir = $queryJumlahTransaksi30HariTerakhir->count();

        $data_home_users_stat = $this->home_users_stat($nama_pasar);

        return response()->json([
            'status' => 1,
            'message' => 'Data dashboard berhasil diambil',
            'data' => [
                "total_nominal_hari_ini" => $totalNominalHariIni,
                "jumlah_transaksi_hari_ini" => $jumlahTransaksiHariIni,
                "total_nominal_30_hari_terakhir" => $totalNominal30HariTerakhir,
                "jumlah_transaksi_30_hari_terakhir" => $jumlahTransaksi30HariTerakhir,
                "users_stat" => $data_home_users_stat
            ]
        ], 200);
    }

    public function home_users_stat($nama_pasar)
    {

        // Query untuk mengambil jumlah transaksi per id_petugas dalam 30 hari terakhir
        $query = DB::table('transaksi')
            ->select('nama_petugas', DB::raw('COUNT(*) as jumlah_transaksi'))
            ->where('tanggal_transaksi', '>=', Carbon::now()->subDays(30)->startOfDay())
            ->groupBy('nama_petugas');

        // Tambahkan filter nama_pasar jika ada
        if (!empty($nama_pasar)) {
            $query->where('nama_pasar', $nama_pasar);
        }

        $data = $query->get();

        return $data;
    }


    public function revenue_chart(Request $request)
    {

        $tanggal = $request->tanggal;
        $id = $request->id_petugas;
        $nama_pasar = $request->nama_pasar;

        // Step 1: Buat array tanggal 30 hari terakhir
        $dates = collect();
        for ($i = 29; $i >= 0; $i--) {
            $dates->push(Carbon::now()->subDays($i)->format('Y-m-d'));
        }

        // Step 2: Ambil data dari DB
        $query = DB::table('transaksi')
            ->select(DB::raw('DATE(tanggal_transaksi) as tanggal'), DB::raw('SUM(nominal_transaksi) as total_nominal'))
            ->where('tanggal_transaksi', '>=', Carbon::now()->subDays(29)->startOfDay());

        // Tambahkan kondisi jika nama_pasar tidak kosong
        if (!empty($nama_pasar)) {
            $query->where('nama_pasar', $nama_pasar);
        }

        $rawData = $query
            ->groupBy(DB::raw('DATE(tanggal_transaksi)'))
            ->pluck('total_nominal', 'tanggal'); // hasil: ['2025-04-15' => 10000, ...]

        // Step 3: Gabungkan hasil ke array final
        $tanggalArray = [];
        $nominalArray = [];

        foreach ($dates as $date) {
            $tanggalArray[] = $date;
            $nominalArray[] = $rawData[$date] ?? 0; // isi 0 kalau tidak ada data
        }

        $result = [
            'tanggal' => $tanggalArray,
            'nominal' => $nominalArray,
        ];

        $array = ['status' => 1, 'data' => $result, 'pages' => 0];
        return response()->json($array, 200);
    }


}
