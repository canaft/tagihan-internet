<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tagihan;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use Carbon\Carbon;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;


class SalesTagihanController extends Controller
{
    // ================================
    //  LIST TAGIHAN YANG BELUM BAYAR
    // ================================
    public function index()
    {
        $bulan = now()->format('Y-m'); // contoh: 2025-11

        $tagihan = Tagihan::with('pelanggan.package')
            ->where('bulan', 'LIKE', "$bulan%")
            ->where('status', 'belum_lunas')       // <<< INI FIX UTAMA
            ->get();

        return view('sales.tagihan.index', compact('tagihan'));
    }

// DETAIL TAGIHAN
public function detail($id)
{
    $tagihan = Tagihan::with(['pelanggan.package', 'pelanggan.area'])->findOrFail($id);
    $pelanggan = $tagihan->pelanggan;

    $bulanTagihan = Carbon::parse($tagihan->bulan)->format('Y-m');

    // Ambil transaksi terkait tagihan (untuk tanggal & waktu bayar)
    $transaksi = $pelanggan->transaksi()
        ->where('bulan', $bulanTagihan)
        ->latest('tanggal_bayar')
        ->first();

    $tanggalBayar = $transaksi
        ? Carbon::parse($transaksi->tanggal_bayar)->format('d-m-Y')
        : '-';

    $waktuBayar = $transaksi
        ? Carbon::parse($transaksi->tanggal_bayar)->format('H:i')
        : '-';

    // STATUS TAGIHAN
    $statusTagihan = ($tagihan->status === 'lunas') ? 'lunas' : 'belum_bayar';

    // AMBIL TEMPLATE WA
    $templateWA = Setting::where('category', $statusTagihan)
        ->where('is_default', 1)
        ->first();

    // HITUNG BIAYA
    $hargaPaket = optional($pelanggan->package)->harga ?? 0;
    $biayaTambahan1 = $pelanggan->biaya_tambahan_1 ?? 0;
    $biayaTambahan2 = $pelanggan->biaya_tambahan_2 ?? 0;
    $diskon = $pelanggan->diskon ?? 0;

    $subtotal = $hargaPaket + $biayaTambahan1 + $biayaTambahan2;
    $tagihanPerbulan = $subtotal - ($subtotal * $diskon / 100);
    $totalBayar = $tagihanPerbulan;

    $masaAktif = optional($pelanggan->tanggal_tagihan)->format('d-m-Y') ?? '-';
    $admin = Auth::user()->name ?? 'Sales';

    // Nomor WA format internasional
    $nomor = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $pelanggan->phone));

    // ================== GENERATE PESAN WA ==================
    if ($templateWA) {
        $pesanWA = str_replace(
            [
                '{nama}', '{nomor}', '{area}',
                '{tanggal_bayar}', '{waktu_bayar}', '{bulan_tagihan}', '{jenis_paket}',
                '{harga_paket}', '{biaya_tambahan1}', '{biaya_tambahan2}',
                '{diskon}', '{tagihan_perbulan}', '{total}',
                '{masa_aktif}', '{status}', '{admin}'
            ],
            [
                $pelanggan->name,
                $nomor,
                optional($pelanggan->area)->nama_area ?? '-',
                $tanggalBayar,
                $waktuBayar,
                Carbon::parse($tagihan->bulan)->translatedFormat('F Y'),
                optional($pelanggan->package)->nama_paket ?? '-',
                number_format($hargaPaket,0,',','.'), 
                number_format($biayaTambahan1,0,',','.'), 
                number_format($biayaTambahan2,0,',','.'), 
                number_format($diskon,0,',','.'), 
                number_format($tagihanPerbulan,0,',','.'), 
                number_format($totalBayar,0,',','.'), 
                $masaAktif,
                strtoupper($tagihan->status ?? 'belum_lunas'),
                $admin
            ],
            $templateWA->value
        );
    } else {
        $pesanWA = "Halo {$pelanggan->name}, silakan lakukan pembayaran tagihan bulan " . Carbon::parse($tagihan->bulan)->translatedFormat('F Y') . ".";
    }

    return view('sales.tagihan.detail', compact('tagihan', 'pelanggan', 'pesanWA', 'nomor'));
}



    // ================================
    //  UPDATE STATUS -> LUNAS
    // ================================
    public function updateStatus(Request $request, $id)
    {
        $tagihan = Tagihan::with('pelanggan.package')->findOrFail($id);
        $statusLama = $tagihan->status;
        $statusBaru = 'lunas';

        $tagihan->status = $statusBaru;
        $tagihan->tanggal_bayar = now();
        $tagihan->save();

        // Jika sebelumnya bukan lunas -> cek / buat transaksi
        if ($statusLama !== 'lunas') {
            $cek = Transaksi::where('bulan', $tagihan->bulan)
                ->where('pelanggan_id', $tagihan->pelanggan_id)
                ->first();

            if (!$cek) {

                $p = $tagihan->pelanggan;
                $hargaPaket = $p->package->harga ?? 0;
                $b1 = $p->biaya_tambahan_1 ?? 0;
                $b2 = $p->biaya_tambahan_2 ?? 0;
                $diskon = $p->diskon ?? 0;

                $jumlah = ($hargaPaket + $b1 + $b2) - (($hargaPaket + $b1 + $b2) * $diskon / 100);

                Transaksi::create([
                    'pelanggan_id'   => $p->id,
                    'kode_transaksi' => 'TRX-' . time(),
                    'jumlah'         => $jumlah,
                    'diskon'         => $diskon,
                    'metode'         => $tagihan->metode_bayar ?? 'cash',
                    'status'         => 'lunas',
                    'dibayar_oleh'   => auth()->user()->name ?? 'sales',
                    'bulan'          => $tagihan->bulan,
                    'tanggal_bayar'  => now(),
                    'ip_address'     => $request->ip(),
                ]);
            }
        }

        return back()->with('success', 'Status pembayaran berhasil diperbarui!');
    }   

    // DETAIL SEMUA TAGIHAN PELANGGAN


 public function pelangganTagihan($id)
    {
        $pelanggan = Pelanggan::with(['tagihan' => function ($q) {
            $q->orderBy('bulan', 'asc');
        }, 'package', 'area'])->findOrFail($id);

        $bulanSekarang = Carbon::now()->format('Y-m');

        // Ambil tagihan bulan ini
        $tagihanBulanIni = $pelanggan->tagihan->firstWhere(function($t) use ($bulanSekarang) {
            return Carbon::parse($t->bulan)->format('Y-m') === $bulanSekarang;
        });

        $statusTagihan = ($tagihanBulanIni && $tagihanBulanIni->status === 'lunas') ? 'lunas' : 'belum_bayar';

        // Ambil transaksi bulan ini (untuk tanggal & waktu bayar)
        $transaksiBulanIni = $pelanggan->transaksi()
            ->where('bulan', $bulanSekarang)
            ->latest('tanggal_bayar')
            ->first();

        $tanggalBayar = $transaksiBulanIni
            ? Carbon::parse($transaksiBulanIni->tanggal_bayar)->format('d-m-Y')
            : '-';

        $waktuBayar = $transaksiBulanIni
            ? Carbon::parse($transaksiBulanIni->tanggal_bayar)->format('H:i')
            : '-';

        // Ambil template WA
        $templateWA = Setting::where('category', $statusTagihan)
            ->where('is_default', 1)
            ->first();

        // Biaya
        $hargaPaket = optional($pelanggan->package)->harga ?? 0;
        $biayaTambahan1 = $pelanggan->biaya_tambahan_1 ?? 0;
        $biayaTambahan2 = $pelanggan->biaya_tambahan_2 ?? 0;
        $diskon = $pelanggan->diskon ?? 0;

        $tagihanPerbulan = ($hargaPaket + $biayaTambahan1 + $biayaTambahan2) - 
                           (($hargaPaket + $biayaTambahan1 + $biayaTambahan2) * $diskon / 100);
        $totalBayar = $tagihanPerbulan;

        $masaAktif = optional($pelanggan->tanggal_tagihan)->format('d-m-Y') ?? '-';

        // Nomor WA format internasional
        $nomor = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $pelanggan->phone));

        // Template pesan
        $pesanTemplate = $templateWA->value ?? 
            "Halo {nama}, silakan lakukan pembayaran tagihan bulan {bulan_tagihan}.";

        $pesanWA = strtr($pesanTemplate, [
            '{nama}' => $pelanggan->name,
            '{nomor}' => $nomor,
            '{area}' => $pelanggan->area->nama_area ?? '-',
            '{bulan}' => now()->translatedFormat('F Y'),
            '{bulan_tagihan}' => $tagihanBulanIni 
                ? Carbon::parse($tagihanBulanIni->bulan)->translatedFormat('F Y') 
                : now()->translatedFormat('F Y'),
            '{jenis_paket}' => optional($pelanggan->package)->nama_paket ?? '-',
            '{paket}' => optional($pelanggan->package)->nama_paket ?? '-',
            '{biaya_paket}' => number_format($hargaPaket,0,',','.'), 
            '{biaya_tambahan1}' => number_format($biayaTambahan1,0,',','.'), 
            '{biaya_tambahan2}' => number_format($biayaTambahan2,0,',','.'), 
            '{diskon}' => number_format($diskon,0,',','.'), 
            '{ppn}' => number_format(0,0,',','.'), 
            '{tagihan_perbulan}' => number_format($tagihanPerbulan,0,',','.'), 
            '{total}' => number_format($totalBayar,0,',','.'), 
            '{status}' => $tagihanBulanIni?->status ?? '-',
            '{masa_aktif}' => $masaAktif,
            '{admin}' => Auth::user()->name ?? 'Sales',
            '{tanggal_bayar}' => $tanggalBayar,   // <-- ambil dari transaksi
            '{waktu_bayar}' => $waktuBayar        // <-- ambil dari transaksi
        ]);

        return view('sales.pelanggan.detail', compact('pelanggan', 'pesanWA', 'nomor'));
    }
    // ================================
    //  BAYAR MULTI BULAN
    // ================================
    public function bayar(Request $request, $id)
    {
        $pelanggan = Pelanggan::with('package')->findOrFail($id);

        // Pastikan array
        $bulanTerpilih = $request->input('bulan');
        if (is_string($bulanTerpilih)) {
            $bulanTerpilih = explode(',', $bulanTerpilih);
        }

        $totalBayar = 0;

        foreach ($bulanTerpilih as $bulan) {

            $tagihan = Tagihan::where('pelanggan_id', $pelanggan->id)
                ->where('bulan', $bulan)
                ->first();

            if (!$tagihan) continue;

            if ($tagihan->status !== 'lunas') {
                $hargaPaket = $pelanggan->package->harga ?? 0;
                $b1 = $pelanggan->biaya_tambahan_1 ?? 0;
                $b2 = $pelanggan->biaya_tambahan_2 ?? 0;
                $diskon = $pelanggan->diskon ?? 0;

                $jumlah = ($hargaPaket + $b1 + $b2) - (($hargaPaket + $b1 + $b2) * $diskon / 100);

                // update tagihan
                $tagihan->status = 'lunas';
                $tagihan->tanggal_bayar = now();
                $tagihan->jumlah = $jumlah;
                $tagihan->save();

                // transaksi
                Transaksi::firstOrCreate(
                    [
                        'pelanggan_id' => $pelanggan->id,
                        'bulan' => $bulan
                    ],
                    [
                        'kode_transaksi' => 'TRX-' . time(),
                        'jumlah'         => $jumlah,
                        'diskon'         => $diskon,
                        'metode'         => $tagihan->metode_bayar ?? 'cash',
                        'status'         => 'lunas',
                        'dibayar_oleh'   => auth()->user()->name ?? 'sales',
                        'tanggal_bayar'  => now(),
                        'ip_address'     => $request->ip(),
                    ]
                );

                $totalBayar += $jumlah;
            }
        }

        return back()->with('success', "Pembayaran berhasil. Total: Rp " . number_format($totalBayar, 0, ',', '.'));
    }
}
