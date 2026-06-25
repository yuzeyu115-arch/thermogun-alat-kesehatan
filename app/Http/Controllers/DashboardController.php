<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Scan;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();
        $role = $authUser->role;
        if ($role === 'admin') {
            return $this->adminDashboard();
        } elseif ($role === 'petugas') {
            return $this->petugasDashboard();
        } else {
            return $this->penggunaDashboard();
        }
    }

    private function adminDashboard()
    {
        $usersCount   = User::count();
        $scansCount   = Scan::count();
        $todayCount   = Scan::whereDate('created_at', today())->count();
        $demamCount   = Scan::whereIn('status', ['DEMAM', 'KRITIS'])->whereDate('created_at', today())->count();
        $recentScans  = Scan::with('user')->orderBy('created_at', 'desc')->take(10)->get();
        return view('dashboards.admin', compact('usersCount', 'scansCount', 'todayCount', 'demamCount', 'recentScans'));
    }

    private function petugasDashboard()
    {
        $todayScans  = Scan::with('user')->whereDate('created_at', today())->orderBy('created_at', 'desc')->get();
        $users       = User::where('role', 'pengguna')->get();
        return view('dashboards.petugas', compact('todayScans', 'users'));
    }

    private function penggunaDashboard()
    {
        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $myScans = $user->scans()->orderBy('created_at', 'desc')->get();
        return view('dashboards.pengguna', compact('user', 'myScans'));
    }

    public function profile()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return view('profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6|confirmed']);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        return redirect()->route('profile')->with('success', 'Profil berhasil diperbarui.');
    }

    public function reports()
    {
        $scans = Scan::with('user')->orderBy('created_at', 'desc')->get();
        $normal  = $scans->where('status', 'NORMAL')->count();
        $demam   = $scans->where('status', 'DEMAM')->count();
        $kritis  = $scans->where('status', 'KRITIS')->count();
        $dingin  = $scans->where('status', 'DINGIN')->count();
        return view('admin.reports', compact('scans', 'normal', 'demam', 'kritis', 'dingin'));
    }

    public function export()
    {
        $scans = Scan::with('user')->orderBy('created_at', 'desc')->get();

        $csvContent = "No,Nama,Suhu (°C),Status,Saran,Tanggal & Waktu\n";
        foreach ($scans as $i => $scan) {
            $name = $scan->user ? $scan->user->name : ($scan->name_manual ?? 'Anonim');
            $csvContent .= ($i + 1) . ',' . $name . ',' . $scan->temperature . ',' . $scan->status . ',"' . $scan->recommendations . '",' . $scan->created_at . "\n";
        }

        return response($csvContent, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="laporan-suhu-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    public function settings()
    {
        return view('admin.settings');
    }
}
