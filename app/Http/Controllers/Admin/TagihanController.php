<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pelanggan;
use App\Models\Tagihan;
use App\Models\Transaksi;
use Carbon\Carbon;
    use App\Models\Setting;

use Illuminate\Support\Facades\DB;

class TagihanController extends Controller
{
    // ===============================
    // INDEX TAGIHAN PER BULAN
    // ===============================
    public function index(Request $request)
    {
        $bulan = $request->get('bulan', now()->format('Y-m'));

        $pelanggans = Pelanggan::with([
            'package',
            'area',
            'tagihans' => fn($q) => $q->where('bulan', 'like', $bulan . '%')
        ])->where('is_active', 1)->get();

        $adaTagihan = $pelanggans->contains(fn($p) => $p->tagihans->isNotEmpty());

        return view('admin.tagihan.index', compact('pelanggans', 'bulan', 'adaTagihan'));
    }

    public function struk(Request $request, $id)
    {
        $pelanggan = Pelanggan::with(['area', 'device', 'package'])->findOrFail($id);

        $query = Tagihan::where('pelanggan_id', $pelanggan->id)
                        ->where('status', 'lunas');

        // Jika filter aktif
        if ($request->filled('from')) {
            $query->whereDate('tanggal_bayar', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('tanggal_bayar', '<=', $request->to);
        }

        $tagihans = $query->orderBy('tanggal_bayar', 'desc')->get();

        return view('admin.struk', compact('pelanggan', 'tagihans'));
    }

    // ===============================
    // GENERATE TAGIHAN MANUAL SATU BULAN
    // ===============================
    public function generate(Request $request)
    {
        $request->validate(['bulan' => 'required|date_format:Y-m']);
        $bulan = $request->bulan; // format Y-m

        $pelanggans = Pelanggan::with(['package', 'area'])->where('is_active', 1)->get();

        foreach ($pelanggans as $pelanggan) {
            $this->createTagihanIfNotExist($pelanggan, $bulan);
        }

        return redirect()->back()->with('success', "Tagihan bulan $bulan berhasil dibuat!");
    }

    // ===============================
    // GENERATE TAGIHAN JANGKA PANJANG
    // Default: generate sampai X tahun ke depan (boleh diset via request 'years')
    // Jika kamu mau "sampai berhenti" secara teknis kita tetap perlu batas waktu
    // (misal 10 tahun) — tapi admin dapat regenerate atau extend.
    // ===============================
    public function generateLongTerm(Request $request, Pelanggan $pelanggan)
    {
        // years: berapa tahun ke depan (default 10)
        $years = (int) $request->get('years', 10);
        if ($years < 1) $years = 10;

        // mulai dari bulan registrasi pelanggan
        $start = Carbon::parse($pelanggan->tanggal_register ?? $pelanggan->created_at)->startOfMonth();
        $end = $start->copy()->addYears($years)->endOfMonth();

        while ($start <= $end) {
            $this->createTagihanIfNotExist($pelanggan, $start->format('Y-m'));
            $start->addMonth();
        }

        return back()->with('success', "Tagihan jangka panjang berhasil dibuat untuk {$pelanggan->name} hingga {$years} tahun ke depan.");
    }

    // Helper bila mau generate otomatis saat register (panggil dari PelangganController setelah simpan)
public function generateOnRegister(Pelanggan $pelanggan)
{
    $years = 10;
    $start = Carbon::parse($pelanggan->tanggal_register ?? $pelanggan->created_at)->startOfMonth();
    $end = $start->copy()->addYears($years)->endOfMonth();

    $bulanPertama = true;

while ($start <= $end) {
    $this->createTagihanIfNotExist($pelanggan, $start->format('Y-m'));
    $start->addMonth();
}


    return true;
}



    // ===============================
    // PRIVATE METHOD: CREATE TAGIHAN JIKA BELUM ADA
    // menerima format bulan sebagai 'Y-m' atau 'Y-m-d'
    // di DB kolom `bulan` akan disimpan dengan format 'Y-m-01'
    // ===============================
// Fungsi createTagihanIfNotExist yang mendukung skip diskon bulan pertama
private function createTagihanIfNotExist($pelanggan, $bulan)
{
    try {
        $c = Carbon::parse($bulan);
    } catch (\Exception $e) {
        return false;
    }

    $bulanDb = $c->format('Y-m-01');

    $exists = Tagihan::where('pelanggan_id', $pelanggan->id)
        ->where('bulan', $bulanDb)
        ->exists();

    if ($exists) return true;

    $hargaPaket = optional($pelanggan->package)->harga ?? 0;
    $biaya1     = $pelanggan->biaya_tambahan_1 ?? 0;
    $biaya2     = $pelanggan->biaya_tambahan_2 ?? 0;
    $diskon     = $pelanggan->diskon ?? 0;

    $total = $hargaPaket + $biaya1 + $biaya2;

    // ✅ DISKON BERLAKU UNTUK SEMUA BULAN TANPA KECUALI
    $jumlahAkhir = $total - ($total * $diskon / 100);

    Tagihan::create([
        'pelanggan_id'    => $pelanggan->id,
        'sales_id'        => $pelanggan->area->sales->id ?? null,
        'bulan'           => $bulanDb,
        'jumlah'          => round($jumlahAkhir),
        'status'          => 'belum_lunas',
        'metode_bayar'    => 'cash',
        'tanggal_tagihan' => now(),
        'diskon'          => $diskon,
    ]);

    return true;
}






    // ===============================
    // HISTORY TAGIHAN LUNAS
    // ===============================
    public function historyLunas()
    {
        $tagihanLunas = Tagihan::where('status', 'lunas')->get();
        return view('history.lunas', compact('tagihanLunas'));
    }

    public function deleteLunas($id)
    {
        Tagihan::findOrFail($id)->delete();
        return back()->with('success', 'Tagihan berhasil dihapus.');
    }

    public function deleteAllLunas()
    {
        Tagihan::where('status', 'lunas')->delete();
        return back()->with('success', 'Semua tagihan lunas berhasil dihapus.');
    }

    // ===============================
    // DETAIL TAGIHAN
    // ===============================
    public function detail($id)
    {
        $tagihan = Tagihan::with('pelanggan')->findOrFail($id);
        return view('admin.tagihan.detail', compact('tagihan'));
    }

public function detail_belum_bayar($id)
{
    // Ambil pelanggan beserta tagihan belum lunas terbaru dan relasi area/package
    $pelanggan = Pelanggan::with([
        'tagihans' => fn($q) => $q->where('status', 'belum_lunas')->latest('bulan'),
        'area',
        'package'
    ])->findOrFail($id);

    $tagihan = $pelanggan->tagihans->first();

    // Ambil template WA aktif untuk kategori 'belum_bayar'
    $template = DB::table('settings')
        ->where('category', 'belum_bayar')
        ->where('is_default', 1)
        ->first();

    // Fallback jika tidak ada template aktif
    $pesanTemplate = $template->value ?? "Halo {nama}, tagihan bulan {bulan_tagihan} sebesar {total} belum dibayar.";

    // Ganti placeholder dengan data sebenarnya
    if (!$tagihan) {
        // Kalau belum ada tagihan
        $pesan = strtr($pesanTemplate, [
            '{nama}' => $pelanggan->name,
            '{nomor}' => $pelanggan->phone,
            '{area}' => $pelanggan->area->name ?? '-',
            '{bulan_tagihan}' => '-',
            '{jenis_paket}' => $pelanggan->package->name ?? '-',
            '{biaya_paket}' => '0',
            '{diskon}' => '0',
            '{total}' => '0',
        ]);
    } else {
        $hargaPaket      = optional($pelanggan->package)->harga ?: 0;
        $biayaTambahan1  = $pelanggan->biaya_tambahan_1 ?: 0;
        $biayaTambahan2  = $pelanggan->biaya_tambahan_2 ?: 0;
        $diskon          = $pelanggan->diskon ?: 0;

        $tagihanPerBulan = $hargaPaket + $biayaTambahan1 + $biayaTambahan2 
                           - (($hargaPaket + $biayaTambahan1 + $biayaTambahan2) * $diskon / 100);

        $totalBayar = $tagihanPerBulan * ($pelanggan->jumlah_bulan_bayar ?? 1);

        $pesan = strtr($pesanTemplate, [
            '{nama}' => $pelanggan->name,
            '{nomor}' => preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $pelanggan->phone)),
            '{area}' => $pelanggan->area->name ?? '-',
            '{bulan_tagihan}' => Carbon::parse($tagihan->bulan)->translatedFormat('F Y'),
            '{jenis_paket}' => $pelanggan->package->name ?? '-',
            '{biaya_paket}' => number_format($hargaPaket,0,',','.'),
            '{diskon}' => $diskon,
            '{total}' => number_format($totalBayar,0,',','.'),
        ]);
    }

