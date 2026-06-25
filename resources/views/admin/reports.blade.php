@extends('layouts.app')
@section('title','Laporan Data')
@section('page-title','📈 Laporan & Ekspor Data')

@section('content')
<div class="stat-grid" style="margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(16,185,129,0.1);">✅</div>
        <div class="stat-info"><h3>{{ $normal }}</h3><p>Normal</p></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(245,158,11,0.1);">⚠️</div>
        <div class="stat-info"><h3>{{ $demam }}</h3><p>Demam</p></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(239,68,68,0.1);">🚨</div>
        <div class="stat-info"><h3>{{ $kritis }}</h3><p>Kritis</p></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(0,212,255,0.1);">❄️</div>
        <div class="stat-info"><h3>{{ $dingin }}</h3><p>Dingin</p></div>
    </div>
</div>

<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <h3 style="font-size:15px;font-weight:700;">📋 Semua Data Scan</h3>
        <a href="{{ route('admin.export') }}" class="btn btn-primary">⬇️ Export CSV</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>#</th><th>Nama</th><th>Suhu</th><th>Status</th><th>Saran</th><th>Tanggal</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($scans as $i => $scan)
                <tr>
                    <td style="color:var(--muted)">{{ $i+1 }}</td>
                    <td><strong>{{ $scan->user ? $scan->user->name : ($scan->name_manual ?? 'Anonim') }}</strong></td>
                    <td><strong style="color:var(--accent)">{{ $scan->temperature }}°C</strong></td>
                    <td><span class="badge badge-{{ strtolower($scan->status) }}">{{ $scan->status }}</span></td>
                    <td style="color:var(--muted);font-size:12px;max-width:200px;">{{ Str::limit($scan->recommendations, 60) }}</td>
                    <td style="color:var(--muted);font-size:12px;">{{ $scan->created_at->format('d M Y H:i') }}</td>
                    <td><a href="{{ route('scan.print', $scan->id) }}" target="_blank" class="btn btn-success btn-sm">🖨️</a></td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;padding:30px;color:var(--muted)">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
