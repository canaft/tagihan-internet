<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Izin;
use App\Models\User;
use Illuminate\Http\Request;

class IzinController extends Controller
{
    // Tampilkan semua izin
    public function index()
    {
        $izin = Izin::with('user')->orderBy('created_at', 'DESC')->get();
        return view('admin.izin.index', compact('izin'));
    }

    // Setujui izin via AJAX
    public function setujui($id)
    {
        try {
            $izin = Izin::findOrFail($id);
            $izin->status = 'disetujui';
            $izin->save();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Tolak izin via AJAX
    public function tolak($id)
    {
        try {
            $izin = Izin::findOrFail($id);
            $izin->status = 'ditolak';
            $izin->save();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Kembalikan status pending via AJAX
    public function pending($id)
    {
        try {
            $izin = Izin::findOrFail($id);
            $izin->status = 'pending';
            $izin->save();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Tampilkan izin per bulan
    public function bulan($user_id, $bulan, $tahun)
    {
        $izin = Izin::with('user')
            ->where('user_id', $user_id)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->orderBy('tanggal', 'DESC')
            ->get();

        $user = $izin->first()->user ?? User::find($user_id);

        return view('admin.izin.bulan', compact('izin', 'user', 'bulan', 'tahun'));
    }
}
