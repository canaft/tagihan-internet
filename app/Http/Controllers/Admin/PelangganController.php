<?php

namespace App\Http\Controllers\Admin;

use App\Models\Pelanggan;
use App\Models\Tagihan;
use App\Models\Package;
use App\Models\Area;
use App\Models\Device;
use App\Models\Transaksi;
use App\Models\ODP;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\TagihanController; // <-- ditambahkan
use App\Models\Setting;


class PelangganController extends Controller
{
    // ================= STEP 0: DASHBOARD =================
    
public function index(Request $request)
{
    $bulanSekarang = now()->month;
$tahunSekarang = now()->year;
$awalBulanSekarang = now()->startOfMonth();

    // Ambil bulan & tahun dari query string atau default ke sekarang
    $bulan = $request->input('bulan', now()->month);
    $tahun = $request->input('tahun', now()->year);

    // ===================== PELANGGAN AKTIF BULAN SEBELUMNYA =====================
    $akhirBulanSebelumnya = Carbon::create($tahun, $bulan, 1)->subDay();

    $pelanggansAktif = Pelanggan::with('package')
->whereDate('tanggal_register', '<', $awalBulanSekarang)
        ->where(function($q) use ($awalBulanSekarang) {
            $q->where('is_active', 1)
              ->orWhere(function($q2) use ($awalBulanSekarang) {
                  $q2->where('activated_at', '<=', $awalBulanSekarang);
              });
        })
        ->get();

    $totalPelanggan = $pelanggansAktif->count();

    // Total biaya pelanggan aktif bulan sebelumnya
    $totalBiaya = $pelanggansAktif->sum(function($pelanggan){
        $hargaPaket = $pelanggan->package->harga ?? 0;
        $biaya1 = $pelanggan->biaya_tambahan_1 ?? 0;
        $biaya2 = $pelanggan->biaya_tambahan_2 ?? 0;
        $diskon = $pelanggan->diskon ?? 0;
        $total = $hargaPaket + $biaya1 + $biaya2;
        return $total - ($total * $diskon / 100);
    });

    // ===================== PELANGGAN BARU BULAN TERPILIH =====================
$pelangganBaruCollection = Pelanggan::with('package')
    ->where('is_active', 1)
    ->whereMonth('created_at', $bulanSekarang)
    ->whereYear('created_at', $tahunSekarang)
    ->get();

$pelangganBaru = $pelangganBaruCollection->count();

$totalTagihanBaru = $pelangganBaruCollection->sum(function($pelanggan){
    $harga = $pelanggan->package->harga ?? 0;
    $b1 = $pelanggan->biaya_tambahan_1 ?? 0;
    $b2 = $pelanggan->biaya_tambahan_2 ?? 0;
    $diskon = $pelanggan->diskon ?? 0;
    $total = $harga + $b1 + $b2;
    return $total - ($total * $diskon / 100);
});


    // ===================== PELANGGAN BERHENTI =====================
    $pelangganBerhentiData = Pelanggan::with('package')
        ->where('is_active', 0)
        ->get();

    $pelangganBerhenti = $pelangganBerhentiData->count();

    $totalBerhenti = $pelangganBerhentiData->sum(function($pelanggan){
        $hargaPaket = $pelanggan->package->harga ?? 0;
        $biaya1 = $pelanggan->biaya_tambahan_1 ?? 0;
        $biaya2 = $pelanggan->biaya_tambahan_2 ?? 0;
        $diskon = $pelanggan->diskon ?? 0;
        $total = $hargaPaket + $biaya1 + $biaya2;
        return $total - ($total * $diskon / 100);
    });

    // ===================== TAGIHAN BULAN TERPILIH =====================
    $tagihanBelumBayarCollection = Tagihan::where('status', 'belum_lunas')
        ->whereYear('bulan', $tahun)
        ->whereMonth('bulan', $bulan)
        ->with('pelanggan.package')
        ->get();

    $tagihanBelumBayar = $tagihanBelumBayarCollection->count();

    $totalBelumBayar = $tagihanBelumBayarCollection->sum(function($tagihan){
        $p = $tagihan->pelanggan;
        $harga = $p->package->harga ?? 0;
        $b1 = $p->biaya_tambahan_1 ?? 0;
        $b2 = $p->biaya_tambahan_2 ?? 0;
        $diskon = $p->diskon ?? 0;
        $total = $harga + $b1 + $b2;
        return $total - ($total * $diskon / 100);
    });

    // ===================== TAGIHAN LUNAS BULAN TERPILIH =====================
    $tagihanLunasCollection = Tagihan::where('status', 'lunas')
        ->whereYear('bulan', $tahun)
        ->whereMonth('bulan', $bulan)
        ->with('pelanggan.package')
        ->get();

    $pelanggansLunas = $tagihanLunasCollection->groupBy('pelanggan_id')->map(function($g){
        $p = $g->first()->pelanggan;
        $harga = $p->package->harga ?? 0;
        $b1 = $p->biaya_tambahan_1 ?? 0;
        $b2 = $p->biaya_tambahan_2 ?? 0;
        $diskon = $p->diskon ?? 0;
        $total = $harga + $b1 + $b2;
        $p->total_tagihan_bulan_ini = $total - ($total * $diskon / 100);
        return $p;
    });

    $totalPelangganLunas = $pelanggansLunas->count();
    $totalBayarLunas = $pelanggansLunas->sum('total_tagihan_bulan_ini');

    // ===================== TRANSAKSI CASH =====================
    $tagihanCashCollection = Tagihan::where('status', 'lunas')
        ->where('metode_bayar', 'cash')
        ->whereYear('bulan', $tahun)
        ->whereMonth('bulan', $bulan)
        ->with('pelanggan.package')
        ->get();

    $pelanggansCash = $tagihanCashCollection->groupBy('pelanggan_id')->map(function($g){
        $p = $g->first()->pelanggan;
        $harga = $p->package->harga ?? 0;
        $b1 = $p->biaya_tambahan_1 ?? 0;
        $b2 = $p->biaya_tambahan_2 ?? 0;
        $diskon = $p->diskon ?? 0;
        $total = $harga + $b1 + $b2;
        $p->total_tagihan_cash = $total - ($total * $diskon / 100);
        return $p;
    });

    $transaksiCash = $pelanggansCash->count();
    $totalCash = $pelanggansCash->sum('total_tagihan_cash');

    // ===================== TRANSAKSI ONLINE =====================
    $tagihanOnlineCollection = Tagihan::where('status', 'lunas')
        ->where('metode_bayar', '!=', 'cash')
        ->whereYear('bulan', $tahun)
        ->whereMonth('bulan', $bulan)
        ->with('pelanggan.package')
        ->get();

    $pelanggansOnline = $tagihanOnlineCollection->groupBy('pelanggan_id')->map(function($g){
        $p = $g->first()->pelanggan;
        $harga = $p->package->harga ?? 0;
        $b1 = $p->biaya_tambahan_1 ?? 0;
        $b2 = $p->biaya_tambahan_2 ?? 0;
        $diskon = $p->diskon ?? 0;
        $total = $harga + $b1 + $b2;
        $p->total_tagihan_online = $total - ($total * $diskon / 100);
        return $p;
    });

    $transaksiOnline = $pelanggansOnline->count();
    $totalOnline = $pelanggansOnline->sum('total_tagihan_online');

    // ===================== PELANGGAN NUNGGAK =====================
    $pelangganNunggak = $tagihanBelumBayar;

    // ===================== PELANGGAN TELAT =====================
    $pembayaranTelat = Tagihan::where('status', 'belum_lunas')
        ->whereYear('bulan', $tahun)
        ->whereMonth('bulan', $bulan)
        ->get()
        ->filter(function($tagihan){
            $akhirBulan = Carbon::parse($tagihan->bulan)->endOfMonth();
            return now()->gt($akhirBulan);
        })
        ->count();

    // ===================== TOTAL BAYAR LUNAS SELURUH =====================
    $totalBayarLunasAll = Tagihan::where('status', 'lunas')
        ->with('pelanggan.package')
        ->get()
        ->sum(function($tagihan){
            $p = $tagihan->pelanggan;
            if (!$p) return 0;
            $harga = $p->package->harga ?? 0;
            $b1 = $p->biaya_tambahan_1 ?? 0;
            $b2 = $p->biaya_tambahan_2 ?? 0;
            $diskon = $p->diskon ?? 0;
            $total = $harga + $b1 + $b2;
            return $total - ($total * $diskon / 100);
        });

    $totalPembayaranSemuaPelanggan = Transaksi::whereIn('status', ['lunas', 'Lunas', 'paid', 'success'])
        ->sum('jumlah');

    // ===================== RETURN VIEW =====================
    return view('admin.pelanggan', [
        'pelanggans' => $pelanggansAktif,
        'totalPelanggan' => $totalPelanggan,
        'totalBiaya' => $totalBiaya,
        'pelangganBaru' => $pelangganBaru,
        'pelangganBerhenti' => $pelangganBerhenti,
        'tagihanBelumBayar' => $tagihanBelumBayar,
        'totalBelumBayar' => $totalBelumBayar,
        'totalPelangganLunas' => $totalPelangganLunas,
        'totalBayarLunas' => $totalBayarLunas,
        'transaksiCash' => $transaksiCash,
        'totalCash' => $totalCash,
        'transaksiOnline' => $transaksiOnline,
        'totalOnline' => $totalOnline,
        'pelangganNunggak' => $pelangganNunggak,
        'bulan' => $bulan,
        'tahun' => $tahun,
        'totalBayarLunasAll' => $totalBayarLunasAll,
        'totalTagihanBaru' => $totalTagihanBaru,
        'totalPembayaranSemuaPelanggan' => $totalPembayaranSemuaPelanggan,
    ]);
}


        
    // ================= STEP 1: FORM IDENTITAS =================
    public function create()
    {
        $pakets = Package::all();
        $areas = Area::all();
        return view('admin.create_pelanggan', compact('pakets', 'areas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'paket_id' => 'required|exists:packages,id',
            'tanggal_register' => 'required|date',
            'tanggal_tagihan' => 'required|date',
            'tanggal_isolir' => 'nullable|date',
            'area_id' => 'required|exists:areas,id',
            'nama_biaya_1' => 'nullable|string|max:255',
            'biaya_tambahan_1' => 'nullable|numeric',
            'nama_biaya_2' => 'nullable|string|max:255',
            'biaya_tambahan_2' => 'nullable|numeric',
            'diskon' => 'nullable|numeric|min:0|max:100', // diskon %
        ]);

        session(['pelanggan_data' => $request->only([
            'name', 'phone', 'paket_id', 'tanggal_register', 'tanggal_tagihan',
            'tanggal_isolir', 'area_id', 'nama_biaya_1', 'biaya_tambahan_1',
            'nama_biaya_2', 'biaya_tambahan_2', 'diskon'
        ])]);

        return redirect()->route('admin.modem');
    }

