@extends('layouts.app')
@section('title','Pengaturan Sistem')
@section('page-title','⚙️ Pengaturan Sistem')

@section('content')
<div class="card">
    <h3 style="font-size:15px;font-weight:700;margin-bottom:20px;">⚙️ Konfigurasi Sistem</h3>
    <div style="display:grid;gap:16px;">
        <div style="padding:20px;background:var(--bg-card2);border-radius:12px;border:1px solid var(--border);">
            <h4 style="font-size:14px;font-weight:600;margin-bottom:8px;">🌡️ Batas Suhu Tubuh</h4>
            <p style="font-size:13px;color:var(--muted);">Nilai batas ini digunakan untuk mengklasifikasi status suhu secara otomatis oleh sensor.</p>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-top:16px;">
                <div style="background:rgba(0,212,255,0.06);border-radius:10px;padding:14px;border:1px solid rgba(0,212,255,0.1);">
                    <p style="font-size:11px;color:var(--muted);">Batas Bawah (Dingin)</p>
                    <strong style="color:#00d4ff;font-size:18px;">< 30°C</strong>
                </div>
                <div style="background:rgba(245,158,11,0.06);border-radius:10px;padding:14px;border:1px solid rgba(245,158,11,0.1);">
                    <p style="font-size:11px;color:var(--muted);">Batas Demam</p>
                    <strong style="color:#f59e0b;font-size:18px;">≥ 37.5°C</strong>
                </div>
                <div style="background:rgba(239,68,68,0.06);border-radius:10px;padding:14px;border:1px solid rgba(239,68,68,0.1);">
                    <p style="font-size:11px;color:var(--muted);">Batas Kritis</p>
                    <strong style="color:#ef4444;font-size:18px;">≥ 38.5°C</strong>
                </div>
            </div>
        </div>
        <div style="padding:20px;background:var(--bg-card2);border-radius:12px;border:1px solid var(--border);">
            <h4 style="font-size:14px;font-weight:600;margin-bottom:8px;">🔌 Konfigurasi Perangkat</h4>
            <p style="font-size:13px;color:var(--muted);">Node.js backend berjalan di <code style="background:rgba(255,255,255,0.06);padding:2px 8px;border-radius:4px;">http://localhost:3000</code> dan secara otomatis mendeteksi ESP32 via USB.</p>
        </div>
        <div style="padding:20px;background:var(--bg-card2);border-radius:12px;border:1px solid var(--border);">
            <h4 style="font-size:14px;font-weight:600;margin-bottom:8px;">📋 API Endpoints</h4>
            <div style="display:flex;flex-direction:column;gap:8px;font-size:13px;font-family:monospace;">
                <div>POST <span style="color:#00d4ff;">/api/auth/login</span></div>
                <div>GET  <span style="color:#10b981;">/api/scans</span></div>
                <div>POST <span style="color:#10b981;">/api/scans</span></div>
                <div>GET  <span style="color:#10b981;">/api/users</span> <span style="color:var(--muted);font-size:11px;">(admin)</span></div>
            </div>
        </div>
    </div>
</div>
@endsection
