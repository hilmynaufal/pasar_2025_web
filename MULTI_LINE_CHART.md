# Multi-Line Revenue Chart - Grafik Pendapatan Per Pasar

## Overview

Grafik pendapatan di dashboard telah dimodifikasi untuk menampilkan **multiple lines**, dimana setiap line merepresentasikan satu pasar. Ini memungkinkan perbandingan visual langsung antar pasar dalam satu grafik.

## Fitur

### 1. Multiple Lines dengan Warna Berbeda

Setiap pasar ditampilkan sebagai line terpisah dengan warna unik:

| Urutan | Warna      | Kode Warna (Border) | Contoh Pasar     |
|--------|------------|---------------------|------------------|
| 1      | Merah      | `#FF6384`          | Pasar Margahayu  |
| 2      | Biru       | `#36A2EB`          | Pasar Cicalengka |
| 3      | Kuning     | `#FFCE56`          | Pasar Gedebage   |
| 4      | Hijau/Cyan | `#4BC0C0`          | Pasar Caringin   |
| 5      | Ungu       | `#9966FF`          | Pasar Cihapit    |
| 6      | Orange     | `#FF9F40`          | Pasar Sederhana  |
| 7+     | *repeat*   | *cycle colors*     | Pasar lainnya    |

### 2. Behavior Berdasarkan Role

#### **Superadmin**

**Tanpa Filter Pasar (Semua Pasar):**
- Menampilkan **semua pasar** dalam satu grafik
- Setiap pasar memiliki line dengan warna berbeda
- Legend menampilkan nama semua pasar
- Ideal untuk melihat perbandingan performa antar pasar

**Dengan Filter Pasar Spesifik:**
- Menampilkan **hanya pasar yang dipilih**
- Berguna untuk fokus analisis satu pasar tertentu

#### **Admin Biasa**

- Menampilkan **hanya pasar mereka sendiri**
- Single line dengan warna merah (default pertama)
- Tidak ada dropdown filter pasar

### 3. Interactive Features

✅ **Legend (Keterangan)**
- Klik nama pasar di legend untuk hide/show line tersebut
- Posisi: Top (atas chart)

✅ **Tooltip**
- Hover di atas chart untuk melihat detail
- Format: `Pasar Name: Rp 1.000.000`
- Mode: Index (menampilkan semua pasar untuk tanggal yang sama)

✅ **Format Rupiah**
- Y-axis: `Rp 1.000.000`
- Tooltip: `Rp 1.000.000`
- Menggunakan format Indonesia (titik sebagai separator)

✅ **Smooth Lines**
- Tension: 0.4 (line melengkung smooth)
- Lebih enak dipandang dibanding garis lurus

✅ **Fill Area**
- Area di bawah line diberi warna transparan
- Memudahkan identifikasi visual antar pasar

## Struktur Data API

### Request

```http
POST /revenue_chart
Content-Type: application/json

{
  "nama_pasar": "Pasar Margahayu",  // Optional, kosong = semua pasar
  "role": "superadmin",               // "admin" atau "superadmin"
  "start_date": "2025-01-01",
  "end_date": "2025-01-31"
}
```

### Response (Multiple Pasar)

```json
{
  "status": 1,
  "data": {
    "tanggal": ["2025-01-01", "2025-01-02", "2025-01-03"],
    "datasets": [
      {
        "nama_pasar": "Pasar Margahayu",
        "data": [100000, 150000, 200000]
      },
      {
        "nama_pasar": "Pasar Cicalengka",
        "data": [80000, 120000, 180000]
      },
      {
        "nama_pasar": "Pasar Gedebage",
        "data": [90000, 130000, 190000]
      }
    ]
  },
  "pages": 0
}
```

### Response (Single Pasar)

```json
{
  "status": 1,
  "data": {
    "tanggal": ["2025-01-01", "2025-01-02", "2025-01-03"],
    "datasets": [
      {
        "nama_pasar": "Pasar Margahayu",
        "data": [100000, 150000, 200000]
      }
    ]
  },
  "pages": 0
}
```

## Implementasi

### Backend (API)

**File**: `app/Http/Controllers/Api/ApiController.php`

**Method**: `revenue_chart(Request $request)`

**Key Changes:**
1. Query menggunakan `GROUP BY nama_pasar, DATE(tanggal_transaksi)`
2. Data diorganisir per pasar dalam array `datasets`
3. Setiap dataset berisi `nama_pasar` dan `data` (array nominal per tanggal)

```php
$query = DB::table('transaksi')
    ->select(
        DB::raw('DATE(tanggal_transaksi) as tanggal'),
        'nama_pasar',
        DB::raw('SUM(nominal_transaksi) as total_nominal')
    )
    ->groupBy(DB::raw('DATE(tanggal_transaksi)'), 'nama_pasar')
    ->get();
```

### Frontend (Chart.js)

**File**: `resources/views/livewire/home.blade.php`

**Key Features:**

1. **Color Palette**: Array warna predefined untuk konsistensi
2. **Dynamic Datasets**: Loop through API response untuk build datasets
3. **Responsive Legend**: Auto-generated dari dataset labels
4. **Custom Tooltip**: Format rupiah untuk better UX

