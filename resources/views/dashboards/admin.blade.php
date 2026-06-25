@extends('layouts.app')
@section('title','Dashboard Admin')
@section('page-title','📊 Dashboard Admin')

@section('content')
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(0,212,255,0.1);">👥</div>
        <div class="stat-info">
            <h3>{{ $usersCount }}</h3>
            <p>Total Pengguna</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(16,185,129,0.1);">🌡️</div>
        <div class="stat-info">
            <h3>{{ $scansCount }}</h3>
            <p>Total Scan</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(245,158,11,0.1);">📅</div>
        <div class="stat-info">
            <h3>{{ $todayCount }}</h3>
            <p>Scan Hari Ini</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(239,68,68,0.1);">🔴</div>
        <div class="stat-info">
            <h3>{{ $demamCount }}</h3>
            <p>Demam/Kritis Hari Ini</p>
        </div>
    </div>
</div>

<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <h2 style="font-size:16px;font-weight:700;">📋 Scan Terbaru</h2>
        <div style="display:flex;gap:10px;">
            <a href="{{ route('admin.reports') }}" class="btn btn-success btn-sm">📈 Laporan</a>
            <a href="{{ route('admin.export') }}" class="btn btn-primary btn-sm">⬇️ Export CSV</a>
        </div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Suhu</th>
                    <th>Status</th>
                    <th>Saran</th>
                    <th>Waktu</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentScans as $i => $scan)
                <tr>
                    <td style="color:var(--muted)">{{ $i+1 }}</td>
                    <td><strong>{{ $scan->user ? $scan->user->name : ($scan->name_manual ?? 'Anonim') }}</strong></td>
                    <td><strong style="color:var(--accent)">{{ $scan->temperature }}°C</strong></td>
                    <td>
                        <span class="badge badge-{{ strtolower($scan->status) }}">{{ $scan->status }}</span>
                    </td>
                    <td style="color:var(--muted);font-size:12px;max-width:200px;">{{ Str::limit($scan->recommendations, 60) }}</td>
                    <td style="color:var(--muted);font-size:12px;">{{ $scan->created_at->format('d M Y H:i') }}</td>
                    <td>
                        <a href="{{ route('scan.print', $scan->id) }}" target="_blank" class="btn btn-success btn-sm">🖨️ Print</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:30px;">Belum ada data scan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
