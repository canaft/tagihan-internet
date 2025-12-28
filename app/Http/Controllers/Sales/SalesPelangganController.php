<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Pelanggan;
use App\Models\Tagihan;   // <-- WAJIB DITAMBAHKAN
use Illuminate\Support\Facades\Hash;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request; // ✅ PENTING


class SalesPelangganController extends Controller
{
    public function index()
    {
        // Jika tabel pelanggan tidak ada kolom wilayah, ambil semua pelanggan
        $pelanggan = Pelanggan::all();

        // Jika pakai relasi area:
        // $wilayah = Auth::user()->wilayah;
        // $pelanggan = Pelanggan::where('area_id', $wilayah)->get();

        return view('sales.pelanggan.index', compact('pelanggan'));
    }

        public function detail($id)
    {
        $pelanggan = Pelanggan::with(['package', 'device', 'area', 'tagihan'])->findOrFail($id);
        return view('sales.pelanggan.detail', compact('pelanggan'));
    }
public function lunas()
{
    $pelanggan = Pelanggan::with(['package', 'device', 'tagihan'])
        ->whereHas('tagihan', function($q) {
            $q->where('status', 'lunas');
        })
        ->get();

    return view('sales.pelanggan.lunas', compact('pelanggan'));
}
public function detailLunas($id)
{
    $pelanggan = Pelanggan::with(['area','package','device'])->findOrFail($id);

    $tagihan = Tagihan::where('pelanggan_id', $id)->where('status', 'lunas')->get();

    return view('sales.pelanggan.detail_lunas', compact('pelanggan','tagihan'));
}
public function detailLunasJson($id)
{
    $pelanggan = Pelanggan::findOrFail($id);

    $transaksi = \App\Models\Transaksi::where('pelanggan_id', $pelanggan->id)
        ->where('status', 'lunas')
        ->orderBy('tanggal_bayar', 'desc')
        ->get();

    $riwayat = [];

    foreach ($transaksi as $t) {
        $bulanList = explode(',', $t->bulan);
        $jumlahPerBulan = $t->jumlah / count($bulanList); // 🔹 bagi rata per bulan

        foreach ($bulanList as $bulan) {
            $riwayat[] = [
                'bulan' => \Carbon\Carbon::parse($bulan.'-01')->translatedFormat('F Y'),
                'nominal' => number_format($jumlahPerBulan, 0, ',', '.'),
                'tanggal_bayar' => $t->tanggal_bayar ? \Carbon\Carbon::parse($t->tanggal_bayar)->format('d-m-Y') : '-',
                'dibayar_oleh' => $t->dibayar_oleh ?? '-',
            ];
        }
    }

    return response()->json([
        'pelanggan' => [
            'nama' => $pelanggan->name,
            'alamat' => $pelanggan->alamat,
        ],
        'riwayat' => $riwayat
    ]);
}

public function setting()
{
    return view('sales.setting');
}
public function updatePassword(Request $request)
{
    $request->validate([
        'new_password' => 'required|min:8',
    ]);

    $user = auth()->user();

    // update password
    $user->password = Hash::make($request->new_password);
    $user->save();

    // buat notifikasi ke admin
    $adminId = User::where('role', 'admin')->value('id');
    if ($adminId) {
        Notification::create([
            'user_id'  => $user->id,
            'admin_id' => $adminId,
            'type'     => 'info_password_changed', 
            'message'  => 'Sales ' . $user->name . ' telah mengganti password akunnya',
            'is_read'  => 0,
        ]);
    }

    // logout user setelah ganti password
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/login')->with('success', 'Password berhasil diganti, silakan login ulang');
}
}
