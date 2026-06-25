@extends('layouts.app')
@section('title','Manajemen Pengguna')
@section('page-title','👥 Manajemen Pengguna')

@section('content')
<div style="display:flex;justify-content:flex-end;margin-bottom:20px;">
    <button class="btn btn-primary" onclick="document.getElementById('modalAdd').classList.add('open')">
        ➕ Tambah Pengguna
    </button>
</div>

<div class="table-wrap">
    <table>
        <thead>
            <tr><th>#</th><th>Nama</th><th>Email</th><th>Role</th><th>Bergabung</th><th>Aksi</th></tr>
        </thead>
        <tbody>
            @forelse($users as $i => $user)
            <tr>
                <td style="color:var(--muted)">{{ $i+1 }}</td>
                <td>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#00d4ff,#7c3aed);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;color:#fff;flex-shrink:0;">
                            {{ strtoupper(substr($user->name,0,1)) }}
                        </div>
                        <strong>{{ $user->name }}</strong>
                    </div>
                </td>
                <td style="color:var(--muted)">{{ $user->email }}</td>
                <td><span class="badge badge-{{ $user->role }}">{{ ucfirst($user->role) }}</span></td>
                <td style="color:var(--muted);font-size:12px;">{{ $user->created_at->format('d M Y') }}</td>
                <td style="display:flex;gap:8px;">
                    <button class="btn btn-success btn-sm" onclick='openEdit({{ json_encode($user) }})'>✏️ Edit</button>
                    <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" onsubmit="return confirm('Hapus pengguna ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">🗑️</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center;padding:30px;color:var(--muted)">Belum ada pengguna.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- MODAL TAMBAH -->
<div class="modal-overlay" id="modalAdd">
    <div class="modal">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
            <h3 class="modal-title" style="margin:0;">➕ Tambah Pengguna</h3>
            <button onclick="document.getElementById('modalAdd').classList.remove('open')" style="background:none;border:none;color:var(--muted);cursor:pointer;font-size:20px;">✕</button>
        </div>
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            <div class="form-group"><label>Nama</label><input type="text" name="name" required></div>
            <div class="form-group"><label>Email</label><input type="email" name="email" required></div>
            <div class="form-group"><label>Password</label><input type="password" name="password" required></div>
            <div class="form-group">
                <label>Role</label>
                <select name="role">
                    <option value="pengguna">Pengguna</option>
                    <option value="petugas">Petugas</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%">💾 Simpan</button>
        </form>
    </div>
</div>

<!-- MODAL EDIT -->
<div class="modal-overlay" id="modalEdit">
    <div class="modal">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
            <h3 class="modal-title" style="margin:0;">✏️ Edit Pengguna</h3>
            <button onclick="document.getElementById('modalEdit').classList.remove('open')" style="background:none;border:none;color:var(--muted);cursor:pointer;font-size:20px;">✕</button>
        </div>
        <form id="editForm" method="POST" action="">
            @csrf @method('PUT')
            <div class="form-group"><label>Nama</label><input type="text" name="name" id="editName" required></div>
            <div class="form-group"><label>Email</label><input type="email" name="email" id="editEmail" required></div>
            <div class="form-group"><label>Password Baru (opsional)</label><input type="password" name="password"></div>
            <div class="form-group">
                <label>Role</label>
                <select name="role" id="editRole">
                    <option value="pengguna">Pengguna</option>
                    <option value="petugas">Petugas</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%">💾 Perbarui</button>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openEdit(user) {
    document.getElementById('editName').value = user.name;
    document.getElementById('editEmail').value = user.email;
    document.getElementById('editRole').value = user.role;
    document.getElementById('editForm').action = '/admin/users/' + user.id;
    document.getElementById('modalEdit').classList.add('open');
}
</script>
@endpush
@endsection
