@extends('layouts.app')
@section('title','Profil Saya')
@section('page-title','👤 Profil Saya')

@section('content')
<div style="max-width:560px;">
<div class="card">
    <div style="text-align:center;margin-bottom:28px;">
        <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#00d4ff,#7c3aed);display:flex;align-items:center;justify-content:center;font-size:36px;font-weight:700;color:#fff;margin:0 auto 12px;">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <h2 style="font-size:18px;font-weight:700;">{{ $user->name }}</h2>
        <span class="badge badge-{{ $user->role }}" style="margin-top:8px;">{{ ucfirst($user->role) }}</span>
    </div>

    <form method="POST" action="{{ route('profile.update') }}">
        @csrf @method('PUT')
        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
        </div>
        <hr style="border-color:var(--border);margin:20px 0;">
        <p style="font-size:12px;color:var(--muted);margin-bottom:16px;">Kosongkan password jika tidak ingin menggantinya.</p>
        <div class="form-group">
            <label>Password Baru</label>
            <input type="password" name="password" placeholder="Minimal 6 karakter">
        </div>
        <div class="form-group">
            <label>Konfirmasi Password</label>
            <input type="password" name="password_confirmation" placeholder="Ulangi password baru">
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">💾 Simpan Perubahan</button>
    </form>
</div>
</div>
@endsection
