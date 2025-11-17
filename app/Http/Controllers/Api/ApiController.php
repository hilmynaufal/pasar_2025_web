<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Mail\InvoiceMail;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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
        $nama_pasar = $request->nama_pasar; // Tambahkan parameter nama_pasar

        $query = DB::table('pedagang')
            ->select('*');

        // Tambahkan filter nama_pasar jika ada
        if (!empty($nama_pasar)) {
            $query->where('nama_pasar', $nama_pasar);
        }

        if (!empty($id_kios)) {
            $query->whereRaw('BINARY id_kios = ?', $id_kios);
        } else if (!empty($string)) {
            $query->where('nama', 'like', '%' . $string . '%');
        }
        // Jika id_kios dan string kosong, tidak perlu menambahkan kondisi WHERE tambahan
        // karena query dasar (yang mungkin sudah difilter oleh nama_pasar) akan mengambil semua.

        $pedagang = $query->orderBy('nama', 'asc')->paginate(10);

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
            $query->whereRaw("BINARY id_kios = ?", [$id_kios]);
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
        // Validasi input yang diperlukan

        $now = now();
        $id_tagihan = $request->id_tagihan;
        $id_petugas = $request->id_petugas;

        // Generate unique transaction ID
        $id = 'RTB-' . str_replace(' ', '', str_replace(':', '', str_replace('-', '', $now))) . rand(1000, 9999);

        // Mulai database transaction
        DB::beginTransaction();

        try {
            // Validasi status tagihan sebelum pembayaran
            $tagihan = DB::table('tagihan')
                ->where('id', $id_tagihan)
                ->where('status', '!=', 1) // Pastikan belum dibayar
                ->first();

            if (!$tagihan) {
                throw new \Exception('Tagihan tidak ditemukan atau sudah dibayar');
            }

            // Validasi nominal pembayaran
            if ($request->nominal_transaksi <= 0) {
                throw new \Exception('Nominal pembayaran harus lebih dari 0');
            }

            // Insert transaksi
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
                'status' => "SUCCESS",
                // 'created_at' => $now,
                // 'updated_at' => $now
            ]);

            if (!$transaksi) {
                throw new \Exception('Gagal menyimpan data transaksi');
            }

            // Update status tagihan
            $updateTagihan = DB::table('tagihan')
                ->where('id', $id_tagihan)
                ->update([
                    'status' => 1,
                    'salesman' => $request->nama_petugas,
                    'transaction_id' => $id,
                    // 'updated_at' => $now
                ]);

            if (!$updateTagihan) {
                throw new \Exception('Gagal mengupdate status tagihan');
            }

            // Ambil data transaksi yang baru dibuat
            $data_transaksi = DB::table('transaksi')->where('id', $id)->first();
            
            if (!$data_transaksi) {
                throw new \Exception('Data transaksi tidak ditemukan');
            }

            // Ambil email pedagang berdasarkan kode_kios
            $pedagang = DB::table('pedagang')
                ->select('email', 'nama')
                ->where('kode_kios', $request->kode_kios)
                ->first();
            
            // Commit transaction sebelum mengirim email
            DB::commit();

            // Log transaksi berhasil
            Log::info('Transaksi berhasil', [
                'transaction_id' => $id,
                'id_tagihan' => $id_tagihan,
                'id_petugas' => $id_petugas,
                'nominal' => $request->nominal_transaksi,
                'timestamp' => $now
            ]);
            
            // Kirim email invoice jika email tersedia (setelah commit)
            // FITUR EMAIL INVOICE DINONAKTIFKAN
            // if ($pedagang && !empty($pedagang->email)) {
            //     try {
            //         $emailData = [
            //             'id' => $data_transaksi->id,
            //             'tanggal_transaksi' => $data_transaksi->tanggal_transaksi,
            //             'nominal_transaksi' => $data_transaksi->nominal_transaksi,
            //             'metode_pembayaran' => $data_transaksi->metode_pembayaran,
            //             'nama_pedagang' => $data_transaksi->nama_pedagang,
            //             'kode_kios' => $data_transaksi->kode_kios,
            //             'jenis_akun' => $data_transaksi->jenis_akun,
            //             'nama_pasar' => $data_transaksi->nama_pasar,
            //             'nama_petugas' => $data_transaksi->nama_petugas,
            //             'nama_distrik' => $data_transaksi->nama_distrik,
            //             'status' => $data_transaksi->status
            //         ];
            //
            //         Mail::to($pedagang->email)->send(new InvoiceMail($emailData));
            //
            //         Log::info('Email invoice berhasil dikirim', [
            //             'transaction_id' => $id,
            //             'email' => $pedagang->email
            //         ]);
            //     } catch (\Exception $e) {
            //         // Log error jika pengiriman email gagal, tapi transaksi tetap berhasil
            //         Log::error('Gagal mengirim email invoice: ' . $e->getMessage(), [
            //             'transaction_id' => $id,
            //             'email' => $pedagang->email ?? 'tidak ada'
            //         ]);
            //     }
            // }
            
            return response()->json([
                'status' => 1, 
                'message' => 'Pembayaran berhasil', 
                'data' => [$data_transaksi]
            ], 200);

        } catch (\Exception $e) {
            // Rollback transaction jika ada error
            DB::rollback();
            
            // Log error
            Log::error('Gagal melakukan pembayaran: ' . $e->getMessage(), [
                'id_tagihan' => $id_tagihan,
                'id_petugas' => $id_petugas,
                'nominal' => $request->nominal_transaksi ?? 0,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'status' => 0, 
                'message' => 'Gagal melakukan pembayaran: ' . $e->getMessage()
            ], 400);
        }
    }

    public function laporan(Request $request)
    {

        $tanggal = $request->tanggal;
        $id = $request->id_petugas;
        $nama_pasar = $request->nama_pasar;
        $status = $request->status;

        $query = DB::table('transaksi')
            ->select('*')
            ->where(DB::raw('DATE(tanggal_transaksi)'), $tanggal);

        if (!empty($id)) {
            $query->where('id_petugas', $id);
        }

        if (!empty($nama_pasar)) {
            $query->where('nama_pasar', $nama_pasar);
        }

        if (!empty($status)) {
            $query->where('status', $status);
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
        $status = $request->input('status');

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

        // Tambahkan kondisi jika status tidak kosong
        if (!empty($status)) {
            $query->where('status', $status);
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

        // Tambahkan kondisi jika status tidak kosong
        if (!empty($status)) {
            $queryJumlahTransaksi->where('status', $status);
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

    public function getFilterOptions(Request $request)
    {
        // Get distinct pasar (markets)
        $pasarList = DB::table('transaksi')
            ->select('nama_pasar')
            ->distinct()
            ->whereNotNull('nama_pasar')
            ->where('nama_pasar', '!=', '')
            ->orderBy('nama_pasar', 'asc')
            ->pluck('nama_pasar')
            ->toArray();

        // Get distinct petugas (officers)
        $petugasList = DB::table('transaksi')
            ->select('id_petugas', 'nama_petugas')
            ->distinct()
            ->whereNotNull('id_petugas')
            ->whereNotNull('nama_petugas')
            ->where('nama_petugas', '!=', '')
            ->orderBy('nama_petugas', 'asc')
            ->get()
            ->toArray();

        // Get distinct status
        $statusList = DB::table('transaksi')
            ->select('status')
            ->distinct()
            ->whereNotNull('status')
            ->where('status', '!=', '')
            ->orderBy('status', 'asc')
            ->pluck('status')
            ->toArray();

        return response()->json([
            'status' => 1,
            'data' => [
                'pasar' => $pasarList,
                'petugas' => $petugasList,
                'status' => $statusList
            ]
        ], 200);
    }

    public function generateQrCode(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'pedagang_id' => 'required|integer|exists:pedagang,id'
            ]);

            $pedagang_id = $request->pedagang_id;

            // Ambil data pedagang
            $pedagang = DB::table('pedagang')
                ->where('id', $pedagang_id)
                ->first();

            if (!$pedagang) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Pedagang tidak ditemukan'
                ], 404);
            }

            // Pastikan directory qr_codes ada
            $qrCodesDir = public_path('qr_codes');
            if (!is_dir($qrCodesDir)) {
                mkdir($qrCodesDir, 0755, true);
            }

            // Generate nama file QR code
            $fileName = 'qr_' . $pedagang_id . '_' . time() . '.png';
            $filePath = public_path('qr_codes/' . $fileName);

            // Generate QR code dengan id_kios sebagai content
            QrCode::format('png')
                ->size(300)
                ->margin(2)
                ->generate($pedagang->id_kios, $filePath);

            // Update field qr_code_file di tabel pedagang
            DB::table('pedagang')
                ->where('id', $pedagang_id)
                ->update([
                    'qr_code_file' => $fileName,
                    'updated_at' => now()
                ]);

            Log::info('QR Code berhasil digenerate', [
                'pedagang_id' => $pedagang_id,
                'id_kios' => $pedagang->id_kios,
                'file_name' => $fileName
            ]);

            return response()->json([
                'status' => 1,
                'message' => 'QR Code berhasil digenerate',
                'data' => [
                    'qr_code_file' => $fileName,
                    'qr_code_url' => url('qr_codes/' . $fileName)
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Gagal generate QR Code: ' . $e->getMessage(), [
                'pedagang_id' => $request->pedagang_id ?? null,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 0,
                'message' => 'Gagal generate QR Code: ' . $e->getMessage()
            ], 500);
        }
    }

}
