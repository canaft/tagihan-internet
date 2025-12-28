<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Tagihan;

class TransaksiController extends Controller
{

    public function cash()
    {
        $transaksiCash = Transaksi::with([
            'pelanggan.package',
            'pelanggan.area',
            'pelanggan.device'
        ])
        ->where('metode', 'cash')
        ->where('status', 'lunas')
        ->orderBy('tanggal_bayar', 'desc')
        ->get();

        return view('admin.transaksi_cash', compact('transaksiCash'));
    }

    public function online()
    {
        $transaksiOnline = Transaksi::with('pelanggan')
            ->where('metode', 'online')
            ->orderBy('tanggal_bayar', 'desc')
            ->get();

        return view('admin.transaksi_online', compact('transaksiOnline'));
    }

    public function bayar(Tagihan $tagihan)
    {
        // Ubah status tagihan
        $tagihan->update([
            'status' => 'lunas',
            'tanggal_bayar' => now()
        ]);

        // Buat transaksi
        Transaksi::create([
            'pelanggan_id'   => $tagihan->pelanggan_id,
            'jumlah'         => $tagihan->jumlah_tagihan,
            'diskon'         => $tagihan->diskon ?? 0,
            'metode'         => $tagihan->metode ?? 'cash',
            'dibayar_oleh'   => 'pelanggan',
            'tanggal_bayar'  => now(),
            'bulan'          => $tagihan->bulan,
            'kode_transaksi' => 'TRX-' . strtoupper(uniqid()),
            'ip_address'     => request()->ip(),
            'status'         => 'lunas',
        ]);

        return back()->with('success', 'Pembayaran berhasil!');
    }

    /*
    |--------------------------------------------------------------------------
    |  BATALKAN TRANSAKSI
    |  sesuai logic kamu:
    |  - transaksi DIHAPUS
    |  - tagihan dikembalikan menjadi BELUM BAYAR
    |--------------------------------------------------------------------------
    */
public function batalkan(Request $request)
{
    $request->validate([
        'pelanggan_id' => 'required|integer',
        'bulan'        => 'required|string',
    ]);

    // Cari transaksi
    $transaksi = Transaksi::where('pelanggan_id', $request->pelanggan_id)
        ->where('bulan', $request->bulan)
        ->where('status', 'lunas')
        ->first();

    if (!$transaksi) {
        return response()->json(['error' => 'Transaksi tidak ditemukan'], 404);
    }

    // Hapus transaksi
    $transaksi->delete();

    // Pecah bulan jika multi
    $daftarBulan = explode(',', $request->bulan);

    // Update tagihan dengan LIKE (karena format di DB "YYYY-MM-01")
    Tagihan::where('pelanggan_id', $request->pelanggan_id)
        ->where(function ($q) use ($daftarBulan) {
            foreach ($daftarBulan as $bln) {
                $prefix = substr($bln, 0, 7); // ambil "2026-02"
                $q->orWhere('bulan', 'LIKE', "$prefix%");
            }
        })
        ->update([
            'status'        => 'belum_lunas',
            'tanggal_bayar' => null
        ]);

    return response()->json(['success' => true]);
}
}