    // ================= STEP 2: FORM MODEM =================
    public function modem()
    {
        $devices = Device::all();
        return view('admin.modem', compact('devices'));
    }

    public function storeModem(Request $request)
    {
        $request->validate([
            'device_id' => 'nullable|exists:devices,id'
        ]);

        $pelangganData = session('pelanggan_data', []);
        $pelangganData['device_id'] = $request->device_id ?? null;

        session(['pelanggan_data' => $pelangganData]);

        return redirect()->route('admin.koordinat');
    }

    // ================= STEP 3: FORM KOORDINAT & ODP =================
    public function koordinat()
    {
        $odp = ODP::all();
        return view('admin.koordinat', compact('odp'));
    }

    public function simpan(Request $request)
    {
        $request->validate([
            'kode_odp' => 'required|string|exists:odp,kode',
            'koordinat' => 'required|string',
        ]);

        $pelangganData = session('pelanggan_data');

        if (!$pelangganData) {
            return redirect()->route('pelanggan.create')
                ->with('error', 'Data pelanggan belum lengkap. Silakan isi dari awal.');
        }

        $odp = ODP::where('kode', $request->kode_odp)->first();

        $koordinat = explode(',', $request->koordinat);
        $latitude = trim($koordinat[0] ?? null);
        $longitude = trim($koordinat[1] ?? null);

        $latitude = $latitude === '' ? null : $latitude;
        $longitude = $longitude === '' ? null : $longitude;

        $pelangganData['odp_id'] = $odp->id ?? null;
        $pelangganData['latitude'] = $latitude;
        $pelangganData['longitude'] = $longitude;
        $pelangganData['status'] = 'belum_lunas';
        $pelangganData['is_active'] = 1;

        $pelanggan = Pelanggan::create($pelangganData);

        // tetap buat tagihan bulan pertama seperti sebelumnya
        // $this->buatTagihan($pelanggan);

        // ----- PENTING: generate tagihan jangka panjang tanpa menghapus logic lama -----
        // panggil TagihanController.generateOnRegister agar tagihan jangka panjang dibuat
        app(TagihanController::class)->generateOnRegister($pelanggan);
        // -------------------------------------------------------------------------------

        session()->forget('pelanggan_data');

        return redirect()->route('pelanggan.index')
            ->with('success', 'Pelanggan baru berhasil disimpan dan tagihan pertama dibuat.');
    }

