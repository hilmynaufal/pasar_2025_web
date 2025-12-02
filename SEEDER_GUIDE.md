# Panduan Menjalankan Seeder Superadmin

## Prasyarat

Pastikan kolom `role` sudah ditambahkan ke tabel `admin` dengan menjalankan migration:

```bash
php artisan migrate
```

## Cara Menjalankan Seeder

### Opsi 1: Jalankan Semua Seeder

```bash
php artisan db:seed
```

### Opsi 2: Jalankan Hanya SuperAdminSeeder

```bash
php artisan db:seed --class=SuperAdminSeeder
```

## Kredensial Superadmin Default

Setelah seeder berhasil dijalankan, Anda dapat login dengan:

- **Username**: `superadmin`
- **Password**: `superadmin123`
- **Role**: `superadmin`
- **Nama**: `Super Admin`
- **Nama Pasar**: `All`

⚠️ **PENTING**: Segera ubah password setelah login pertama kali!

## Verifikasi

Setelah menjalankan seeder, Anda dapat memverifikasi dengan:

### 1. Cek Database

```sql
SELECT * FROM admin WHERE role = 'superadmin';
```

### 2. Login ke Aplikasi

1. Buka aplikasi di browser
2. Login dengan kredensial di atas
3. Verifikasi bahwa Anda melihat:
   - Filter pasar di dashboard
   - Chart "Perbandingan Antar Pasar"
   - Tombol Edit/Delete di halaman Pedagang

## Troubleshooting

### Error: "SQLSTATE[42S22]: Column not found: 1054 Unknown column 'role'"

**Solusi**: Jalankan migration terlebih dahulu

```bash
php artisan migrate
```

### Superadmin sudah ada

Jika Anda menjalankan seeder lagi dan superadmin sudah ada, seeder akan skip dan menampilkan warning:

```
Superadmin already exists. Skipping...
```

Ini normal dan mencegah duplikasi data.

### Ingin Membuat Superadmin Baru dengan Username Berbeda

Edit file `database/seeders/SuperAdminSeeder.php` dan ubah data sesuai kebutuhan:

```php
DB::table('admin')->insert([
    'nama' => 'Admin Baru',
    'nama_pasar' => 'All',
    'id_pasar' => 0,
    'username' => 'admin_baru', // Ganti username
    'password' => 'password123', // Ganti password
    'role' => 'superadmin',
]);
```

Kemudian jalankan seeder lagi.

## Menghapus Superadmin (Jika Diperlukan)

Jika Anda ingin menghapus superadmin dan membuat ulang:

```sql
DELETE FROM admin WHERE username = 'superadmin';
```

Kemudian jalankan seeder lagi.

## Keamanan

⚠️ **Catatan Penting tentang Keamanan**:

1. Password disimpan dalam **plain text** (tidak di-hash) sesuai dengan arsitektur aplikasi
2. Pastikan database Anda aman dan tidak dapat diakses publik
3. Segera ubah password default setelah deployment
4. Jangan gunakan password yang sama untuk multiple environment (dev, staging, production)

## Lihat Juga

- `SUPERADMIN_SETUP.md` - Dokumentasi lengkap fitur superadmin
- `CLAUDE.md` - Dokumentasi arsitektur aplikasi
