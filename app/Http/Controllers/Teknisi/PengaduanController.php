<?php

namespace App\Http\Controllers\Teknisi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengaduan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Admin\NotificationController;


class PengaduanController extends Controller
{

        public function index()
    {
        $teknisiId = Auth::id();

        $pending = Pengaduan::where('id_teknisi', $teknisiId)
                            ->where('status', 'Dikirim ke Teknisi')
                            ->orderByDesc('created_at')
                            ->get();

        $selesai = Pengaduan::where('id_teknisi', $teknisiId)
                            ->where('status', 'Selesai')
                            ->orderByDesc('created_at')
                            ->get();

        return view('kangteknisi.pengaduan.index', compact('pending', 'selesai'));
    }

    // Detail pengaduan
    public function show($id)
    {
        $pengaduan = Pengaduan::where('id', $id)
            ->where('id_teknisi', Auth::id())
            ->firstOrFail();


        return view('kangteknisi.pengaduan.show', compact('pengaduan'));
    }
    // Menyelesaikan pengaduan dan menyimpan foto
public function selesai(Request $request, $id)
{
    $request->validate([
        'bukti_foto' => 'required|image|mimes:jpeg,png,jpg|max:10240',
    ]);

    $pengaduan = Pengaduan::where('id', $id)
        ->where('id_teknisi', Auth::id())
        ->firstOrFail();

    // upload foto
    $path = $request->file('bukti_foto')->store('pengaduan', 'public');

    // update pengaduan
    $pengaduan->update([
        'bukti_foto' => $path,
        'status'     => 'Selesai',
        'selesai_at' => now(), // ⬅️ INI KUNCI
    ]);

    // 🔔 NOTIFIKASI KE ADMIN (KHUSUS PENGADUAN)
    NotificationController::createAdminNotification(
        'pengaduan_selesai',
        'Pengaduan #' . $pengaduan->id .
        ' diselesaikan oleh ' . Auth::user()->name .
        ' pada ' . now()->format('d M Y H:i'),
        Auth::id()
    );

    return redirect()->back()->with('success', 'Pengaduan berhasil diselesaikan!');
}
}