    // ================= STEP 4: DETAIL PELANGGAN =================
public function show($id)
{
    $pelanggan = Pelanggan::with(['package', 'area', 'device', 'odp', 'tagihan'])->findOrFail($id);

    $areas = Area::all();
    $packages = Package::all();
    $devices = Device::all();
    $odps = ODP::all();

    $hargaPaket = $pelanggan->package->harga ?? 0;
    $biaya1 = $pelanggan->biaya_tambahan_1 ?? 0;
    $biaya2 = $pelanggan->biaya_tambahan_2 ?? 0;
    $diskon = $pelanggan->diskon ?? 0;

    $total = $hargaPaket + $biaya1 + $biaya2;
    $pelanggan->tagihan_terakhir = $total - ($total * $diskon / 100);

    // Ambil template WA dari database
    $templateWA = Setting::where('key_name', 'template_wa_belum_bayar')->value('value');
dd($templateWA, $pelanggan->phone);

    return view('admin.detail_pelanggan', compact(
        'pelanggan', 'areas', 'packages', 'devices', 'odps', 'templateWA'
    ));
}



    // ================= FILTER DATA =================
    public function pelangganBelumBayar()
    {
        $bulanIni = now()->format('Y-m-01');

        $tagihans = Tagihan::with(['pelanggan.package', 'pelanggan.area', 'pelanggan.device'])
            ->where('status', 'belum_lunas')
            ->where('bulan', $bulanIni)
            ->get();

        $pelanggans = $tagihans->map(function($tagihan) {
            $pelanggan = $tagihan->pelanggan;
            $hargaPaket = $pelanggan->package->harga ?? 0;
            $biaya1 = $pelanggan->biaya_tambahan_1 ?? 0;
            $biaya2 = $pelanggan->biaya_tambahan_2 ?? 0;
            $diskon = $pelanggan->diskon ?? 0;

            $pelanggan->tagihan_terakhir = ($hargaPaket + $biaya1 + $biaya2) - (($hargaPaket + $biaya1 + $biaya2) * $diskon / 100);
            $pelanggan->bulan_terakhir = $tagihan->bulan;

            // Buat array tagihan per bulan (ambil via relasi supaya aman)
            $tagihanBulan = [];
            foreach ($pelanggan->tagihan()->get() as $t) {
                $bulan = \Carbon\Carbon::parse($t->bulan)->locale('id')->translatedFormat('F');
                $tagihanBulan[$bulan] = [
                    'id' => $t->id,
                    'status' => $t->status,
                    'jumlah' => $t->jumlah,
                ];
            }
            $pelanggan->tagihanBulan = $tagihanBulan;

            return $pelanggan;
        });

        $tagihanBelumBayar = $pelanggans->count();
        $totalBelumBayar = $pelanggans->sum(fn($p) => $p->tagihan_terakhir);

        return view('admin.belum_bayar', compact('pelanggans', 'tagihanBelumBayar', 'totalBelumBayar'));
    }

public function pelangganLunas(Request $request)
{
    $from = $request->from;
    $to   = $request->to;

    // Ambil hanya pelanggan aktif yang punya TAGIHAN LUNAS
    $pelanggans = Pelanggan::where('is_active', 1) // Hanya pelanggan aktif
        ->whereHas('tagihan', function($q) use ($from, $to){
            $q->where('status', 'lunas');

            // Filter tanggal bayar dari TAGIHAN
            if ($from) {
                $q->whereDate('tanggal_bayar', '>=', $from);
            }

            if ($to) {
                $q->whereDate('tanggal_bayar', '<=', $to);
            }

        })
        ->with([
            'area',
            'device',
            'tagihan' => function($q){
                $q->where('status', 'lunas');  
            }
        ])
        ->get();

    // Total bayar = SUM jumlah tagihan lunas
    foreach ($pelanggans as $p) {
        $p->total_tagihan_lunas = $p->tagihan->sum('jumlah');

        // Tambahkan array pembayar untuk setiap tagihan
        $p->pembayar = $p->tagihan->map(function($t){
            return $t->dibayar_oleh ?? '-';
        });
    }

    return view('admin.pelanggan_lunas', compact('pelanggans'));
}



public function pelangganSemua()
{
    $bulan = now()->month;
    $tahun = now()->year;

    $pelanggans = Pelanggan::with(['area','device','package'])
        ->where('is_active', 1)
        ->where(function ($q) use ($bulan, $tahun) {
            // Ambil pelanggan yang BUKAN bulan ini & tahun ini
            $q->whereYear('tanggal_register', '!=', $tahun)
              ->orWhereMonth('tanggal_register', '!=', $bulan);
        })
        ->get();

    return view('admin.pelanggan_semua', compact('pelanggans'));
}




