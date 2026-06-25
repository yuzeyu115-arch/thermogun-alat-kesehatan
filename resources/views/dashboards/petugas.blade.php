@extends('layouts.app')
@section('title','Dashboard Petugas')
@section('page-title','🔧 Dashboard Petugas')

@push('styles')
<style>
.scan-live-card{
    background:var(--bg-card2);border:2px solid rgba(0,212,255,0.2);border-radius:20px;padding:28px;
    margin-bottom:24px;text-align:center;position:relative;overflow:hidden;
}
.scan-live-card::before{
    content:'';position:absolute;inset:0;
    background:radial-gradient(ellipse at 50% 100%,rgba(0,212,255,0.05),transparent 70%);
}
.temp-display{font-size:72px;font-weight:800;line-height:1;margin:20px 0;}
.temp-display.normal{color:#10b981;}
.temp-display.demam{color:#f59e0b;}
.temp-display.kritis{color:#ef4444;animation:pulse 0.8s infinite;}
.temp-display.dingin{color:#00d4ff;}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:0.5}}
.status-big{font-size:22px;font-weight:700;margin-bottom:8px;}
.recommendation{background:rgba(255,255,255,0.04);border-radius:10px;padding:12px 20px;font-size:13px;color:var(--muted);max-width:400px;margin:0 auto 20px;}
.waiting{font-size:18px;color:var(--muted);animation:blink 1.5s infinite;}
@keyframes blink{0%,100%{opacity:1}50%{opacity:0.3}}
</style>
@endpush

@section('content')
<div class="scan-live-card">
    <h2 style="font-size:15px;font-weight:600;color:var(--muted);margin-bottom:16px;">🌡️ Live Scan ESP32</h2>
    <div id="tempDisplay" class="temp-display" style="color:var(--muted)">-- °C</div>
    <div id="statusBadge" style="margin:12px 0;"></div>
    <div id="recBox" class="recommendation" style="display:none;"></div>
    <div id="waitMsg" class="waiting">Menunggu data dari sensor...</div>
    <div id="scanActions" style="display:none;gap:12px;justify-content:center;margin-top:16px;">
        <button id="btnSave" class="btn btn-primary" onclick="saveScan()">💾 Simpan Data</button>
        <a id="btnPrint" href="#" target="_blank" class="btn btn-success">🖨️ Print Hasil</a>
    </div>
</div>

<!-- INPUT MANUAL -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">
<div class="card">
    <h3 style="font-size:15px;font-weight:700;margin-bottom:20px;">✏️ Input Data Manual</h3>
    <form method="POST" action="{{ route('petugas.scan.manual') }}">
        @csrf
        <div class="form-group">
            <label>Nama Pasien / Pengunjung</label>
            <input type="text" name="name_manual" placeholder="Masukkan nama..." required>
        </div>
        <div class="form-group">
            <label>Suhu Tubuh (°C)</label>
            <input type="number" name="temperature" step="0.1" min="20" max="50" placeholder="Contoh: 36.5" required>
        </div>
        <div class="form-group">
            <label>Pilih Pengguna (opsional)</label>
            <select name="user_id">
                <option value="">-- Tamu/Anonim --</option>
                @foreach($users as $u)
                <option value="{{ $u->id }}">{{ $u->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%">💾 Simpan Manual</button>
    </form>
</div>

<!-- RINGKASAN HARI INI -->
<div class="card">
    <h3 style="font-size:15px;font-weight:700;margin-bottom:16px;">📅 Ringkasan Hari Ini</h3>
    @php
        $normal = $todayScans->where('status','NORMAL')->count();
        $demam = $todayScans->where('status','DEMAM')->count();
        $kritis = $todayScans->where('status','KRITIS')->count();
    @endphp
    <div style="display:flex;flex-direction:column;gap:12px;">
        <div style="display:flex;justify-content:space-between;padding:12px 16px;background:rgba(16,185,129,0.08);border-radius:10px;border:1px solid rgba(16,185,129,0.15);">
            <span style="color:#10b981;font-weight:600;">✅ Normal</span>
            <strong style="color:#fff">{{ $normal }} orang</strong>
        </div>
        <div style="display:flex;justify-content:space-between;padding:12px 16px;background:rgba(245,158,11,0.08);border-radius:10px;border:1px solid rgba(245,158,11,0.15);">
            <span style="color:#f59e0b;font-weight:600;">⚠️ Demam</span>
            <strong style="color:#fff">{{ $demam }} orang</strong>
        </div>
        <div style="display:flex;justify-content:space-between;padding:12px 16px;background:rgba(239,68,68,0.08);border-radius:10px;border:1px solid rgba(239,68,68,0.15);">
            <span style="color:#ef4444;font-weight:600;">🚨 Kritis</span>
            <strong style="color:#fff">{{ $kritis }} orang</strong>
        </div>
        <div style="display:flex;justify-content:space-between;padding:12px 16px;background:rgba(255,255,255,0.04);border-radius:10px;border:1px solid var(--border);">
            <span style="color:var(--muted);font-weight:600;">📊 Total</span>
            <strong style="color:#fff">{{ $todayScans->count() }} orang</strong>
        </div>
    </div>
</div>
</div>

<!-- DAFTAR KEHADIRAN HARI INI -->
<div class="card">
    <h3 style="font-size:15px;font-weight:700;margin-bottom:20px;">📋 Daftar Scan Suhu Hari Ini</h3>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Suhu</th>
                    <th>Status</th>
                    <th>Saran</th>
                    <th>Jam</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($todayScans as $i => $scan)
                <tr>
                    <td style="color:var(--muted)">{{ $i+1 }}</td>
                    <td><strong>{{ $scan->user ? $scan->user->name : ($scan->name_manual ?? 'Anonim') }}</strong></td>
                    <td><strong style="color:var(--accent)">{{ $scan->temperature }}°C</strong></td>
                    <td><span class="badge badge-{{ strtolower($scan->status) }}">{{ $scan->status }}</span></td>
                    <td style="color:var(--muted);font-size:12px;">{{ Str::limit($scan->recommendations, 50) }}</td>
                    <td style="color:var(--muted);font-size:12px;">{{ $scan->created_at->format('H:i') }}</td>
                    <td><a href="{{ route('scan.print', $scan->id) }}" target="_blank" class="btn btn-success btn-sm">🖨️</a></td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:30px;">Belum ada scan hari ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>
<script>
let lastSuhu = null;
let lastScanId = null;
const socket = io('http://localhost:3000');

function diagnose(t) {
    if (t < 30) return {status:'DINGIN', cls:'dingin', rec:'Suhu tidak normal atau jarak terlalu jauh. Coba ulangi.'};
    if (t < 37.5) return {status:'NORMAL', cls:'normal', rec:'Kondisi tubuh Anda sehat. Tetap jaga kebersihan.'};
    if (t < 38.5) return {status:'DEMAM', cls:'demam', rec:'Terindikasi demam ringan. Istirahat dan banyak minum air.'};
    return {status:'KRITIS', cls:'kritis', rec:'Suhu sangat tinggi! Segera ke fasilitas kesehatan.'};
}

socket.on('dataSuhu', function(suhu) {
    lastSuhu = suhu;
    const d = diagnose(suhu);
    document.getElementById('waitMsg').style.display = 'none';
    const el = document.getElementById('tempDisplay');
    el.className = 'temp-display ' + d.cls;
    el.textContent = suhu.toFixed(1) + ' °C';
    document.getElementById('statusBadge').innerHTML = '<span style="font-size:20px;font-weight:700;color:var(--text)">' + d.status + '</span>';
    const rb = document.getElementById('recBox');
    rb.textContent = '💡 ' + d.rec;
    rb.style.display = 'block';
    document.getElementById('scanActions').style.display = 'flex';
});

function saveScan() {
    if (!lastSuhu) return;
    fetch('/scan/save', {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content},
        body: JSON.stringify({temperature: lastSuhu})
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            lastScanId = data.scan.id;
            document.getElementById('btnPrint').href = '/scan/' + lastScanId + '/print';
            alert('✅ Data berhasil disimpan!');
            location.reload();
        }
    });
}
</script>
@endpush
@endsection
