# Update versi
Beberapa penambahan / perubahan fitur di update versi

## Penambahan fitur Generate QR Code

Karena di aplikasi frontend mobile nya ada fitur scan qr pedagang untuk mendapatkan tagihan, saya ingin menambahkan fitur generate qr code di menu pedagang per pedagang nya, jadi nanti qr code nya dari field id_kios, yang nanti akan tergenerate ke file, dan nanti jika sudah tergenerate, maka akan bisa di lihat
nama file qr code akan di simpan di field qr_code_file pada tabel pedagang

sebagai referensi, contoh header dan value tabel pedagang:

id	nama	nomor_identitas	jenis_identitas	alamat	kategori	blok	no_kios	jenis_dagangan	ukuran	tarif	tarif_realisasi	kode_kios	id_kios	nama_pasar	buka	email	updated_at	qr_code_file
2117	H Acep Kamil	3204252702580005	KTP	Pacinan Timur No.61 Rt.003/004 Desa Cicalengka Wetan Kec. Cicalengka	Kios	I/Lantai Basement/A	2	Sembako	3X2	4000	0	Kios - I/Lantai Basement/A - 2	CICALENGKA - KIOS - I/LANTAI BASEMENT/A - 2	Pasar Cicalengka	1	hilmyblazr@gmail.com	2025-10-21 11:24:49 