 public function belumBayar(Request $request)
{
    $query = Pelanggan::with(['area', 'device', 'package'])
        ->where('status', 'belum_lunas')
        ->where('is_active', 1); // <--- Hanya yang aktif

    if ($request->q) {
        $query->where(function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->q . '%')
              ->orWhere('phone', 'like', '%' . $request->q . '%')
              ->orWhereHas('area', function ($q2) use ($request) {
                  $q2->where('nama_area', 'like', '%' . $request->q . '%');
              });
        });
    }

    $pelanggans = $query->get();

    foreach ($pelanggans as $pelanggan) {
        $hargaPaket = $pelanggan->package->harga ?? 0;
        $biaya1 = $pelanggan->biaya_tambahan_1 ?? 0;
        $biaya2 = $pelanggan->biaya_tambahan_2 ?? 0;
        $diskon = $pelanggan->diskon ?? 0;

        $pelanggan->tagihan_terakhir = ($hargaPaket + $biaya1 + $biaya2) - 
            (($hargaPaket + $biaya1 + $biaya2) * $diskon / 100);
        $pelanggan->bulan_terakhir = $pelanggan->tagihan()->latest('bulan')->value('bulan') ?? null;
    }

    $totalBelumBayar = $pelanggans->sum(fn($p) => $p->tagihan_terakhir);
    $tagihanBelumBayar = $pelanggans->count();

    return view('admin.belum_bayar', compact('pelanggans', 'totalBelumBayar', 'tagihanBelumBayar'));
}


    // ================= DETAIL =================
