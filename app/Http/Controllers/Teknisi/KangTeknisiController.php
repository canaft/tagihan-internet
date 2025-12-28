<?php

namespace App\Http\Controllers\Teknisi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tagihan;
use App\Models\Pengaduan;
use App\Http\Controllers\Admin\NotificationController;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;



class KangTeknisiController extends Controller
{
    /* ================= DASHBOARD ================= */
    public function dashboard()
    {
        $teknisiId = auth()->id();

        $notifCount = Pengaduan::where('id_teknisi', $teknisiId)
            ->where('status', 'Dikirim ke Teknisi')
            ->count();

        return view('kangteknisi.dashboard', compact('notifCount'));
    }

    /* ================= HISTORY TAGIHAN ================= */
    public function historyLunas()
    {
        $tagihanLunas = Tagihan::where('status', 'lunas')
            ->with('pelanggan')
            ->orderBy('bulan', 'desc')
            ->get();

        return view('kangteknisi.history.lunas', compact('tagihanLunas'));
    }

    public function deleteHistoryLunas($id)
    {
        $tagihan = Tagihan::findOrFail($id);
        $tagihan->delete();

        NotificationController::createAdminNotification(
            'hapus_history_lunas',
            'Teknisi ' . auth()->user()->name . ' menghapus tagihan lunas ID #' . $id,
            auth()->id()
        );

        return back()->with('success', 'Data berhasil dihapus dan admin diberi tahu');
    }

    public function deleteAllHistoryLunas()
    {
        Tagihan::where('status', 'lunas')->delete();

        NotificationController::createAdminNotification(
            'hapus_semua_history_lunas',
            'Teknisi ' . auth()->user()->name . ' menghapus semua tagihan lunas',
            auth()->id()
        );

        return back()->with('success', 'Semua history lunas berhasil dihapus');
    }

    /* ================= SELESAI TUGAS + FOTO ================= */
 public function selesaiTugas(Request $request, $pengaduanId)
{
    try {
        $request->validate([
            'bukti_foto' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $pengaduan = Pengaduan::findOrFail($pengaduanId);

        if ($request->hasFile('bukti_foto')) {

            // hapus foto lama
            if ($pengaduan->bukti_foto && Storage::disk('public')->exists($pengaduan->bukti_foto)) {
                Storage::disk('public')->delete($pengaduan->bukti_foto);
            }

            $fileName = 'bukti_pengaduan_' . $pengaduanId . '_' . time() . '.' .
                        $request->bukti_foto->extension();

            $path = $request->bukti_foto->storeAs(
                'bukti_pengaduan',
                $fileName,
                'public'
            );

            $pengaduan->bukti_foto = $path;
        }

        $pengaduan->status = 'Selesai';
        $pengaduan->save();

        // ====================== CREATE NOTIFIKASI KE ADMIN ======================
        $adminId = User::where('role', 'admin')->value('id');

        if ($adminId) {
            Notification::create([
                'user_id'      => auth()->id(),       // teknisi
                'admin_id'     => $adminId,           // admin penerima
                'pengaduan_id' => $pengaduan->id,    // ID pengaduan selesai
                'type'         => 'pengaduan_selesai',
                'message'      => 'Teknisi ' . auth()->user()->name .
                                  ' telah menyelesaikan pengaduan pelanggan ' .
                                  ($pengaduan->pelanggan->nama ?? ''),
                'is_read'      => 0,
            ]);
        }

        return response()->json(['success' => true]);

    } catch (\Exception $e) {
        Log::error('UPLOAD FOTO ERROR: '.$e->getMessage());
        return response()->json(['error' => true], 500);
    }
}


    /* ================= SETTING ================= */
    public function setting()
    {
        return view('kangteknisi.setting');
    }

    /* ================= REQUEST UPDATE PASSWORD ================= */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'new_password' => 'required|min:8',
        ]);

        $user = auth()->user();

        // update password langsung
        $user->password = Hash::make($request->new_password);
        $user->save();

        // notif info ke admin
        $adminId = User::where('role', 'admin')->value('id');

        if ($adminId) {
            Notification::create([
                'user_id'  => $user->id,
                'admin_id' => $adminId,
                'type'     => 'info_password_changed', // ✅ BENAR
                'message'  => 'Teknisi ' . $user->name . ' telah mengganti password akunnya',
                'is_read'  => 0,
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Password berhasil diganti, silakan login ulang');
    }


    /* ================= REQUEST DELETE HISTORY ================= */
    public function requestDeleteHistory()
    {
        $user = auth()->user();
        $adminId = User::where('role', 'admin')->value('id');

        if (!$adminId) {
            return back()->with('error', 'Admin tidak ditemukan');
        }

        try {
            Notification::create([
                'user_id'  => $user->id,
                'admin_id' => $adminId,
                'type'     => 'request_delete_history',
                'message'  => $user->name . ' meminta konfirmasi hapus history lunas',
                'is_read'  => 0,
            ]);

            return back()->with('success', 'Permintaan hapus history dikirim ke admin');

        } catch (\Exception $e) {
            Log::error('Request delete history gagal: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengirim permintaan');
        }
    }
}