    return view('admin.detail_belum_bayar', compact('pelanggan', 'tagihan', 'pesan', 'pesanTemplate'));
}





    // ===============================
    // SET LUNAS MULTI BULAN (bayar N bulan sekaligus)
    // menerima input bulan_dipilih => 'YYYY-MM,YYYY-MM,...' atau jumlah_bulan
    // ===============================
 public function setLunasMulti(Request $request, $id)
{
    $request->validate([
        'bulan_dipilih' => 'nullable|string',
        'jumlah_bulan'  => 'nullable|integer|min:1'
    ]);

    $pelanggan = Pelanggan::findOrFail($id);

    // ============================
    // AMBIL LIST BULAN DIPILIH
    // ============================
    if ($request->filled('bulan_dipilih')) {
        // dari checkbox swal
        $bulanDipilih = explode(',', $request->bulan_dipilih);

    } else {
        // AUTO AMBIL N BULAN TERLAMA YANG BELUM LUNAS
        $jumlah = $request->get('jumlah_bulan', 1);

        $bulanDipilih = Tagihan::where('pelanggan_id', $pelanggan->id)
            ->where('status', 'belum_lunas')
            ->orderBy('bulan', 'asc')
            ->limit($jumlah)
            ->pluck('bulan')
            ->map(fn($b) => Carbon::parse($b)->format('Y-m'))
            ->toArray();
    }


    // ============================
    // TRANSAKSI DATABASE
    // ============================
    DB::transaction(function () use ($pelanggan, $bulanDipilih, $request) {

        $total = 0;
        $tagihanIds = [];

        foreach ($bulanDipilih as $bulanKey) {

            // SIMPAN DALAM FORMAT DATABASE (Y-m-01)
            $bulanDb = Carbon::parse($bulanKey)->format('Y-m-01');

            // CEK ADA TAGIHAN ATAU TIDAK
            $t = Tagihan::where('pelanggan_id', $pelanggan->id)
                ->where('bulan', $bulanDb)
                ->first();

            // JIKA BELUM ADA → BUAT BARU
            if (!$t) {
                $this->createTagihanIfNotExist($pelanggan, $bulanKey);

                $t = Tagihan::where('pelanggan_id', $pelanggan->id)
                    ->where('bulan', $bulanDb)
                    ->first();
            }

            // UPDATE JADI LUNAS
            if ($t && $t->status !== 'lunas') {
                $t->update([
                    'status'        => 'lunas',
                    'tanggal_bayar' => now(),
                ]);

                $total += $t->jumlah;
                $tagihanIds[] = $t->id;
            }
        }

        // ============================
        // SIMPAN TRANSAKSI
        // ============================
        if (!empty($tagihanIds)) {

            $bulanString = implode(',', array_map(
                fn($b) => Carbon::parse($b)->format('Y-m'),
                $bulanDipilih
            ));

            Transaksi::create([
                'pelanggan_id'   => $pelanggan->id,
                'kode_transaksi' => 'TRX-' . strtoupper(uniqid()),
                'jumlah'         => $total,
                'diskon'         => 0,
                'metode'         => $request->metode ?? 'cash',
                'status'         => 'success',
                'dibayar_oleh'   => auth()->user()->name ?? 'Admin',
                'bulan'          => $bulanString,
                'tanggal_bayar'  => now(),
                'ip_address'     => request()->ip(),
            ]);
        }

    });

    // ============================
    // BUAT NOTIF NAMA BULAN
    // ============================
    $listBulanNama = array_map(function ($b) {
        return Carbon::parse($b . '-01')->translatedFormat('F Y');
    }, $bulanDipilih);

    // Format pesan
    if (count($listBulanNama) == 1) {
        $pesan = "Pembayaran bulan {$listBulanNama[0]} selesai.";
    } else {
        $pesan = "Pembayaran untuk bulan: " . implode(', ', $listBulanNama) . " selesai.";
    }

    return redirect()->back()->with('success', $pesan);
}




    // ===============================
    // TAGIHAN BELUM BAYAR
    // ===============================
    public function belumBayar()
{
    $pelanggans = Pelanggan::whereHas('tagihans', fn($q) => $q->where('status', 'belum_lunas'))
        ->with([
            'area',
            'package',
            'tagihans' => fn($q) => $q->where('status', 'belum_lunas')->latest('bulan')
        ])
        ->get();

    // Hitung total tagihan belum lunas per pelanggan
    $pelanggans->map(function($pelanggan) {
        $pelanggan->totalBelumBayar = $pelanggan->tagihans->sum(function($tagihan) use ($pelanggan) {
            $hargaPaket     = optional($pelanggan->package)->harga ?: 0;
            $biayaTambahan1 = $tagihan->biaya_tambahan_1 ?: 0;
            $biayaTambahan2 = $tagihan->biaya_tambahan_2 ?: 0;
            $diskon         = $pelanggan->diskon ?: 0;

            $subtotal = $hargaPaket + $biayaTambahan1 + $biayaTambahan2;
            $subtotal -= $subtotal * $diskon / 100;

            return $subtotal;
        });

        return $pelanggan;
    });

    return view('admin.belum_bayar', compact('pelanggans'));
}


    // ===============================
    // Optional: regenerate semua tagihan (hapus lalu generate ulang)
    // Gunakan dengan hati-hati (admin only)
    // ===============================
    public function regenerate(Pelanggan $pelanggan, Request $request)
    {
        $keepYears = (int) $request->get('years', 10);

        Tagihan::where('pelanggan_id', $pelanggan->id)->delete();

        // generate ulang
        $start = Carbon::parse($pelanggan->tanggal_register ?? $pelanggan->created_at)->startOfMonth();
        $end = $start->copy()->addYears($keepYears)->endOfMonth();

        while ($start <= $end) {
            $this->createTagihanIfNotExist($pelanggan, $start->format('Y-m'));
            $start->addMonth();
        }

        return back()->with('success', 'Tagihan berhasil digenerate ulang.');
    }


public function show($id)
{
    $pelanggan = Pelanggan::with(['package','device','area','odp','tagihan'])->findOrFail($id);

    // ambil template WA
    $templateWA = Setting::where('key_name', 'template_wa_belum_bayar')->value('value');

    return view('admin.belum_bayar.show', compact('pelanggan', 'templateWA'));
}

}