public function detail($id)
{
    $pelanggan = Pelanggan::with(['package', 'area', 'device', 'odp', 'tagihan'])->findOrFail($id);

    $areas = Area::all();
    $packages = Package::all();
    $devices = Device::all();
    $odps = ODP::all();

    $hargaPaket = $pelanggan->package->harga ?? 0;
    $biaya1 = $pelanggan->biaya_tambahan_1 ?? 0;
    $biaya2 = $pelanggan->biaya_tambahan_2 ?? 0;
    $diskon = $pelanggan->diskon ?? 0;

    $total = $hargaPaket + $biaya1 + $biaya2;
    $pelanggan->tagihan_terakhir = $total - ($total * $diskon / 100);

    $pelanggan->status_tagihan = $pelanggan->tagihan()
        ->latest('bulan')
        ->value('status') ?? 'belum bayar';

    return view('admin.detail_pelanggan', compact(
        'pelanggan', 'areas', 'packages', 'devices', 'odps'
    ));
}



    protected function generateTagihanOtomatis(Pelanggan $pelanggan)
    {
        $tglRegister = Carbon::parse($pelanggan->tanggal_register)->startOfMonth();
        $tglSekarang = now()->startOfMonth();

        // Ambil bulan terakhir yang ada di DB
        $lastTagihan = $pelanggan->tagihan()->orderByDesc('bulan')->first();
        $mulai = $lastTagihan ? Carbon::parse($lastTagihan->bulan)->addMonth() : $tglRegister;

        // Loop dari bulan terakhir + 1 sampai bulan sekarang
        while ($mulai <= $tglSekarang) {
            $bulanKey = $mulai->format('Y-m-01');

            if (!$pelanggan->tagihan()->where('bulan', $bulanKey)->exists()) {
                $pelanggan->tagihan()->create([
                    'bulan' => $bulanKey,
                    'jumlah' => (optional($pelanggan->package)->harga ?: 0)
                               + ($pelanggan->biaya_tambahan_1 ?: 0)
                               + ($pelanggan->biaya_tambahan_2 ?: 0)
                               - (((optional($pelanggan->package)->harga ?: 0)
                                  + ($pelanggan->biaya_tambahan_1 ?: 0)
                                  + ($pelanggan->biaya_tambahan_2 ?: 0))
                                  * ($pelanggan->diskon ?: 0) / 100),
                    'status' => 'belum'
                ]);
            }

            $mulai->addMonth();
        }
    }

public function detailLunas(Request $request, $id)
{
    // Ambil pelanggan
    $pelanggan = Pelanggan::with(['area', 'device', 'package'])->findOrFail($id);

    // Ambil transaksi lunas
    $transaksiQuery = Transaksi::where('pelanggan_id', $id)
        ->where('status', 'lunas');

    // ================= FILTER TANGGAL =================
    if ($request->filled('from')) {
        $from = \Carbon\Carbon::parse($request->from)->startOfDay();
        $transaksiQuery->where('tanggal_bayar', '>=', $from);
    }

    if ($request->filled('to')) {
        $to = \Carbon\Carbon::parse($request->to)->endOfDay();
        $transaksiQuery->where('tanggal_bayar', '<=', $to);
    }

    // ================= FILTER BULAN =================
    if ($request->filled('bulan_dari')) {
        $bulanDari = str_pad($request->bulan_dari, 2, '0', STR_PAD_LEFT);
        $transaksiQuery->whereMonth('tanggal_bayar', '>=', $bulanDari);
    }

    if ($request->filled('bulan_sampai')) {
        $bulanSampai = str_pad($request->bulan_sampai, 2, '0', STR_PAD_LEFT);
        $transaksiQuery->whereMonth('tanggal_bayar', '<=', $bulanSampai);
    }

    // ================= FILTER PAKET =================
    if ($request->filled('package_id')) {
        $transaksiQuery->whereHas('pelanggan', function($q) use ($request) {
            $q->where('package_id', $request->package_id);
        });
    }

    // Semua transaksi untuk ditampilkan di modal
    $transaksi = $transaksiQuery->orderBy('tanggal_bayar', 'DESC')->get();

    // Ambil transaksi terbaru untuk WA
    $tLunas = $transaksi->first();

    // ================= TEMPLATE DEFAULT =================
    $templateSetting = \App\Models\Setting::where('key', 'wa_template_lunas')->first();
    $templateLunas = $templateSetting ? $templateSetting->value : '';

    // ================= REPLACE PLACEHOLDERS =================
    if ($tLunas) {

        // Format bulan (bisa banyak)
        $bulanList = explode(',', $tLunas->bulan);
        $bulanFormatted = collect($bulanList)->map(function ($b) {
            return \Carbon\Carbon::parse($b.'-01')->translatedFormat('F Y');
        })->implode(', ');

        $pesan = strtr($templateLunas, [
            '{nama}'           => $pelanggan->name,
            '{nomor}'          => preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $pelanggan->phone)),
            '{area}'           => $pelanggan->area->nama_area ?? '-',
            '{paket}'          => $pelanggan->package->nama ?? '-',
            '{bulan}'          => $bulanFormatted,
            '{jumlah}'         => 'Rp ' . number_format($tLunas->jumlah ?? 0, 0, ',', '.'),
            '{tanggal_bayar}'  => $tLunas->tanggal_bayar ? \Carbon\Carbon::parse($tLunas->tanggal_bayar)->format('d-m-Y') : '-',
            '{jam_bayar}'      => $tLunas->tanggal_bayar ? \Carbon\Carbon::parse($tLunas->tanggal_bayar)->format('H:i') : '-',
        ]);
    } else {
        // Kalau belum ada transaksi lunas
        $pesan = strtr($templateLunas, [
            '{nama}' => $pelanggan->name,
            '{nomor}' => $pelanggan->phone ?? '-',
            '{area}' => $pelanggan->area->nama_area ?? '-',
            '{paket}' => $pelanggan->package->nama ?? '-',
            '{bulan}' => '-',
            '{jumlah}' => '0',
            '{tanggal_bayar}' => '-',
            '{jam_bayar}' => '-',
        ]);
    }

    // ================= FORMAT RIWAYAT UNTUK MODAL =================
    $riwayat = $transaksi->map(function ($t) {
        $bulanList = explode(',', $t->bulan);

        $bulanFormatted = collect($bulanList)->map(function ($b) {
            return \Carbon\Carbon::parse($b.'-01')->translatedFormat('F Y');
        })->implode(', ');

        return [
            'bulan'   => $bulanFormatted,
            'jumlah'  => 'Rp ' . number_format($t->jumlah ?? 0, 0, ',', '.'),
            'tanggal' => $t->tanggal_bayar
                            ? \Carbon\Carbon::parse($t->tanggal_bayar)->format('d-m-Y')
                            : '-',
            'waktu'   => $t->tanggal_bayar
                            ? \Carbon\Carbon::parse($t->tanggal_bayar)->format('H:i')
                            : '-',
            'dibayar_oleh' => $t->dibayar_oleh ?? '-',
        ];
    });

    return response()->json([
        'pelanggan' => [
            'nama' => $pelanggan->name,
            'phone' => $pelanggan->phone ?? '-',
            'area' => $pelanggan->area->nama_area ?? '-',
            'ip' => $pelanggan->device->ip_address ?? '-',
            'paket' => $pelanggan->package->nama ?? '-',
        ],

        'riwayat' => $riwayat,

        // Template yang sudah direplace akan dipakai langsung oleh tombol WA
        'pesan_lunas' => $pesan,

        // Tetap kirim template asli (kalau user ingin edit)
        'template_lunas' => $templateLunas,
    ]);
}





    // private function buatTagihan($pelanggan)
    // {
    //     $bulanIni = now()->format('Y-m-01');

    //     $hargaPaket = $pelanggan->package->harga ?? 0;
    //     $biaya1 = $pelanggan->biaya_tambahan_1 ?? 0;
    //     $biaya2 = $pelanggan->biaya_tambahan_2 ?? 0;
    //     $diskon = $pelanggan->diskon ?? 0;

    //     $total = $hargaPaket + $biaya1 + $biaya2;
    //     $jumlah = $total - ($total * $diskon / 100);

    //     Tagihan::create([
    //         'pelanggan_id' => $pelanggan->id,
    //         'bulan' => $bulanIni,
    //         'jumlah' => $jumlah,
    //         'status' => 'belum_lunas',
    //         'tanggal_tagihan' => now(),
    //         'created_at' => now(),
    //         'updated_at' => now(),
    //     ]);
    // }

