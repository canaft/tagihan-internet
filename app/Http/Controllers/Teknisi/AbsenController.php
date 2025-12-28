<?php

namespace App\Http\Controllers\Teknisi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Absensi;
use Carbon\Carbon;
use App\Models\IzinAbsen;

class AbsenController extends Controller
{
    // ===============================
    // 1. HALAMAN UTAMA ABSEN
    // ===============================
    public function index()
    {
        $user = auth()->user();
        $today = today(); // Carbon hari ini

        // Ambil absen hari ini
        $absen = Absensi::where('user_id', $user->id)
                        ->whereDate('tanggal', $today)
                        ->first();

        // Riwayat absensi user
        $history = Absensi::where('user_id', $user->id)
                          ->orderBy('tanggal','desc')
                          ->get();

        return view('absen.sekarang', compact('user', 'absen', 'history'));
    }

    // ===============================
    // 2. HALAMAN DETAIL ABSEN
    // ===============================
    public function detail()
    {
        $user = auth()->user();
        $today = today();

        $absen = Absensi::where('user_id', $user->id)
                        ->whereDate('tanggal', $today)
                        ->first();

        return view('absen.detail', compact('user', 'absen'));
    }

    // ===============================
    // 3. PROSES ABSEN MASUK (TOMBOL)
    // ===============================
    public function absenMasuk()
    {
        $user = auth()->user();

        // Cek jika sudah ada absen hari ini
        $absen = Absensi::where('user_id', $user->id)
                        ->whereDate('tanggal', today())
                        ->first();

        if ($absen) {
            return back()->with('error', 'Anda sudah absen hari ini.');
        }

        // Buat absen baru untuk hari ini
        Absensi::create([
            'user_id' => $user->id,
            'tanggal' => today(),
            'jam_masuk' => now(),
        ]);

        return back()->with('success', 'Absen masuk berhasil.');
    }

    // ===============================
    // 4. PROSES ABSEN PULANG (TOMBOL)
    // ===============================
    public function absenPulang($id)
    {
        $absen = Absensi::findOrFail($id);

        if (!$absen->jam_pulang) {
            $absen->update([
                'jam_pulang' => now(), // pakai now() langsung
            ]);
        }

        return back()->with('success', 'Absen pulang berhasil.');
    }

public function history()
{
    $userId = auth()->id();
    $absens = Absensi::where('user_id', $userId)
                     ->orderByDesc('tanggal')
                     ->get();

    $izinAbsens = IzinAbsen::where('user_id', $userId)
                            ->orderByDesc('tanggal')
                            ->get();

    // Ambil absen hari ini
    $absenHariIni = Absensi::where('user_id', $userId)
                            ->whereDate('tanggal', today())
                            ->first();

    // Ambil izin hari ini
    $izinHariIni = IzinAbsen::where('user_id', $userId)
                             ->whereDate('tanggal', today())
                             ->first();

    return view('absen.history', compact('absens', 'izinAbsens', 'absenHariIni', 'izinHariIni'));
}

// ===============================
// 2. HALAMAN DETAIL ABSEN
// ===============================
public function detailHistory()
{
    $user = auth()->user();
    $today = today();

    // Absen hari ini
    $absen = Absensi::where('user_id', $user->id)
                    ->whereDate('tanggal', $today)
                    ->first();

    // Ambil 3 history terbaru
    $absenMini = Absensi::where('user_id', $user->id)
                        ->orderByDesc('tanggal')
                        ->limit(3)
                        ->get();

    return view('absen.detailhistory', compact('user', 'absen', 'absenMini'));
}

public function izin() {
    return view('absen.izin');
}

public function submitIzin(Request $request) {
    $request->validate([
        'tanggal' => 'required|date',
        'alasan' => 'required|string|max:255',
    ]);

    IzinAbsen::create([
        'user_id' => auth()->id(),
        'tanggal' => $request->tanggal,
        'alasan' => $request->alasan,
        'status' => 'pending',
    ]);

    return redirect()->route('absen.history')->with('success', 'Izin berhasil dikirim ke admin!');
}


}
