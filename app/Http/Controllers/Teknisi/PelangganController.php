<?php

namespace App\Http\Controllers\Teknisi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pelanggan;
use App\Models\Area;
use App\Models\Device;
use App\Models\Package;
use App\Models\Odp;
use App\Models\Transaksi;

class PelangganController extends Controller
{
    public function index()
    {
        $pelanggans = Pelanggan::with([
                'area',
                'device',
                'package',
                'transaksi' => function($q) {
                    $q->orderByDesc('tanggal_bayar');
                }
            ])
            ->where('is_active', 1)
            ->get();

        return view('kangteknisi.pelanggan.index', compact('pelanggans'));
    }


    // ======================= DETAIL PELANGGAN ===========================
public function detail($id)
{
    $pelanggan = Pelanggan::with([
            'area',
            'device',
            'package',
            'odp',
            'transaksi' => function($q) {
                $q->orderByDesc('tanggal_bayar');
            }
        ])
        ->where('is_active', 1)
        ->findOrFail($id);

    // 🔹 Ambil transaksi terbaru (tagihan terakhir)
    $transaksiTerakhir = $pelanggan->transaksi->first();

    // 🔹 Ambil riwayat bulan yang sudah dibayar, aman untuk banyak bulan
    $riwayatPembayaran = $pelanggan->transaksi->map(function ($t) {
        $bulanTerformat = [];

        if ($t->bulan) {
            $bulanArray = explode(',', $t->bulan);
            foreach ($bulanArray as $b) {
                $b = trim($b);
                if ($b) {
                    try {
                        $bulanTerformat[] = \Carbon\Carbon::createFromFormat('Y-m', $b)->translatedFormat('F Y');
                    } catch (\Exception $e) {
                        // fallback kalau format salah
                        $bulanTerformat[] = $b;
                    }
                }
            }
        }

        return [
            'bulan' => $bulanTerformat, // array bulan terformat
            'jumlah' => $t->jumlah,
            'tanggal_bayar' => \Carbon\Carbon::parse($t->tanggal_bayar)->format('d M Y')
        ];
    });

    // Data dropdown update
    $areas = Area::all();
    $devices = Device::all();
    $packages = Package::all();
    $odps = Odp::all();

    return view('kangteknisi.pelanggan.detail', compact(
        'pelanggan',
        'transaksiTerakhir',
        'riwayatPembayaran',
        'areas',
        'devices',
        'packages',
        'odps'
    ));
}


    // ========================= UPDATE =========================
    public function update(Request $request, $id)
    {
        $pelanggan = Pelanggan::findOrFail($id);

        $pelanggan->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'area_id' => $request->area_id,
            'device_id' => $request->device_id,
            'package_id' => $request->package_id,
            'odp_id' => $request->odp_id,
            'koordinat' => $request->koordinat,
            'rw' => $request->rw,
        ]);

        return redirect()
            ->route('kangteknisi.pelanggan.detail', $id)
            ->with('success', 'Data pelanggan berhasil diperbarui');
    }
}