public function generateTagihanBulanIni()
{
    $bulanIni = Carbon::now()->format('Y-m-01');

    // Ambil semua pelanggan aktif
    $pelanggans = Pelanggan::where('is_active', 1)->get();

    foreach ($pelanggans as $pelanggan) {

        // Cek apakah tagihan bulan ini sudah ada
        $exists = Tagihan::where('pelanggan_id', $pelanggan->id)
            ->where('bulan', $bulanIni)
            ->exists();

        if (!$exists) {
            app(\App\Http\Controllers\Admin\TagihanController::class)
                ->createTagihanIfNotExist($pelanggan, Carbon::now()->format('Y-m'));
        }   
    }

    return "Tagihan bulan ini berhasil dibuat tanpa duplikasi.";
}




    public function cash()
    {
        $transaksiCash = Transaksi::with([
            'pelanggan.package',
            'pelanggan.area'
        ])
        ->where('metode', 'cash')
        ->where('status', 'lunas')
        ->orderBy('tanggal_bayar', 'desc')
        ->get();

        // Tambahkan total per transaksi dengan diskon
        foreach($transaksiCash as $transaksi) {
            $pelanggan = $transaksi->pelanggan;
            $hargaPaket = $pelanggan->package->harga ?? 0;
            $biaya1 = $pelanggan->biaya_tambahan_1 ?? 0;
            $biaya2 = $pelanggan->biaya_tambahan_2 ?? 0;
            $diskon = $pelanggan->diskon ?? 0;
            $transaksi->total = ($hargaPaket + $biaya1 + $biaya2) - (($hargaPaket + $biaya1 + $biaya2) * $diskon / 100);
        }

        return view('admin.transaksi_cash', compact('transaksiCash'));
    }

    public function nunggak()
    {
        $bulanLalu = Carbon::now()->subMonth();

        $pelangganNunggak = Tagihan::whereYear('bulan', $bulanLalu->year)
            ->whereMonth('bulan', $bulanLalu->month)
            ->where('status', 'belum_lunas')
            ->with('pelanggan')
            ->get();

        return view('admin.pelanggan_nunggak', compact('pelangganNunggak'));
    }

    public function telat()
    {
        $pelangganTelat = Tagihan::where('status', 'lunas')
            ->whereDate('tanggal_bayar', '>', Carbon::now()->startOfMonth()->addDays(5))
            ->with('pelanggan.package')
            ->get();

        return view('admin.pelanggan_telat', compact('pelangganTelat'));
    }

   public function transaksiCash(Request $request)
{
    // Ambil bulan dari query param (format input type="month" => "YYYY-MM"), default sekarang
    $bulan = $request->get('bulan', now()->format('Y-m'));

    // normalisasi ke format stored di tagihan (YYYY-MM-01)
    $bulanDb = Carbon::parse($bulan . '-01')->format('Y-m-01');

    // Ambil semua tagihan LUNAS dengan metode cash untuk bulan tersebut
    $transaksiCash = Tagihan::where('status', 'lunas')
        ->where('metode_bayar', 'cash')
        ->where('bulan', $bulanDb)
        ->with(['pelanggan.package', 'pelanggan.area', 'pelanggan.device'])
        ->orderBy('tanggal_bayar', 'desc')
        ->get();

    // Hitung total pemasukan bulan tersebut — gunakan jumlah dari tagihan jika tersedia
    $totalPemasukan = $transaksiCash->sum(function($trx) {
        // pakai nilai jumlah di tagihan bila ada, fallback kalkulasi paket jika perlu
        if (!empty($trx->jumlah)) {
            return (float) $trx->jumlah;
        }

        $p = $trx->pelanggan;
        $hargaPaket = $p->package->harga ?? 0;
        $b1 = $p->biaya_tambahan_1 ?? 0;
        $b2 = $p->biaya_tambahan_2 ?? 0;
        $diskon = $p->diskon ?? 0;
        $total = ($hargaPaket + $b1 + $b2) - (($hargaPaket + $b1 + $b2) * $diskon / 100);
        return $total;
    });

    return view('admin.transaksi_cash', compact('transaksiCash', 'bulan', 'totalPemasukan'));
}



    public function updateStatus(Request $request, $id)
    {
        $tagihan = Tagihan::findOrFail($id);

        $statusLama = $tagihan->status;
        $statusBaru = $request->status;

        // Update status tagihan
        $tagihan->status = $statusBaru;
        $tagihan->tanggal_bayar = $statusBaru == 'lunas' ? now() : null;
        $tagihan->save();

        // Jika status berubah menjadi lunas dan transaksi belum ada
        if ($statusLama !== 'lunas' && $statusBaru === 'lunas') {

            // Cek apakah transaksi sudah dibuat sebelumnya
            $cek = Transaksi::where('bulan', $tagihan->bulan)
                            ->where('pelanggan_id', $tagihan->pelanggan_id)
                            ->first();

            if (!$cek) {

                Transaksi::create([
                    'pelanggan_id'   => $tagihan->pelanggan_id,
                    'kode_transaksi' => 'TRX-' . time(),
                    'jumlah'         => $tagihan->jumlah,
                    'diskon'         => $tagihan->diskon,
                    'metode'         => $tagihan->metode_bayar, // dari tagihan
                    'status'         => 'lunas',
                    'dibayar_oleh'   => auth()->user()->name ?? 'system',
                    'bulan'          => $tagihan->bulan,
                    'tanggal_bayar'  => now(),
                    'ip_address'     => $request->ip(),
                ]);

            }
        }

        return back()->with('success', 'Status tagihan berhasil diperbarui.');
    }

