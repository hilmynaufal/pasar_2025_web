# Panduan Pengiriman Invoice Email

## Fitur yang Ditambahkan

Sistem sekarang akan mengirimkan invoice otomatis ke email pedagang setelah transaksi pembayaran berhasil.

## File yang Dibuat/Dimodifikasi

### 1. Template Email
- `resources/views/emails/invoice.blade.php` - Template HTML untuk invoice

### 2. Mailable Class
- `app/Mail/InvoiceMail.php` - Class untuk mengirim email invoice

### 3. API Controller
- `app/Http/Controllers/Api/ApiController.php` - Ditambahkan logika pengiriman email di fungsi `bayar()`

### 4. Test
- `tests/Feature/EmailInvoiceTest.php` - Unit test untuk fitur email

## Cara Kerja

1. **Transaksi Berhasil**: Ketika fungsi `bayar()` berhasil menyimpan transaksi
2. **Cari Email Pedagang**: Sistem mencari email pedagang berdasarkan `kode_kios`
3. **Kirim Email**: Jika email ditemukan, invoice dikirim otomatis
4. **Error Handling**: Jika pengiriman email gagal, transaksi tetap berhasil (error di-log)

## Konfigurasi Email

### Environment Variables (.env)
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

### Database Schema
Pastikan tabel `pedagang` memiliki kolom `email`:
```sql
ALTER TABLE pedagang ADD COLUMN email VARCHAR(255) NULL;
```

## Testing

### Manual Testing
1. Pastikan konfigurasi email sudah benar
2. Buat transaksi pembayaran melalui API `/api/bayar`
3. Periksa email pedagang untuk invoice

### Unit Testing
```bash
php artisan test tests/Feature/EmailInvoiceTest.php
```

## Template Email

Template invoice mencakup:
- ID Transaksi
- Tanggal dan Waktu
- Detail Pedagang (nama, kode kios, pasar, distrik)
- Metode Pembayaran
- Nama Petugas
- Status Transaksi
- Total Pembayaran (dengan format Rupiah)

## Error Handling

- Jika email pedagang tidak ditemukan: Transaksi tetap berhasil, tidak ada email dikirim
- Jika pengiriman email gagal: Transaksi tetap berhasil, error di-log di `storage/logs/laravel.log`

## Logging

Error pengiriman email akan tercatat di:
```
storage/logs/laravel.log
```

Dengan format:
```
[timestamp] local.ERROR: Gagal mengirim email invoice: [error message]
```

## Troubleshooting

### Email Tidak Terkirim
1. Periksa konfigurasi SMTP di `.env`
2. Pastikan kredensial email benar
3. Cek log error di `storage/logs/laravel.log`
4. Pastikan kolom `email` ada di tabel `pedagang`

### Testing Email
Untuk development, gunakan:
- **Mailtrap**: Email testing service
- **Log Driver**: Set `MAIL_MAILER=log` untuk menyimpan email di log
- **Mailpit**: Local SMTP server

## Contoh Response API

### Berhasil dengan Email
```json
{
    "status": 1,
    "message": "Pembayaran berhasil",
    "data": [
        {
            "id": "RTB-20250115123456789",
            "nominal_transaksi": 50000,
            "tanggal_transaksi": "2025-01-15 12:34:56",
            "metode_pembayaran": "Tunai",
            "nama_pedagang": "John Doe",
            "kode_kios": "K001",
            "jenis_akun": "Pedagang",
            "nama_pasar": "Pasar Test",
            "nama_petugas": "Petugas A",
            "nama_distrik": "Distrik A",
            "status": "SUCCESS"
        }
    ]
}
```

### Berhasil tanpa Email (pedagang tidak punya email)
Response sama, tapi tidak ada email yang dikirim.
