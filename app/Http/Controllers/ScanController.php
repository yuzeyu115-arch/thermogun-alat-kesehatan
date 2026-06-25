<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Scan;
use Illuminate\Support\Facades\Auth;

class ScanController extends Controller
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

    // Dipanggil dari websocket Node.js via POST
    public function store(Request $request)
    {
        $data = $request->validate([
            'temperature' => 'required|numeric|min:20|max:50',
            'name_manual' => 'nullable|string|max:255',
            'user_id'     => 'nullable|exists:users,id',
        ]);

        $diagnosis = $this->diagnose((float) $data['temperature']);
        $scan = Scan::create(array_merge($data, $diagnosis));

        return response()->json(['success' => true, 'scan' => $scan]);
    }

    // Input manual oleh petugas
    public function storeManual(Request $request)
    {
        $data = $request->validate([
            'temperature' => 'required|numeric|min:20|max:50',
            'name_manual' => 'required|string|max:255',
            'user_id'     => 'nullable|exists:users,id',
        ]);

        $diagnosis = $this->diagnose((float) $data['temperature']);
        $scan = Scan::create(array_merge($data, $diagnosis));

        return redirect()->route('dashboard')->with('success', 'Data scan manual berhasil disimpan!');
    }

    public function print($id)
    {
        $scan = Scan::with('user')->findOrFail($id);
        return view('scan.print', compact('scan'));
    }
}
