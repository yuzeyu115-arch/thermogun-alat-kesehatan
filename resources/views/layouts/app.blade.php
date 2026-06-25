<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SmartThermo') | Sistem Monitoring Suhu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-dark: #0a0e1a;
            --bg-card: #111827;
            --bg-card2: #1a2235;
            --border: rgba(255,255,255,0.07);
            --accent: #00d4ff;
            --accent2: #7c3aed;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --text: #e2e8f0;
            --muted: #64748b;
            --sidebar-w: 260px;
        }
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'Inter',sans-serif;background:var(--bg-dark);color:var(--text);min-height:100vh;}
        a{color:inherit;text-decoration:none;}
        .layout{display:flex;min-height:100vh;}

        /* SIDEBAR */
        .sidebar{
            width:var(--sidebar-w);min-height:100vh;
            background:linear-gradient(180deg,#0f1729 0%,#0a0e1a 100%);
            border-right:1px solid var(--border);
            display:flex;flex-direction:column;position:fixed;top:0;left:0;height:100vh;z-index:100;
            transition:transform 0.3s ease;
        }
        .sidebar-brand{
            padding:24px 20px;border-bottom:1px solid var(--border);
            display:flex;align-items:center;gap:12px;
        }
        .sidebar-brand .icon{
            width:42px;height:42px;border-radius:12px;
            background:linear-gradient(135deg,var(--accent),var(--accent2));
            display:flex;align-items:center;justify-content:center;font-size:20px;
        }
        .sidebar-brand h2{font-size:15px;font-weight:700;color:#fff;}
        .sidebar-brand p{font-size:11px;color:var(--muted);}
        .sidebar-nav{flex:1;padding:16px 12px;overflow-y:auto;}
        .nav-label{font-size:10px;font-weight:600;letter-spacing:1.5px;color:var(--muted);text-transform:uppercase;padding:8px 8px 4px;}
        .nav-item{
            display:flex;align-items:center;gap:12px;padding:11px 12px;border-radius:10px;
            font-size:14px;font-weight:500;color:var(--muted);transition:all 0.2s;margin-bottom:2px;cursor:pointer;
        }
        .nav-item:hover,.nav-item.active{background:rgba(0,212,255,0.08);color:var(--accent);}
        .nav-item .icon{width:18px;text-align:center;}
        .sidebar-footer{padding:16px 12px;border-top:1px solid var(--border);}
        .user-card{
            display:flex;align-items:center;gap:10px;padding:10px;
            border-radius:10px;background:var(--bg-card2);
        }
        .user-avatar{
            width:36px;height:36px;border-radius:50%;
            background:linear-gradient(135deg,var(--accent),var(--accent2));
            display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;color:#fff;
        }
        .user-info p{font-size:13px;font-weight:600;color:#fff;}
        .user-info span{font-size:11px;color:var(--muted);text-transform:capitalize;}

        /* MAIN */
        .main{margin-left:var(--sidebar-w);flex:1;display:flex;flex-direction:column;}
        .topbar{
            height:64px;background:rgba(10,14,26,0.9);backdrop-filter:blur(12px);
            border-bottom:1px solid var(--border);position:sticky;top:0;z-index:90;
            display:flex;align-items:center;justify-content:space-between;padding:0 28px;
        }
        .topbar-title{font-size:16px;font-weight:600;}
        .topbar-right{display:flex;align-items:center;gap:12px;}
        .btn-logout{
            display:flex;align-items:center;gap:8px;padding:8px 16px;border-radius:8px;
            background:rgba(239,68,68,0.1);color:#ef4444;border:1px solid rgba(239,68,68,0.2);
            font-size:13px;font-weight:500;cursor:pointer;transition:all 0.2s;
        }
        .btn-logout:hover{background:rgba(239,68,68,0.2);}
        .content{padding:28px;flex:1;}

        /* CARDS */
        .card{
            background:var(--bg-card);border:1px solid var(--border);
            border-radius:16px;padding:24px;
        }
        .stat-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px;margin-bottom:24px;}
        .stat-card{
            background:var(--bg-card);border:1px solid var(--border);
            border-radius:16px;padding:20px;display:flex;align-items:center;gap:16px;
            transition:transform 0.2s,border-color 0.2s;
        }
        .stat-card:hover{transform:translateY(-2px);border-color:var(--accent);}
        .stat-icon{
            width:52px;height:52px;border-radius:14px;
            display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;
        }
        .stat-info h3{font-size:26px;font-weight:800;color:#fff;}
        .stat-info p{font-size:12px;color:var(--muted);margin-top:2px;}

        /* TABLE */
        .table-wrap{overflow-x:auto;border-radius:16px;border:1px solid var(--border);}
        table{width:100%;border-collapse:collapse;}
        thead th{
            padding:14px 16px;text-align:left;font-size:11px;font-weight:600;
            letter-spacing:1px;text-transform:uppercase;color:var(--muted);
            background:var(--bg-card2);border-bottom:1px solid var(--border);
        }
        tbody tr{border-bottom:1px solid var(--border);transition:background 0.15s;}
        tbody tr:last-child{border-bottom:none;}
        tbody tr:hover{background:rgba(255,255,255,0.02);}
        tbody td{padding:14px 16px;font-size:14px;}

        /* BADGE */
        .badge{
            display:inline-flex;align-items:center;padding:4px 12px;
            border-radius:20px;font-size:11px;font-weight:600;letter-spacing:0.5px;
        }
        .badge-normal{background:rgba(16,185,129,0.12);color:#10b981;border:1px solid rgba(16,185,129,0.2);}
        .badge-demam{background:rgba(245,158,11,0.12);color:#f59e0b;border:1px solid rgba(245,158,11,0.2);}
        .badge-kritis{background:rgba(239,68,68,0.12);color:#ef4444;border:1px solid rgba(239,68,68,0.2);}
        .badge-dingin{background:rgba(0,212,255,0.12);color:#00d4ff;border:1px solid rgba(0,212,255,0.2);}
        .badge-admin{background:rgba(124,58,237,0.15);color:#a78bfa;border:1px solid rgba(124,58,237,0.2);}
        .badge-petugas{background:rgba(0,212,255,0.12);color:#00d4ff;border:1px solid rgba(0,212,255,0.2);}
        .badge-pengguna{background:rgba(16,185,129,0.12);color:#10b981;border:1px solid rgba(16,185,129,0.2);}

        /* FORMS */
        .form-group{margin-bottom:16px;}
        label{font-size:13px;font-weight:500;color:var(--muted);display:block;margin-bottom:6px;}
        input[type=text],input[type=email],input[type=password],input[type=number],select,textarea{
            width:100%;padding:11px 14px;background:var(--bg-card2);
            border:1px solid var(--border);border-radius:10px;
            color:var(--text);font-size:14px;font-family:'Inter',sans-serif;
            transition:border-color 0.2s;outline:none;
        }
        input:focus,select:focus,textarea:focus{border-color:var(--accent);}

        /* BUTTONS */
        .btn{display:inline-flex;align-items:center;gap:8px;padding:10px 20px;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;border:none;transition:all 0.2s;text-decoration:none;}
        .btn-primary{background:linear-gradient(135deg,#00b8d9,var(--accent2));color:#fff;}
        .btn-primary:hover{opacity:0.85;transform:translateY(-1px);}
        .btn-success{background:rgba(16,185,129,0.15);color:#10b981;border:1px solid rgba(16,185,129,0.3);}
        .btn-success:hover{background:rgba(16,185,129,0.25);}
        .btn-danger{background:rgba(239,68,68,0.12);color:#ef4444;border:1px solid rgba(239,68,68,0.2);}
        .btn-danger:hover{background:rgba(239,68,68,0.22);}
        .btn-sm{padding:6px 14px;font-size:12px;}

        /* ALERT */
        .alert{padding:12px 16px;border-radius:10px;font-size:13px;margin-bottom:20px;}
        .alert-success{background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.2);color:#10b981;}
        .alert-danger{background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.2);color:#ef4444;}

        /* MODAL */
        .modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,0.7);backdrop-filter:blur(4px);z-index:200;display:none;align-items:center;justify-content:center;}
        .modal-overlay.open{display:flex;}
        .modal{background:var(--bg-card);border:1px solid var(--border);border-radius:20px;padding:28px;width:100%;max-width:480px;}
        .modal-title{font-size:16px;font-weight:700;margin-bottom:20px;color:#fff;}

        /* GRADIENT TEXT */
        .grad{background:linear-gradient(135deg,var(--accent),var(--accent2));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;}

        /* RESPONSIVE */
        @media(max-width:768px){
            .sidebar{transform:translateX(-100%);}
            .main{margin-left:0;}
            .stat-grid{grid-template-columns:1fr 1fr;}
        }
    </style>
    @stack('styles')
</head>
<body>
<div class="layout">
    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="icon">🌡️</div>
            <div>
                <h2>SmartThermo</h2>
                <p>Monitoring Suhu</p>
            </div>
        </div>
        <nav class="sidebar-nav">
            @php $role = auth()->user()->role; @endphp

            @if($role === 'admin')
            <div class="nav-label">Admin</div>
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
                <span class="icon">📊</span> Dashboard
            </a>
            <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->is('admin/users') ? 'active' : '' }}">
                <span class="icon">👥</span> Manajemen Pengguna
            </a>
            <a href="{{ route('admin.reports') }}" class="nav-item {{ request()->is('admin/reports') ? 'active' : '' }}">
                <span class="icon">📈</span> Laporan & Ekspor
            </a>
            <a href="{{ route('admin.settings') }}" class="nav-item {{ request()->is('admin/settings') ? 'active' : '' }}">
                <span class="icon">⚙️</span> Pengaturan Sistem
            </a>

            @elseif($role === 'petugas')
            <div class="nav-label">Petugas</div>
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
                <span class="icon">📊</span> Dashboard
            </a>
            <a href="{{ route('dashboard') }}" class="nav-item">
                <span class="icon">✏️</span> Input Manual
            </a>

            @else
            <div class="nav-label">Pengguna</div>
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
                <span class="icon">🏠</span> Dashboard
            </a>
            <a href="{{ route('profile') }}" class="nav-item {{ request()->is('profile') ? 'active' : '' }}">
                <span class="icon">👤</span> Profil Saya
            </a>
            @endif
        </nav>
        <div class="sidebar-footer">
            <div class="user-card">
                <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div class="user-info">
                    <p>{{ auth()->user()->name }}</p>
                    <span>{{ auth()->user()->role }}</span>
                </div>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main">
        <div class="topbar">
            <span class="topbar-title">@yield('page-title', 'Dashboard')</span>
            <div class="topbar-right">
                <a href="{{ route('profile') }}" style="font-size:13px;color:var(--muted)">
                    👤 {{ auth()->user()->name }}
                </a>
                <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                    @csrf
                    <button type="submit" class="btn-logout">🚪 Keluar</button>
                </form>
            </div>
        </div>

        <div class="content">
            @if(session('success'))
                <div class="alert alert-success">✅ {{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $e) • {{ $e }}<br> @endforeach
                </div>
            @endif
            @yield('content')
        </div>
    </main>
</div>
@stack('scripts')
</body>
</html>
