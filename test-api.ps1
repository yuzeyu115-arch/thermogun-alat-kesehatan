$body = @{email='admin@thermogun.com';password='password'} | ConvertTo-Json
$headers = @{'Accept'='application/json'}

Write-Host "=== 1. TEST LOGIN ==="
try {
    $login = Invoke-RestMethod -Uri 'http://127.0.0.1:8000/api/auth/login' -Method POST -ContentType 'application/json' -Headers $headers -Body $body
    $token = $login.token
    Write-Host "LOGIN OK! Token: $($token.Substring(0, 20))..."
    Write-Host "User: $($login.user.name) ($($login.user.role))"
} catch {
    Write-Host "LOGIN FAILED: $($_.Exception.Message)"
    exit 1
}

$authHeaders = @{'Accept'='application/json'; 'Authorization'="Bearer $token"}

Write-Host ""
Write-Host "=== 2. TEST GET /api/auth/me ==="
try {
    $me = Invoke-RestMethod -Uri 'http://127.0.0.1:8000/api/auth/me' -Method GET -Headers $authHeaders
    Write-Host "ME OK! Name: $($me.name), Role: $($me.role)"
} catch {
    Write-Host "ME FAILED: $($_.Exception.Message)"
}

Write-Host ""
Write-Host "=== 3. TEST POST /api/scans ==="
$scanBody = @{temperature=36.5; name_manual='Test Patient'} | ConvertTo-Json
try {
    $scan = Invoke-RestMethod -Uri 'http://127.0.0.1:8000/api/scans' -Method POST -ContentType 'application/json' -Headers $authHeaders -Body $scanBody
    $scanId = $scan.data.id
    Write-Host "SCAN CREATE OK! ID: $scanId, Status: $($scan.data.status), Temp: $($scan.data.temperature)"
} catch {
    Write-Host "SCAN CREATE FAILED: $($_.Exception.Message)"
}

Write-Host ""
Write-Host "=== 4. TEST GET /api/scans ==="
try {
    $scans = Invoke-RestMethod -Uri 'http://127.0.0.1:8000/api/scans' -Method GET -Headers $authHeaders
    Write-Host "SCANS LIST OK! Count: $($scans.data.Count)"
} catch {
    Write-Host "SCANS LIST FAILED: $($_.Exception.Message)"
}

Write-Host ""
Write-Host "=== 5. TEST GET /api/scans/$scanId ==="
try {
    $scanDetail = Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/scans/$scanId" -Method GET -Headers $authHeaders
    Write-Host "SCAN DETAIL OK! Temp: $($scanDetail.data.temperature), Rec: $($scanDetail.data.recommendations)"
} catch {
    Write-Host "SCAN DETAIL FAILED: $($_.Exception.Message)"
}

Write-Host ""
Write-Host "=== 6. TEST GET /api/users (Admin Only) ==="
try {
    $users = Invoke-RestMethod -Uri 'http://127.0.0.1:8000/api/users' -Method GET -Headers $authHeaders
    Write-Host "USERS LIST OK! Count: $($users.data.Count)"
} catch {
    Write-Host "USERS LIST FAILED: $($_.Exception.Message)"
}

Write-Host ""
Write-Host "=== 7. TEST POST /api/users (Create User) ==="
$newUser = @{name='User Baru';email='baru@test.com';password='password123';role='pengguna'} | ConvertTo-Json
try {
    $created = Invoke-RestMethod -Uri 'http://127.0.0.1:8000/api/users' -Method POST -ContentType 'application/json' -Headers $authHeaders -Body $newUser
    $newUserId = $created.data.id
    Write-Host "USER CREATE OK! ID: $newUserId, Name: $($created.data.name)"
} catch {
    Write-Host "USER CREATE FAILED: $($_.Exception.Message)"
}

Write-Host ""
Write-Host "=== 8. TEST DELETE /api/scans/$scanId ==="
try {
    $del = Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/scans/$scanId" -Method DELETE -Headers $authHeaders
    Write-Host "SCAN DELETE OK! $($del.message)"
} catch {
    Write-Host "SCAN DELETE FAILED: $($_.Exception.Message)"
}

Write-Host ""
Write-Host "=== 9. TEST DELETE /api/users/$newUserId ==="
try {
    $delUser = Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/users/$newUserId" -Method DELETE -Headers $authHeaders
    Write-Host "USER DELETE OK! $($delUser.message)"
} catch {
    Write-Host "USER DELETE FAILED: $($_.Exception.Message)"
}

Write-Host ""
Write-Host "=== 10. TEST LOGOUT ==="
try {
    $logout = Invoke-RestMethod -Uri 'http://127.0.0.1:8000/api/auth/logout' -Method POST -Headers $authHeaders
    Write-Host "LOGOUT OK! $($logout.message)"
} catch {
    Write-Host "LOGOUT FAILED: $($_.Exception.Message)"
}

Write-Host ""
Write-Host "==================="
Write-Host "ALL API TESTS DONE!"
Write-Host "==================="
