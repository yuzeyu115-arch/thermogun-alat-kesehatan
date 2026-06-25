<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil Scan Suhu | SmartThermo</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'Inter',sans-serif;background:#fff;color:#1a1a2e;padding:40px;}
        .print-page{max-width:580px;margin:0 auto;border:2px solid #e2e8f0;border-radius:16px;overflow:hidden;}
        .print-header{
            padding:28px 32px;
            background:linear-gradient(135deg,#0a0e1a,#1a2235);
            color:#fff;text-align:center;
        }
        .print-header .icon{font-size:40px;margin-bottom:10px;}
        .print-header h1{font-size:22px;font-weight:800;}
        .print-header p{font-size:12px;color:rgba(255,255,255,0.5);margin-top:4px;}
        .print-body{padding:32px;}
        .result-section{
            text-align:center;padding:28px;border-radius:14px;margin-bottom:24px;
        }
        .result-normal{background:#f0fdf4;border:2px solid #86efac;}
        .result-demam{background:#fffbeb;border:2px solid #fcd34d;}
        .result-kritis{background:#fef2f2;border:2px solid #fca5a5;}
        .result-dingin{background:#eff6ff;border:2px solid #93c5fd;}
        .result-temp{font-size:60px;font-weight:800;line-height:1;}
        .temp-normal{color:#16a34a;}
        .temp-demam{color:#d97706;}
        .temp-kritis{color:#dc2626;}
        .temp-dingin{color:#2563eb;}
        .result-status{font-size:18px;font-weight:700;margin-top:10px;}
        .info-table{width:100%;border-collapse:collapse;margin-bottom:20px;}
        .info-table tr{border-bottom:1px solid #e2e8f0;}
        .info-table td{padding:12px 0;font-size:14px;}
        .info-table td:first-child{color:#64748b;width:140px;}
        .info-table td:last-child{font-weight:600;}
        .rec-box{
            background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;
            padding:16px 20px;margin-bottom:24px;
        }
        .rec-box h4{font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#64748b;margin-bottom:8px;}
        .rec-box p{font-size:14px;line-height:1.6;}
        .print-footer{text-align:center;font-size:11px;color:#94a3b8;margin-top:20px;padding-top:20px;border-top:1px solid #e2e8f0;}
        .btn-print{
            display:block;width:100%;padding:14px;background:linear-gradient(135deg,#0ea5e9,#7c3aed);
            color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer;
            margin-bottom:10px;font-family:'Inter',sans-serif;
        }
        .btn-back{
            display:block;width:100%;padding:12px;background:#f1f5f9;
            color:#1a1a2e;border:none;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;
            font-family:'Inter',sans-serif;
        }
        @media print {
            .no-print{display:none!important;}
            body{padding:0;}
            .print-page{border:none;border-radius:0;max-width:100%;}
        }
    </style>
</head>
<body>
<div class="print-page">
    <div class="print-header">
        <div class="icon">🌡️</div>
        <h1>SmartThermo</h1>
        <p>Hasil Pemeriksaan Suhu Tubuh</p>
    </div>
    <div class="print-body">
        @php $cls = strtolower($scan->status); @endphp
        <div class="result-section result-{{ $cls }}">
            <div class="result-temp temp-{{ $cls }}">{{ number_format($scan->temperature, 1) }}°C</div>
            <div class="result-status">{{ $scan->status }}</div>
        </div>

        <table class="info-table">
            <tr>
                <td>Nama</td>
                <td>{{ $scan->user ? $scan->user->name : ($scan->name_manual ?? 'Anonim') }}</td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>{{ $scan->created_at->format('d F Y') }}</td>
            </tr>
            <tr>
                <td>Waktu</td>
                <td>{{ $scan->created_at->format('H:i:s') }} WIB</td>
            </tr>
            <tr>
                <td>No. Referensi</td>
                <td>#SCAN-{{ str_pad($scan->id, 5, '0', STR_PAD_LEFT) }}</td>
            </tr>
        </table>

        <div class="rec-box">
            <h4>💡 Saran Kesehatan</h4>
            <p>{{ $scan->recommendations }}</p>
        </div>

        <div class="no-print">
            <button class="btn-print" onclick="window.print()">🖨️ Cetak / Simpan PDF</button>
            <button class="btn-back" onclick="window.close()">← Kembali</button>
        </div>

        <div class="print-footer">
            Dicetak oleh Sistem SmartThermo &bull; {{ now()->format('d M Y H:i') }} &bull; Dokumen ini hanya untuk keperluan internal.
        </div>
    </div>
</div>
</body>
</html>
