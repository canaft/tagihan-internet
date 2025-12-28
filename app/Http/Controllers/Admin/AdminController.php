<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Ambil semua transaksi lunas
        $transaksiLunas = Transaksi::where('status', 'lunas')->get();

        // Total pemasukan = jumlah semua transaksi lunas - diskon
        $totalPemasukan = $transaksiLunas->sum(function($t) {
            return ($t->jumlah ?? 0) - ($t->diskon ?? 0);
        });

        // Jumlah transaksi lunas
        $jumlahTransaksiLunas = $transaksiLunas->count();

        // Total pengeluaran (misal sementara 0 dulu)
        $totalPengeluaran = 0;

        // Saldo
        $saldo = $totalPemasukan - $totalPengeluaran;

        return view('admin.dashboard', compact(
            'totalPemasukan',
            'totalPengeluaran',
            'saldo',
            'jumlahTransaksiLunas'
        ));
    }
}
