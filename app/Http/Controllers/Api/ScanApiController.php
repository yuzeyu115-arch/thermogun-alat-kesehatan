<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Scan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScanApiController extends Controller
{
    private function diagnose(float $temp): array
    {
        if ($temp < 30) {
            return ['status' => 'DINGIN', 'recommendations' => 'Suhu tidak normal atau jarak pengukuran terlalu jauh. Coba ulangi pengukuran.'];
        } elseif ($temp < 37.5) {
            return ['status' => 'NORMAL', 'recommendations' => 'Kondisi tubuh Anda sehat. Tetap jaga kebersihan dan kesehatan.'];
        } elseif ($temp < 38.5) {
            return ['status' => 'DEMAM', 'recommendations' => 'Anda terindikasi demam ringan. Istirahat yang cukup dan banyak minum air putih.'];
        } else {
            return ['status' => 'KRITIS', 'recommendations' => 'Suhu tubuh sangat tinggi! Segera periksakan diri ke fasilitas kesehatan terdekat.'];
        }
    }

    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Pengguna biasa hanya lihat miliknya
        if ($user->role === 'pengguna') {
            $scans = Scan::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        } else {
            $scans = Scan::with('user')->orderBy('created_at', 'desc')->get();
        }

        return response()->json(['data' => $scans]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'temperature' => 'required|numeric|min:20|max:50',
            'name_manual' => 'nullable|string|max:255',
            'user_id'     => 'nullable|exists:users,id',
        ]);

        $diagnosis = $this->diagnose((float) $data['temperature']);
        $scan = Scan::create(array_merge($data, $diagnosis));

        return response()->json([
            'message' => 'Data scan berhasil disimpan.',
            'data'    => $scan,
        ], 201);
    }

    public function show($id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $scan = Scan::with('user')->findOrFail($id);

        if ($user->role === 'pengguna' && $scan->user_id !== $user->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        return response()->json(['data' => $scan]);
    }

    public function destroy($id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->role === 'pengguna') {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $scan = Scan::findOrFail($id);
        $scan->delete();

        return response()->json(['message' => 'Data scan berhasil dihapus.']);
    }
}
