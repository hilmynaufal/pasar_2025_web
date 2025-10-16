# Konfigurasi Email untuk Invoice

## Pengaturan Environment Variables

Tambahkan konfigurasi berikut ke file `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@pasar2025.com"
MAIL_FROM_NAME="Sistem Pasar 2025"
```

## Konfigurasi untuk Gmail

1. Aktifkan 2-Factor Authentication di akun Gmail
2. Buat App Password khusus untuk aplikasi
3. Gunakan App Password sebagai MAIL_PASSWORD

## Konfigurasi untuk Mailtrap (Testing)

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="test@pasar2025.com"
MAIL_FROM_NAME="Sistem Pasar 2025"
```

## Testing Email

Untuk testing, Anda bisa menggunakan:

1. **Mailtrap** - untuk development
2. **Log Driver** - email akan disimpan di storage/logs/laravel.log
3. **Mailpit** - local SMTP server

## Struktur Database

Pastikan tabel `pedagang` memiliki kolom `email`:

```sql
ALTER TABLE pedagang ADD COLUMN email VARCHAR(255) NULL;
```

## Cara Kerja

1. Saat transaksi berhasil di fungsi `bayar()`
2. Sistem akan mencari email pedagang berdasarkan `kode_kios`
3. Jika email ditemukan, invoice akan dikirim otomatis
4. Jika pengiriman email gagal, transaksi tetap berhasil (error di-log)
