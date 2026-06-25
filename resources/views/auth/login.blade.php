<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | SmartThermo</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'Inter',sans-serif;background:#0a0e1a;min-height:100vh;display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden;}
        body::before{content:'';position:absolute;width:600px;height:600px;border-radius:50%;background:radial-gradient(circle,rgba(0,212,255,0.08) 0%,transparent 70%);top:-100px;left:-100px;pointer-events:none;}
        body::after{content:'';position:absolute;width:500px;height:500px;border-radius:50%;background:radial-gradient(circle,rgba(124,58,237,0.08) 0%,transparent 70%);bottom:-100px;right:-100px;pointer-events:none;}
        .login-container{
            width:100%;max-width:420px;padding:20px;
        }
        .login-card{
            background:rgba(17,24,39,0.9);backdrop-filter:blur(20px);
            border:1px solid rgba(255,255,255,0.07);border-radius:24px;padding:40px;
            box-shadow:0 24px 64px rgba(0,0,0,0.5);
        }
        .brand{text-align:center;margin-bottom:32px;}
        .brand-icon{
            width:64px;height:64px;border-radius:18px;margin:0 auto 16px;
            background:linear-gradient(135deg,#00d4ff,#7c3aed);
            display:flex;align-items:center;justify-content:center;font-size:28px;
            box-shadow:0 8px 24px rgba(0,212,255,0.3);
        }
        .brand h1{font-size:24px;font-weight:800;background:linear-gradient(135deg,#00d4ff,#7c3aed);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;}
        .brand p{font-size:13px;color:#64748b;margin-top:6px;}
        .form-group{margin-bottom:18px;}
        label{font-size:12px;font-weight:600;letter-spacing:0.5px;color:#64748b;display:block;margin-bottom:7px;text-transform:uppercase;}
        input{
            width:100%;padding:12px 16px;background:rgba(255,255,255,0.04);
            border:1px solid rgba(255,255,255,0.08);border-radius:12px;
            color:#e2e8f0;font-size:14px;font-family:'Inter',sans-serif;
            transition:border-color 0.2s,box-shadow 0.2s;outline:none;
        }
        input:focus{border-color:#00d4ff;box-shadow:0 0 0 3px rgba(0,212,255,0.1);}
        .btn-login{
            width:100%;padding:14px;background:linear-gradient(135deg,#00b8d9,#7c3aed);
            color:#fff;border:none;border-radius:12px;font-size:15px;font-weight:700;
            cursor:pointer;transition:all 0.2s;margin-top:8px;font-family:'Inter',sans-serif;
            letter-spacing:0.3px;
        }
        .btn-login:hover{opacity:0.9;transform:translateY(-1px);box-shadow:0 8px 24px rgba(0,212,255,0.25);}
        .btn-login:active{transform:translateY(0);}
        .error-msg{background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.2);color:#ef4444;padding:12px 16px;border-radius:10px;font-size:13px;margin-bottom:16px;}
        .demo-accounts{
            margin-top:24px;padding-top:20px;border-top:1px solid rgba(255,255,255,0.06);
        }
        .demo-title{font-size:11px;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:#64748b;text-align:center;margin-bottom:12px;}
        .demo-list{display:flex;flex-direction:column;gap:8px;}
        .demo-item{
            background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);
            border-radius:8px;padding:10px 14px;display:flex;justify-content:space-between;align-items:center;
        }
        .demo-role{font-size:12px;font-weight:600;text-transform:capitalize;}
        .demo-cred{font-size:11px;color:#64748b;font-family:monospace;}
        .role-admin{color:#a78bfa;}
        .role-petugas{color:#00d4ff;}
        .role-pengguna{color:#10b981;}
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="brand">
                <div class="brand-icon">🌡️</div>
                <h1>SmartThermo</h1>
                <p>Sistem Monitoring Suhu Tubuh</p>
            </div>

            @if($errors->any())
                <div class="error-msg">⚠️ {{ $errors->first('email') }}</div>
            @endif

            <form method="POST" action="/login">
                @csrf
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="email@contoh.com" value="{{ old('email') }}" required autofocus>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn-login">🚀 Masuk ke Sistem</button>
            </form>

            <div class="demo-accounts">
                <div class="demo-title">Akun Demo</div>
                <div class="demo-list">
                    <div class="demo-item">
                        <span class="demo-role role-admin">👑 Admin</span>
                        <span class="demo-cred">admin@thermogun.com / password</span>
                    </div>
                    <div class="demo-item">
                        <span class="demo-role role-petugas">🔧 Petugas</span>
                        <span class="demo-cred">petugas@thermogun.com / password</span>
                    </div>
                    <div class="demo-item">
                        <span class="demo-role role-pengguna">👤 Pengguna</span>
                        <span class="demo-cred">pengguna@thermogun.com / password</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
