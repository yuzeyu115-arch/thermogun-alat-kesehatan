@extends('layouts.app')
@section('title','Dashboard Pengguna')
@section('page-title','🏠 Dashboard Saya')

@section('content')
<div style="display:grid;grid-template-columns:300px 1fr;gap:24px;align-items:start;">

<!-- PROFIL CARD -->
<div class="card" style="text-align:center;">
    <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#00d4ff,#7c3aed);display:flex;align-items:center;justify-content:center;font-size:36px;font-weight:700;color:#fff;margin:0 auto 16px;">
        {{ strtoupper(substr($user->name, 0, 1)) }}
    </div>
    <h2 style="font-size:18px;font-weight:700;margin-bottom:4px;">{{ $user->name }}</h2>
    <p style="font-size:13px;color:var(--muted);margin-bottom:16px;">{{ $user->email }}</p>
    <span class="badge badge-{{ $user->role }}">{{ ucfirst($user->role) }}</span>

    <div style="margin-top:24px;display:flex;flex-direction:column;gap:10px;">
        @php
            $myNormal = $myScans->where('status','NORMAL')->count();
            $myDemam  = $myScans->whereIn('status',['DEMAM','KRITIS'])->count();
        @endphp
        <div style="display:flex;justify-content:space-between;padding:10px 14px;background:rgba(16,185,129,0.08);border-radius:10px;">
            <span style="color:var(--muted);font-size:13px;">✅ Normal</span>
            <strong style="color:#10b981">{{ $myNormal }}x</strong>
        </div>
        <div style="display:flex;justify-content:space-between;padding:10px 14px;background:rgba(245,158,11,0.08);border-radius:10px;">
            <span style="color:var(--muted);font-size:13px;">⚠️ Demam/Kritis</span>
            <strong style="color:#f59e0b">{{ $myDemam }}x</strong>
        </div>
        <div style="display:flex;justify-content:space-between;padding:10px 14px;background:rgba(255,255,255,0.04);border-radius:10px;">
            <span style="color:var(--muted);font-size:13px;">📊 Total Scan</span>
            <strong style="color:#fff">{{ $myScans->count() }}x</strong>
        </div>
    </div>

    <a href="{{ route('profile') }}" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:20px;">✏️ Edit Profil</a>
</div>

<!-- RIWAYAT SCAN -->
<div class="card">
    <h3 style="font-size:15px;font-weight:700;margin-bottom:20px;">📋 Riwayat Scan Suhu Saya</h3>
    @if($myScans->isEmpty())
        <div style="text-align:center;color:var(--muted);padding:40px;">
            <div style="font-size:48px;margin-bottom:12px;">🌡️</div>
            <p>Belum ada riwayat scan suhu.</p>
            <p style="font-size:12px;margin-top:6px;">Scan suhu Anda akan muncul di sini.</p>
        </div>
    @else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Suhu</th>
                    <th>Status</th>
                    <th>Saran Kesehatan</th>
                    <th>Tanggal & Waktu</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($myScans as $i => $scan)
                <tr>
                    <td style="color:var(--muted)">{{ $i+1 }}</td>
                    <td><strong style="color:var(--accent);font-size:16px;">{{ $scan->temperature }}°C</strong></td>
                    <td><span class="badge badge-{{ strtolower($scan->status) }}">{{ $scan->status }}</span></td>
                    <td style="color:var(--muted);font-size:12px;">{{ $scan->recommendations }}</td>
                    <td style="color:var(--muted);font-size:12px;">{{ $scan->created_at->format('d M Y, H:i') }}</td>
                    <td><a href="{{ route('scan.print', $scan->id) }}" target="_blank" class="btn btn-success btn-sm">🖨️ Print</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

</div>
@endsection
