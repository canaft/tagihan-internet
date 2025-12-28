<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Pelanggan;
use Illuminate\Http\Request;

class SettingController extends Controller
{

    // =============================
    // 1. HALAMAN INDEX SETTING
    // =============================
    public function index()
    {
        $templates = Setting::whereIn('category', ['belum_bayar', 'lunas'])->get();

        return view('admin.setting', compact('templates'));
    }


    // =============================
    // 2. HALAMAN TEMPLATE WA
    // =============================
    public function waTemplate()
    {
        // Ambil semua template sesuai kategori
        $templates = Setting::whereIn('category', ['belum_bayar', 'lunas'])
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.wa_template', compact('templates'));
    }


    // =============================
    // 3. SIMPAN TEMPLATE BARU
    // =============================
public function saveWaTemplate(Request $request)
{
    $request->validate([
        'template' => 'required',
        'category' => 'required|in:belum_bayar,lunas',
        'key_name' => 'nullable|string' // key_name dari form update
    ]);

    if ($request->key_name) {
        // Update template yang sudah ada
        Setting::where('key_name', $request->key_name)
            ->update([
                'value' => $request->template,
                'category' => $request->category
            ]);
        $message = 'Template berhasil diperbarui!';
    } else {
        // Tambah template baru
        Setting::create([
            'key_name' => 'template_wa_' . $request->category . '_' . time(),
            'value' => $request->template,
            'category' => $request->category,
            'is_default' => 0
        ]);
        $message = 'Template baru berhasil disimpan!';
    }

    return back()->with('success', $message);
}



    // =============================
    // 4. JADIKAN DEFAULT (GUNAKAN)
    // =============================
    public function gunakanTemplate($key)
    {
        $template = Setting::where('key_name', $key)->firstOrFail();

        // reset kategori template ini = 0 semua
        Setting::where('category', $template->category)->update([
            'is_default' => 0
        ]);

        // jadikan default
        $template->update([
            'is_default' => 1
        ]);

        return back()->with('success', 'Template berhasil dijadikan default!');
    }


    // =============================
    // 5. DETAIL BELUM BAYAR
    // =============================
    public function detail_belum_bayar($id)
    {
        $pelanggan = Pelanggan::with(['package','area'])->findOrFail($id);

        $tagihan = $pelanggan->tagihan()
            ->where('status', 'belum_lunas')
            ->latest()
            ->first();

        // template default kategori "belum_bayar"
        $templateWA = Setting::where([
            ['category', 'belum_bayar'],
            ['is_default', 1]
        ])->value('value');

        return view('admin.detail_belum_bayar', compact('pelanggan','tagihan','templateWA'));
    }


 // =============================
// 6. DETAIL LUNAS (FIX)
// =============================
public function showLunas($id)
{
    // ambil pelanggan + relasi dasar
    $pelanggan = Pelanggan::with(['package', 'area'])->findOrFail($id);

    // ambil TAGIHAN YANG SUDAH LUNAS (PALING TERAKHIR)
    $tagihan = $pelanggan->tagihan()
        ->where('status', 'lunas')
        ->latest()
        ->first();

    // template default kategori "lunas"
    $templateWA = Setting::where([
        ['category', 'lunas'],
        ['is_default', 1]
    ])->value('value');

    return view('admin.detail_lunas', compact(
        'pelanggan',
        'tagihan',
        'templateWA'
    ));
}

}