public function detail_belum_bayar($id)
{        
    $pelanggan = Pelanggan::with(['tagihan' => function($q){
        $q->where('status', 'belum_lunas')->latest();
    }])->findOrFail($id);

    $tagihan = $pelanggan->tagihan->first();

    // ===========================
    // AMBIL TEMPLATE DEFAULT 
    // ===========================
    $template = \App\Models\Setting::where('key', 'wa_template_belum_lunas')->first();
    $pesanTemplate = $template ? $template->value : '';

    return view('admin.detail_belum_bayar', compact('pelanggan', 'tagihan', 'pesanTemplate'));
}

    
public function update(Request $request, $id)
{
    $pelanggan = Pelanggan::findOrFail($id);

    $pelanggan->update([
        'name' => $request->name,
        'phone' => $request->phone,
        'area_id' => $request->area_id,
        'package_id' => $request->package_id,
        'odp_id' => $request->odp_id,
        'is_active' => $request->is_active,  // <--- ini yg kita cek
    ]);

    // === Jika status Berhenti, arahkan ke halaman pelanggan berhenti ===
    if ($request->is_active == 0) {
        return redirect()->route('admin.pelanggan_berhenti')
                         ->with('success', 'Pelanggan dipindahkan ke daftar berhenti.');
    }

    // === Kalau tetap aktif, balik ke detail normal ===
    return redirect()->route('admin.pelanggan_semua')
                     ->with('success', 'Data pelanggan berhasil diperbarui.');
}
public function berhenti()
{
    $pelanggan = Pelanggan::with(['area', 'package'])
        ->where('is_active', 0)
        ->orderByDesc('updated_at') // pelanggan yg baru berhenti muncul di atas
        ->get();

    return view('admin.pelanggan_berhenti', compact('pelanggan'));
}