```javascript
var pasarColors = [
  { border: '#FF6384', background: 'rgba(255, 99, 132, 0.2)' },  // Merah
  { border: '#36A2EB', background: 'rgba(54, 162, 235, 0.2)' },  // Biru
  // ... more colors
];

data.datasets.forEach(function(dataset, index) {
  var colorIndex = index % pasarColors.length;
  chartDatasets.push({
    label: dataset.nama_pasar,
    data: dataset.data,
    borderColor: pasarColors[colorIndex].border,
    backgroundColor: pasarColors[colorIndex].background,
    borderWidth: 2,
    fill: true,
    tension: 0.4
  });
});
```

## Use Cases

### 1. Superadmin: Analisis Performa Semua Pasar

**Skenario**: Superadmin ingin melihat pasar mana yang perform terbaik bulan ini

**Langkah**:
1. Login sebagai superadmin
2. Pilih date range (misalnya 1 Januari - 31 Januari)
3. Biarkan filter pasar kosong atau pilih "Semua Pasar"
4. Lihat grafik menampilkan semua pasar dalam satu chart

**Insight**:
- Pasar dengan line tertinggi = revenue terbesar
- Tren naik/turun per pasar
- Perbandingan konsistensi pendapatan

### 2. Superadmin: Deep Dive Satu Pasar

**Skenario**: Superadmin ingin fokus analisis Pasar Margahayu saja

**Langkah**:
1. Pilih "Pasar Margahayu" di dropdown filter pasar
2. Klik "Terapkan Filter"
3. Chart hanya menampilkan Pasar Margahayu

**Insight**:
- Detail pergerakan revenue harian
- Identifikasi hari dengan revenue tertinggi/terendah
- Lebih fokus tanpa distraksi pasar lain

### 3. Admin: Monitoring Pasar Sendiri

**Skenario**: Admin Pasar Cicalengka ingin monitor pendapatan pasar mereka

**Langkah**:
1. Login sebagai admin Pasar Cicalengka
2. Dashboard otomatis menampilkan data Pasar Cicalengka saja

**Insight**:
- Revenue harian pasar mereka
- Tren mingguan/bulanan
- Data spesifik untuk pasar yang mereka kelola

## Tips & Best Practices

### 1. Membaca Chart dengan Banyak Pasar

- ✅ **Gunakan Legend**: Klik legend untuk hide/show pasar tertentu
- ✅ **Zoom Date Range**: Perkecil date range untuk detail lebih jelas
- ✅ **Compare Top Performers**: Hide pasar dengan revenue rendah untuk fokus ke top performers

### 2. Identifikasi Trend

- 📈 **Line Naik Konsisten**: Pasar bertumbuh sehat
- 📉 **Line Turun**: Perlu investigasi penyebab
- 📊 **Line Flat**: Revenue stabil, bisa jadi saturasi atau musim sepi

### 3. Action Items dari Chart

| Pattern                  | Possible Action                        |
|--------------------------|----------------------------------------|
| Gap besar antar pasar    | Share best practice dari top performer |
| Drop tiba-tiba           | Investigasi: masalah operasional?      |
| Revenue tinggi hari tertentu | Analisa: ada event khusus?         |
| Semua pasar turun        | Faktor eksternal (ekonomi, musim)      |

## Troubleshooting

### Chart Tidak Menampilkan Multiple Lines

**Kemungkinan Penyebab**:
1. Filter pasar dipilih (hanya 1 pasar muncul) - **Expected behavior**
2. Data transaksi hanya ada untuk 1 pasar - **Check database**
3. Kolom `nama_pasar` kosong/null - **Data quality issue**

**Solusi**:
```sql
-- Check data pasar di transaksi
SELECT DISTINCT nama_pasar, COUNT(*) as total
FROM transaksi
WHERE tanggal_transaksi >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY nama_pasar;
```

### Warna Tidak Konsisten

**Penyebab**: Urutan pasar berubah karena sorting/query order

**Solusi**:
- Backend sudah order by nama_pasar
- Jika perlu warna spesifik per pasar, modifikasi frontend:

```javascript
// Map warna spesifik per nama pasar
var specificColors = {
  'Pasar Margahayu': { border: '#FF6384', background: 'rgba(255, 99, 132, 0.2)' },
  'Pasar Cicalengka': { border: '#36A2EB', background: 'rgba(54, 162, 235, 0.2)' }
};
```

### Legend Terlalu Panjang

**Penyebab**: Terlalu banyak pasar (>8)

**Solusi**:
- Filter by date range untuk reduce noise
- Pilih pasar spesifik
- Modifikasi legend position atau font size

## Future Enhancements

Potential improvements:

1. **Custom Color Mapping**: Allow admin to set custom colors per pasar
2. **Export Chart**: Download chart as PNG/PDF
3. **Compare Periods**: Year-over-year comparison (2024 vs 2025)
4. **Annotations**: Mark special events on chart (holidays, promotions)
5. **Zoom/Pan**: Interactive zoom untuk explore details

## Related Documentation

- `SUPERADMIN_SETUP.md` - Dokumentasi fitur superadmin
- `CLAUDE.md` - Arsitektur aplikasi
- Chart.js Documentation: https://www.chartjs.org/docs/latest/charts/line.html
