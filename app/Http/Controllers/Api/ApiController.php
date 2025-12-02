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
        $role = $request->role;

        // Superadmin dapat melihat semua pedagang tanpa filter
        if ($role === 'superadmin' || empty($nama_pasar)) {
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

    public function hapus_transaksi(Request $request)
    {
        // Cek apakah user adalah superadmin
        if (session('role') !== 'superadmin') {
            return response()->json([
                'status' => 0,
                'message' => 'Akses ditolak. Hanya superadmin yang dapat menghapus transaksi.'
            ], 403);
        }

        $transaction_id = $request->transaction_id;

        // Mulai database transaction
        DB::beginTransaction();

        try {
            // Ambil data transaksi berdasarkan ID
            $transaksi = DB::table('transaksi')
                ->where('id', $transaction_id)
                ->first();

            if (!$transaksi) {
                throw new \Exception('Transaksi tidak ditemukan');
            }

            // Cari tagihan berdasarkan transaction_id
            $tagihan = DB::table('tagihan')
                ->where('transaction_id', $transaction_id)
                ->first();

            // Reset tagihan jika ditemukan
            if ($tagihan) {
                $updateTagihan = DB::table('tagihan')
                    ->where('transaction_id', $transaction_id)
                    ->update([
                        'status' => 0, // Reset ke belum dibayar
                        'salesman' => null,
                        'transaction_id' => null
                    ]);

                if (!$updateTagihan) {
                    throw new \Exception('Gagal mereset status tagihan');
                }

                Log::info('Tagihan berhasil direset', [
                    'tagihan_id' => $tagihan->id,
                    'transaction_id' => $transaction_id
                ]);
            }

            // Hapus transaksi
            $deleteTransaksi = DB::table('transaksi')
                ->where('id', $transaction_id)
                ->delete();

            if (!$deleteTransaksi) {
                throw new \Exception('Gagal menghapus transaksi');
            }

            // Commit transaction
            DB::commit();

            Log::info('Transaksi berhasil dihapus', [
                'transaction_id' => $transaction_id,
                'deleted_by' => $request->deleted_by ?? 'unknown'
            ]);

            return response()->json([
                'status' => 1,
                'message' => 'Transaksi berhasil dihapus dan tagihan telah direset'
            ], 200);

        } catch (\Exception $e) {
            // Rollback transaction jika ada error
            DB::rollback();

            Log::error('Gagal menghapus transaksi: ' . $e->getMessage(), [
                'transaction_id' => $transaction_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 0,
                'message' => 'Gagal menghapus transaksi: ' . $e->getMessage()
            ], 400);
        }
    }

    public function laporan(Request $request)
    {

        $tanggal = $request->tanggal;
        $id = $request->id_petugas;
        $nama_pasar = $request->nama_pasar;
        $nama_distrik = $request->nama_distrik;

        $query = DB::table('transaksi')
            ->select('*')
            ->where(DB::raw('DATE(tanggal_transaksi)'), $tanggal);

        if (!empty($id)) {
            $query->where('id_petugas', $id);
        }

        if (!empty($nama_pasar)) {
            $query->where('nama_pasar', $nama_pasar);
        }

        if (!empty($nama_distrik)) {
            $query->where('nama_distrik', $nama_distrik);
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

        $id = $request->input("id_petugas") ?? $request->input("id");
        $tanggal = $request->input('tanggal');
        $nama_pasar = $request->input('nama_pasar');
        $nama_distrik = $request->input('nama_distrik');



        $query = DB::table('transaksi')
            ->select(DB::raw('SUM(nominal_transaksi) as total_nominal'))
            ->where(DB::raw('DATE(tanggal_transaksi)'), $tanggal);
        // ->groupBy('nama_pasar');

        if (!empty($id)) {
            $query->select(DB::raw('id_petugas, SUM(nominal_transaksi) as total_nominal'))
                ->where('id_petugas', $id)
                ->groupBy('id_petugas');
        }

        //Tambahkan kondisi jika nama_pasar tidak kosong
        if (!empty($nama_pasar)) {
            $query->select(DB::raw('nama_pasar, SUM(nominal_transaksi) as total_nominal'))
                ->where('nama_pasar', $nama_pasar)
                ->groupBy('nama_pasar');
        }

        // Tambahkan kondisi jika nama_distrik tidak kosong
        if (!empty($nama_distrik)) {
            $query->select(DB::raw('nama_distrik, SUM(nominal_transaksi) as total_nominal'))
                ->where('nama_distrik', $nama_distrik)
                ->groupBy('nama_distrik');
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

        // Tambahkan kondisi jika nama_distrik tidak kosong
        if (!empty($nama_distrik)) {
            $queryJumlahTransaksi->where('nama_distrik', $nama_distrik);
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
        $role = $request->input('role');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        // Jika ada date range, gunakan itu. Jika tidak, gunakan tanggal hari ini dan 30 hari terakhir
        $dateStart = $start_date ?? $tanggal;
        $dateEnd = $end_date ?? $tanggal;
        $date30DaysAgo = $start_date ?? Carbon::now()->subDays(30)->format('Y-m-d');

        // Query untuk total nominal dan jumlah transaksi untuk hari ini (atau range yang dipilih)
        $queryHariIni = DB::table('transaksi')
            ->select(DB::raw('SUM(nominal_transaksi) as total_nominal, status'))
            ->where(DB::raw('DATE(tanggal_transaksi)'), '>=', $dateStart)
            ->where(DB::raw('DATE(tanggal_transaksi)'), '<=', $dateEnd)
            ->groupBy('status');

        // Superadmin tanpa filter pasar atau admin dengan filter pasar
        if ($role !== 'superadmin' && !empty($nama_pasar)) {
            $queryHariIni->where('nama_pasar', $nama_pasar);
        }

        $totalNominalHariIni = $queryHariIni->get()->toArray();

        if (empty($totalNominalHariIni)) {
            $totalNominalHariIni = "0";
        } else {
            $totalNominalHariIni = $totalNominalHariIni[0]->total_nominal;
        }

        $queryJumlahTransaksiHariIni = DB::table('transaksi')
            ->where(DB::raw('DATE(tanggal_transaksi)'), '>=', $dateStart)
            ->where(DB::raw('DATE(tanggal_transaksi)'), '<=', $dateEnd);

        if ($role !== 'superadmin' && !empty($nama_pasar)) {
            $queryJumlahTransaksiHariIni->where('nama_pasar', $nama_pasar);
        }

        $jumlahTransaksiHariIni = $queryJumlahTransaksiHariIni->count();

        // Query untuk total nominal dan jumlah transaksi untuk 30 hari terakhir (atau range yang dipilih)
        $query30HariTerakhir = DB::table('transaksi')
            ->select(DB::raw('SUM(nominal_transaksi) as total_nominal, status'))
            ->where('tanggal_transaksi', '>=', Carbon::parse($date30DaysAgo)->startOfDay())
            ->groupBy('status');

        if ($role !== 'superadmin' && !empty($nama_pasar)) {
            $query30HariTerakhir->where('nama_pasar', $nama_pasar);
        }

        $totalNominal30HariTerakhir = $query30HariTerakhir->get()->toArray();

        if (empty($totalNominal30HariTerakhir)) {
            $totalNominal30HariTerakhir = "0";
        } else {
            $totalNominal30HariTerakhir = $totalNominal30HariTerakhir[0]->total_nominal;
        }

        $queryJumlahTransaksi30HariTerakhir = DB::table('transaksi')
            ->where('tanggal_transaksi', '>=', Carbon::parse($date30DaysAgo)->startOfDay());

        if ($role !== 'superadmin' && !empty($nama_pasar)) {
            $queryJumlahTransaksi30HariTerakhir->where('nama_pasar', $nama_pasar);
        }

        $jumlahTransaksi30HariTerakhir = $queryJumlahTransaksi30HariTerakhir->count();

        $data_home_users_stat = $this->home_users_stat($nama_pasar, $role, $date30DaysAgo);

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

    public function home_users_stat($nama_pasar = "Pasar Cicalengka", $role = 'admin', $date30DaysAgo = null)
    {
        if ($date30DaysAgo === null) {
            $date30DaysAgo = Carbon::now()->subDays(30)->format('Y-m-d');
        }

        // Query untuk mengambil jumlah transaksi per id_petugas dalam 30 hari terakhir
        $query = DB::table('transaksi')
            ->select('nama_petugas', DB::raw('COUNT(*) as jumlah_transaksi'))
            ->where('tanggal_transaksi', '>=', Carbon::parse($date30DaysAgo)->startOfDay())
            ->groupBy('nama_petugas');

        // Superadmin tidak perlu filter nama_pasar
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
        $role = $request->role;
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        // dd($nama_pasar);

        // Jika ada date range, gunakan itu
        $daysBack = 29;
        if ($start_date && $end_date) {
            $startDate = Carbon::parse($start_date);
            $endDate = Carbon::parse($end_date);
            $daysBack = $startDate->diffInDays($endDate);
        }

        // Step 1: Buat array tanggal
        $dates = collect();
        for ($i = $daysBack; $i >= 0; $i--) {
            if ($start_date && $end_date) {
                $dates->push(Carbon::parse($start_date)->addDays($daysBack - $i)->format('Y-m-d'));
            } else {
                $dates->push(Carbon::now()->subDays($i)->format('Y-m-d'));
            }
        }

        // Step 2: Ambil data dari DB (GROUP BY nama_pasar dan tanggal)
        $query = DB::table('transaksi')
            ->select(
                DB::raw('DATE(tanggal_transaksi) as tanggal'),
                'nama_pasar',
                DB::raw('SUM(nominal_transaksi) as total_nominal')
            )
            ->whereNotNull('nama_pasar')
            ->where('nama_pasar', '!=', '');

        if ($start_date && $end_date) {
            $query->where('tanggal_transaksi', '>=', Carbon::parse($start_date)->startOfDay())
                ->where('tanggal_transaksi', '<=', Carbon::parse($end_date)->endOfDay());
        } else {
            $query->where('tanggal_transaksi', '>=', Carbon::now()->subDays(29)->startOfDay());
        }

        // Filter pasar jika bukan superadmin atau jika pasar dipilih
        if (!empty($nama_pasar)) {
            // Superadmin memilih pasar spesifik
            $query->where('nama_pasar', $nama_pasar);
        }

        $rawData = $query
            ->groupBy(DB::raw('DATE(tanggal_transaksi)'), 'nama_pasar')
            ->get();

        // Step 3: Organize data by pasar
        $pasarData = [];
        foreach ($rawData as $row) {
            if (!isset($pasarData[$row->nama_pasar])) {
                $pasarData[$row->nama_pasar] = [];
            }
            $pasarData[$row->nama_pasar][$row->tanggal] = $row->total_nominal;
        }

        // Step 4: Build datasets untuk setiap pasar
        $datasets = [];
        foreach ($pasarData as $namaPasar => $data) {
            $nominalArray = [];
            foreach ($dates as $date) {
                $nominalArray[] = $data[$date] ?? 0;
            }

            $datasets[] = [
                'nama_pasar' => $namaPasar,
                'data' => $nominalArray
            ];
        }

        // Step 5: Tambahkan data periode sebelumnya
        // Untuk yang ada pasar dipilih (baik admin maupun superadmin)
        if (!empty($nama_pasar)) {
            // Hitung jumlah hari dalam periode yang dipilih
            $numberOfDays = $daysBack + 1;

            // Hitung tanggal periode sebelumnya
            if ($start_date && $end_date) {
                $prevStartDate = Carbon::parse($start_date)->subDays($numberOfDays);
                $prevEndDate = Carbon::parse($start_date)->subDay();
            } else {
                $prevStartDate = Carbon::now()->subDays(29 + $numberOfDays);
                $prevEndDate = Carbon::now()->subDays(30);
            }

            // Query data periode sebelumnya
            $queryPrev = DB::table('transaksi')
                ->select(
                    DB::raw('DATE(tanggal_transaksi) as tanggal'),
                    DB::raw('SUM(nominal_transaksi) as total_nominal')
                )
                ->whereNotNull('nama_pasar')
                ->where('nama_pasar', '!=', '')
                ->where('nama_pasar', $nama_pasar)
                ->where('tanggal_transaksi', '>=', $prevStartDate->startOfDay())
                ->where('tanggal_transaksi', '<=', $prevEndDate->endOfDay())
                ->groupBy(DB::raw('DATE(tanggal_transaksi)'))
                ->get();

            // Organize data periode sebelumnya
            $prevData = [];
            foreach ($queryPrev as $row) {
                $prevData[$row->tanggal] = $row->total_nominal;
            }

            // Build array untuk periode sebelumnya (mapped ke tanggal yang sama dengan periode saat ini)
            $prevNominalArray = [];
            $prevDates = collect();
            for ($i = $daysBack; $i >= 0; $i--) {
                if ($start_date && $end_date) {
                    $prevDate = Carbon::parse($start_date)->subDays($numberOfDays)->addDays($daysBack - $i)->format('Y-m-d');
                } else {
                    $prevDate = Carbon::now()->subDays($numberOfDays + $i)->format('Y-m-d');
                }
                $prevDates->push($prevDate);
                $prevNominalArray[] = $prevData[$prevDate] ?? 0;
            }

            // Format label untuk periode
            $currentPeriodLabel = sprintf(
                'Periode Saat Ini (%s - %s)',
                Carbon::parse($dates->first())->format('d M'),
                Carbon::parse($dates->last())->format('d M')
            );

            $prevPeriodLabel = sprintf(
                'Periode Sebelumnya (%s - %s)',
                Carbon::parse($prevDates->first())->format('d M'),
                Carbon::parse($prevDates->last())->format('d M')
            );

            // Update dataset pertama dengan label yang jelas
            if (count($datasets) > 0) {
                $datasets[0]['nama_pasar'] = $currentPeriodLabel;
            }

            // Tambahkan dataset periode sebelumnya
            $datasets[] = [
                'nama_pasar' => $prevPeriodLabel,
                'data' => $prevNominalArray,
                'isPreviousPeriod' => true,
                'previousDates' => $prevDates->toArray() // Tanggal untuk periode sebelumnya
            ];
        }
        // Untuk superadmin yang tidak memilih pasar (melihat total semua pasar)
        elseif (empty($nama_pasar)) {
            // Hitung jumlah hari dalam periode yang dipilih
            $numberOfDays = $daysBack + 1;

            // Hitung tanggal periode sebelumnya
            if ($start_date && $end_date) {
                $prevStartDate = Carbon::parse($start_date)->subDays($numberOfDays);
                $prevEndDate = Carbon::parse($start_date)->subDay();
            } else {
                $prevStartDate = Carbon::now()->subDays(29 + $numberOfDays);
                $prevEndDate = Carbon::now()->subDays(30);
            }

            // Query data periode sebelumnya (total semua pasar)
            $queryPrev = DB::table('transaksi')
                ->select(
                    DB::raw('DATE(tanggal_transaksi) as tanggal'),
                    DB::raw('SUM(nominal_transaksi) as total_nominal')
                )
                ->whereNotNull('nama_pasar')
                ->where('nama_pasar', '!=', '')
                ->where('tanggal_transaksi', '>=', $prevStartDate->startOfDay())
                ->where('tanggal_transaksi', '<=', $prevEndDate->endOfDay())
                ->groupBy(DB::raw('DATE(tanggal_transaksi)'))
                ->get();

            // Organize data periode sebelumnya
            $prevData = [];
            foreach ($queryPrev as $row) {
                $prevData[$row->tanggal] = $row->total_nominal;
            }

            // Build array untuk periode sebelumnya
            $prevNominalArray = [];
            $prevDates = collect();
            for ($i = $daysBack; $i >= 0; $i--) {
                if ($start_date && $end_date) {
                    $prevDate = Carbon::parse($start_date)->subDays($numberOfDays)->addDays($daysBack - $i)->format('Y-m-d');
                } else {
                    $prevDate = Carbon::now()->subDays($numberOfDays + $i)->format('Y-m-d');
                }
                $prevDates->push($prevDate);
                $prevNominalArray[] = $prevData[$prevDate] ?? 0;
            }

            // Hitung total untuk periode saat ini (gabungkan semua pasar)
            $currentTotalArray = [];
            foreach ($dates as $date) {
                $totalForDate = 0;
                foreach ($pasarData as $namaPasar => $data) {
                    $totalForDate += $data[$date] ?? 0;
                }
                $currentTotalArray[] = $totalForDate;
            }

            // Format label untuk periode
            $currentPeriodLabel = sprintf(
                'Total Semua Pasar (%s - %s)',
                Carbon::parse($dates->first())->format('d M'),
                Carbon::parse($dates->last())->format('d M')
            );

            $prevPeriodLabel = sprintf(
                'Periode Sebelumnya (%s - %s)',
                Carbon::parse($prevDates->first())->format('d M'),
                Carbon::parse($prevDates->last())->format('d M')
            );

            // Replace datasets dengan total
            $datasets = [
                [
                    'nama_pasar' => $currentPeriodLabel,
                    'data' => $currentTotalArray
                ],
                [
                    'nama_pasar' => $prevPeriodLabel,
                    'data' => $prevNominalArray,
                    'isPreviousPeriod' => true,
                    'previousDates' => $prevDates->toArray() // Tanggal untuk periode sebelumnya
                ]
            ];
        }

        $result = [
            'tanggal' => $dates->toArray(),
            'datasets' => $datasets,
        ];

        $array = ['status' => 1, 'data' => $result, 'pages' => 0];
        return response()->json($array, 200);
    }

    public function getFilterOptions(Request $request)
    {
        $nama_pasar = $request->nama_pasar;

        // Ambil nama pasar dari tabel pasar
        $pasarListQuery = DB::table('pasar')
            ->select('nama');

        if (!empty($nama_pasar)) {
            $pasarListQuery->where('nama', $nama_pasar);
        }
        $pasarListQuery = $pasarListQuery
            ->whereNotNull('nama')
            ->where('nama', '!=', '')
            ->orderBy('nama', 'asc');
        $pasarList = $pasarListQuery->pluck('nama')->toArray();

        // Get distinct petugas (officers) - only from the same pasar
        $petugasList = DB::table('transaksi')
            ->select('id_petugas', 'nama_petugas')
            ->distinct()
            ->whereNotNull('id_petugas')
            ->whereNotNull('nama_petugas')
            ->where('nama_petugas', '!=', '');

        if (!empty($nama_pasar)) {
            $petugasList->where('nama_pasar', $nama_pasar);
        }

        $petugasList = $petugasList->orderBy('nama_petugas', 'asc')
            ->get()
            ->toArray();

        // Get distinct district (nama_distrik)
        $distrikList = DB::table('transaksi')
            ->select('nama_distrik')
            ->distinct()
            ->whereNotNull('nama_distrik')
            ->where('nama_distrik', '!=', '');

        if (!empty($nama_pasar)) {
            $distrikList->where('nama_pasar', $nama_pasar);
        }

        $distrikList = $distrikList->orderBy('nama_distrik', 'asc')
            ->pluck('nama_distrik')
            ->toArray();

        return response()->json([
            'status' => 1,
            'data' => [
                'pasar' => $pasarList,
                'petugas' => $petugasList,
                'distrik' => $distrikList
            ]
        ], 200);
    }

    public function payment_methods_chart(Request $request)
    {
        $nama_pasar = $request->nama_pasar;
        $role = $request->role;
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $query = DB::table('transaksi')
            ->select('metode_pembayaran', DB::raw('COUNT(*) as total'))
            ->whereNotNull('metode_pembayaran')
            ->where('metode_pembayaran', '!=', '');

        if ($start_date && $end_date) {
            $query->where('tanggal_transaksi', '>=', Carbon::parse($start_date)->startOfDay())
                ->where('tanggal_transaksi', '<=', Carbon::parse($end_date)->endOfDay());
        } else {
            $query->where('tanggal_transaksi', '>=', Carbon::now()->subDays(30)->startOfDay());
        }

        if ($role !== 'superadmin' && !empty($nama_pasar)) {
            $query->where('nama_pasar', $nama_pasar);
        }

        $data = $query->groupBy('metode_pembayaran')->get();

        return response()->json([
            'status' => 1,
            'data' => $data
        ], 200);
    }

    public function markets_chart(Request $request)
    {
        $role = $request->role;
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        // Chart ini hanya untuk superadmin
        if ($role !== 'superadmin') {
            return response()->json([
                'status' => 0,
                'message' => 'Access denied'
            ], 403);
        }

        $query = DB::table('transaksi')
            ->select('nama_pasar', DB::raw('SUM(nominal_transaksi) as total_nominal'), DB::raw('COUNT(*) as total_transaksi'))
            ->whereNotNull('nama_pasar')
            ->where('nama_pasar', '!=', '');

        if ($start_date && $end_date) {
            $query->where('tanggal_transaksi', '>=', Carbon::parse($start_date)->startOfDay())
                ->where('tanggal_transaksi', '<=', Carbon::parse($end_date)->endOfDay());
        } else {
            $query->where('tanggal_transaksi', '>=', Carbon::now()->subDays(30)->startOfDay());
        }

        $data = $query->groupBy('nama_pasar')->get();

        return response()->json([
            'status' => 1,
            'data' => $data
        ], 200);
    }

    public function tagihan_status_chart(Request $request)
    {
        $nama_pasar = $request->nama_pasar;
        $role = $request->role;
        $tanggal = $request->tanggal ?? date('Y-m-d');

        $query = DB::table('tagihan')
            ->select(DB::raw('status, COUNT(*) as total'))
            ->where('tanggal_tagihan', $tanggal);

        if ($role !== 'superadmin' && !empty($nama_pasar)) {
            $query->where('merchant_id', $nama_pasar);
        }

        $result = $query->groupBy('status')->get();

        $sudahDibayar = 0;
        $belumDibayar = 0;

        foreach ($result as $row) {
            if ($row->status == 'sudah_dibayar' || $row->status == 1) {
                $sudahDibayar = $row->total;
            } else if ($row->status == 'belum_dibayar' || $row->status == 0) {
                $belumDibayar = $row->total;
            }
        }

        return response()->json([
            'status' => 1,
            'data' => [
                'sudah_dibayar' => $sudahDibayar,
                'belum_dibayar' => $belumDibayar
            ]
        ], 200);
    }

    public function getPasarList(Request $request)
    {
        // Get distinct pasar untuk dropdown
        $pasarList = DB::table('pasar')
            ->select('id', 'nama')
            ->orderBy('nama', 'asc')
            ->get();

        return response()->json([
            'status' => 1,
            'data' => $pasarList
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

    public function updatePassword(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'id' => 'required|integer',
                'current_password' => 'required',
                'new_password' => 'required|min:6',
            ]);

            $id = $request->id;
            $currentPassword = $request->current_password;
            $newPassword = $request->new_password;

            // Ambil data admin
            $admin = DB::table('admin')
                ->where('id', $id)
                ->first();

            if (!$admin) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Admin tidak ditemukan'
                ], 404);
            }

            // Cek password saat ini (backward compatible)
            $passwordValid = false;

            // Cek jika password di database sudah hash (bcrypt)
            if (password_verify($currentPassword, $admin->password)) {
                $passwordValid = true;
            }
            // Jika tidak cocok dengan hash, cek plain text (backward compatibility)
            elseif ($admin->password === $currentPassword) {
                $passwordValid = true;
            }

            if (!$passwordValid) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Password saat ini tidak sesuai'
                ], 400);
            }

            // Hash password baru menggunakan bcrypt
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

            // Update password di database
            DB::table('admin')
                ->where('id', $id)
                ->update([
                    'password' => $hashedPassword
                ]);

            Log::info('Password berhasil diupdate', [
                'admin_id' => $id,
                'username' => $admin->username,
                'timestamp' => now()
            ]);

            return response()->json([
                'status' => 1,
                'message' => 'Password berhasil diupdate'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Gagal update password: ' . $e->getMessage(), [
                'admin_id' => $request->id ?? null,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 0,
                'message' => 'Gagal update password: ' . $e->getMessage()
            ], 500);
        }
    }

}