public function pelangganBaru()
{
    $bulan = now()->month;
    $tahun = now()->year;

    $pelanggan = Pelanggan::with(['area','device','package','tagihan'])
        ->where('is_active', 1)
        ->whereYear('tanggal_register', $tahun)
        ->whereMonth('tanggal_register', $bulan)
        ->get();

    // jumlah pelanggan baru (unik berdasarkan id)
    $pelangganBaru = $pelanggan->unique('id')->count();

    // total tagihan lunas dari pelanggan baru
    $totalTagihanLunas = $pelanggan->sum(function($p){
        return $p->tagihan->where('status', 'lunas')->sum('jumlah');
    });

    return view('admin.pelanggan_baru', compact('pelanggan', 'pelangganBaru', 'totalTagihanLunas'));
}






public function aktifkanUlang($id)
{
    $pelanggan = Pelanggan::findOrFail($id);

    // Aktifkan kembali pelanggan
    $pelanggan->is_active = 1;

    // Simpan tanggal aktivasi ulang
    $pelanggan->tanggal_aktivasi = now();

    // Perbarui tanggal register agar pelanggan masuk kategori "pelanggan baru"
    $pelanggan->tanggal_register = now();
        $pelanggan->activated_at = now(); // <-- kolom baru untuk mencatat kapan diaktifkan ulang


    $pelanggan->save();

    return redirect()
        ->back()
        ->with('success', 'Pelanggan berhasil diaktifkan kembali.');
}


 public function indexLunas(Request $request)
    {
        $query = Pelanggan::with(['area', 'device', 'package', 'tagihan']);

        // Filter area
        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        // Filter paket
        if ($request->filled('package_id')) {
            $query->where('package_id', $request->package_id);
        }

        // Filter tanggal pembayaran
        if ($request->filled('from') || $request->filled('to')) {
            $query->whereHas('tagihan', function($q) use ($request) {
                $q->where('status', 'lunas');

                if ($request->filled('from')) {
                    $q->whereDate('tanggal_bayar', '>=', $request->from);
                }
                if ($request->filled('to')) {
                    $q->whereDate('tanggal_bayar', '<=', $request->to);
                }
            });
        }

        // Filter bulan
        if ($request->filled('bulan_dari')) {
            $query->whereHas('tagihan', function($q) use ($request) {
                $q->whereMonth('bulan', '>=', $request->bulan_dari);
            });
        }
        if ($request->filled('bulan_sampai')) {
            $query->whereHas('tagihan', function($q) use ($request) {
                $q->whereMonth('bulan', '<=', $request->bulan_sampai);
            });
        }

        $pelanggans = $query->get();

        // Ambil semua area dan paket untuk filter
        $areas = Area::all();
        $packages = Package::all();

        return view('admin.pelanggan.lunas', compact('pelanggans', 'areas', 'packages'));
    }
public function detailJson($id)
{
    $p = Pelanggan::with(['area','package','device','odp','transaksi'])
        ->findOrFail($id);

    return response()->json([
        'name' => $p->name,
        'phone' => $p->phone,
        'area' => $p->area->nama_area ?? '-',
        'ip' => $p->device->ip_address ?? '-',
        'paket' => $p->package->nama_paket ?? '-',
        'tagihan' => number_format($p->tagihan_terakhir ?? 0,0,',','.'),

        'riwayat' => $p->transaksi
            ->where('status','lunas')
            ->map(function($t){
                $formattedBulan = collect(explode(',', $t->bulan))
                    ->map(function($b){
                        try {
                            return \Carbon\Carbon::createFromFormat('Y-m', trim($b))
                                ->translatedFormat('M Y');
                        } catch (\Exception $e) {
                            return trim($b);
                        }
                    })->implode(', ');

                return [
                    'bulan' => $formattedBulan,
                    'metode' => ucfirst($t->metode),
                    'dibayar_oleh' => $t->dibayar_oleh,
                    'total' => number_format($t->jumlah, 0, ',', '.'),
                ];
            })->values()
    ]);
}
 public function detailBaru($id)
{
    // Ambil data pelanggan baru / pelanggan aktif
    $pelanggan = Pelanggan::with(['area', 'package', 'device', 'odp', 'tagihan'])
                          ->where('is_active', 1) // pastikan aktif
                          ->findOrFail($id);

    // Filter tagihan yang sudah dibayar / lunas
    $tagihanLunas = $pelanggan->tagihan->where('status', 'lunas');

    // Hitung total tagihan yang sudah dibayar
    $pelanggan->total_tagihan_lunas = $tagihanLunas->sum('jumlah');

    // Ambil tagihan terakhir yang sudah dibayar
    $lastTagihan = $tagihanLunas->sortByDesc('bulan')->first();
    $pelanggan->tagihan_terakhir = $lastTagihan ? $lastTagihan->jumlah : 0;

    // Data referensi untuk form
    $areas = Area::all();
    $packages = Package::all();
    $odps = ODP::all();

    return view('admin.detail_pelanggan_baru', compact('pelanggan', 'areas', 'packages', 'odps'));
}


}