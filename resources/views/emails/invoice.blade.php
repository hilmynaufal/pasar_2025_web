<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice Pembayaran - {{ $data['nama_pedagang'] }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        .invoice-details {
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #f1f3f4;
        }
        .detail-label {
            font-weight: bold;
            color: #495057;
        }
        .detail-value {
            color: #212529;
        }
        .total-section {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            margin-top: 20px;
        }
        .total-amount {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            font-size: 14px;
            color: #6c757d;
        }
        .status-success {
            color: #28a745;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Invoice Pembayaran</h1>
        <p>SiPintarOnline</p>
    </div>

    <div class="invoice-details">
        <h2>Detail Transaksi</h2>
        
        <div class="detail-row">
            <span class="detail-label">ID Transaksi:</span>
            <span class="detail-value">{{ $data['id'] }}</span>
        </div>
        
        <div class="detail-row">
            <span class="detail-label">Tanggal Transaksi:</span>
            <span class="detail-value">{{ \Carbon\Carbon::parse($data['tanggal_transaksi'])->format('d/m/Y H:i:s') }}</span>
        </div>
        
        <div class="detail-row">
            <span class="detail-label">Nama Pedagang:</span>
            <span class="detail-value">{{ $data['nama_pedagang'] }}</span>
        </div>
        
        <div class="detail-row">
            <span class="detail-label">Kode Kios:</span>
            <span class="detail-value">{{ $data['kode_kios'] }}</span>
        </div>
        
        <div class="detail-row">
            <span class="detail-label">Nama Pasar:</span>
            <span class="detail-value">{{ $data['nama_pasar'] }}</span>
        </div>
        
        <div class="detail-row">
            <span class="detail-label">Nama Distrik:</span>
            <span class="detail-value">{{ $data['nama_distrik'] }}</span>
        </div>
        
        <div class="detail-row">
            <span class="detail-label">Jenis Akun:</span>
            <span class="detail-value">{{ $data['jenis_akun'] }}</span>
        </div>
        
        <div class="detail-row">
            <span class="detail-label">Metode Pembayaran:</span>
            <span class="detail-value">{{ $data['metode_pembayaran'] }}</span>
        </div>
        
        <div class="detail-row">
            <span class="detail-label">Nama Petugas:</span>
            <span class="detail-value">{{ $data['nama_petugas'] }}</span>
        </div>
        
        <div class="detail-row">
            <span class="detail-label">Status:</span>
            <span class="detail-value status-success">{{ $data['status'] }}</span>
        </div>
        
        <div class="total-section">
            <div class="total-amount">
                Rp {{ number_format($data['nominal_transaksi'], 0, ',', '.') }}
            </div>
            <p>Total Pembayaran</p>
        </div>
    </div>

    <div class="footer">
        <p>Terima kasih telah melakukan pembayaran.</p>
        <p>Invoice ini dikirim secara otomatis oleh SiPintarOnline.</p>
        <p>Jika ada pertanyaan, silakan hubungi petugas pasar.</p>
    </div>
</body>
</html>