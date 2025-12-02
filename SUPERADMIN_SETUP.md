# Setup Superadmin - Pasar 2025

## Cara Membuat Akun Superadmin

Setelah migration berhasil dijalankan, kolom `role` sudah ditambahkan ke tabel `admin`. Berikut cara membuat superadmin:

### 1. Melalui Database

Jalankan query SQL berikut di database Anda:

```sql
-- Update admin yang sudah ada menjadi superadmin
UPDATE admin SET role = 'superadmin' WHERE username = 'admin_username';

-- Atau insert admin baru sebagai superadmin
INSERT INTO admin (nama, nama_pasar, id_pasar, username, password, role)
VALUES ('Super Admin', 'All', '0', 'superadmin', 'password123', 'superadmin');
```

### 2. Role yang Tersedia

- `admin` (default) - Admin pasar biasa dengan akses terbatas ke pasar mereka sendiri
- `superadmin` - Superadmin dengan akses ke semua pasar

## Fitur Superadmin

### 1. Dashboard

Superadmin memiliki akses ke:
- **Filter Pasar**: Dapat memilih pasar spesifik atau melihat data semua pasar
- **Date Range Filter**: Dapat memilih periode tanggal untuk melihat statistik
- **Chart Perbandingan Antar Pasar**: Melihat performa semua pasar dalam satu chart

### 2. Halaman Pedagang

Superadmin dapat:
- **Melihat semua pedagang** dari semua pasar
- **Mengedit pedagang** dari pasar manapun
- **Menghapus pedagang** dari pasar manapun

Admin biasa **TIDAK** dapat mengedit atau menghapus pedagang.

### 3. Halaman Transaksi

Superadmin dapat:
- Melihat transaksi dari semua pasar
- Filter berdasarkan pasar tertentu

## Dashboard Charts

Dashboard sekarang menampilkan 5 jenis diagram:

1. **Grafik Pendapatan Harian** - Line chart menampilkan pendapatan per hari
2. **Statistik Petugas** - Doughnut chart menampilkan performa petugas
3. **Metode Pembayaran** - Pie chart menampilkan distribusi metode pembayaran
4. **Status Tagihan** - Doughnut chart menampilkan tagihan sudah/belum dibayar
5. **Perbandingan Antar Pasar** (Superadmin Only) - Bar chart membandingkan pendapatan antar pasar

## Testing

### Login sebagai Superadmin
1. Login dengan username dan password yang sudah dibuat
2. Verifikasi bahwa Anda dapat melihat filter pasar di dashboard
3. Coba filter berdasarkan pasar tertentu atau pilih "Semua Pasar"
4. Coba ubah date range filter
5. Pastikan chart perbandingan antar pasar muncul

### Login sebagai Admin Biasa
1. Login dengan akun admin biasa
2. Verifikasi bahwa filter pasar **TIDAK** muncul
3. Verifikasi bahwa data hanya menampilkan pasar sesuai dengan `nama_pasar` admin tersebut
4. Coba edit/delete pedagang, pastikan muncul error "Anda tidak memiliki akses"

## Troubleshooting

### Role tidak tersimpan saat login
- Periksa apakah kolom `role` sudah ada di tabel `admin`
- Jalankan migration: `php artisan migrate`

### Superadmin tidak bisa melihat semua pasar
- Clear cache: `php artisan cache:clear`
- Clear session: `php artisan session:flush` (atau logout dan login kembali)

### Chart tidak muncul
- Periksa console browser untuk error JavaScript
- Pastikan Chart.js sudah ter-load dengan benar
- Periksa API endpoint apakah return data dengan benar
