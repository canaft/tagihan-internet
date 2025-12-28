<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ODC;
use App\Models\ODP;

class ODCController extends Controller
{


        public function index()
    {
            $odcs = ODC::all(); // ambil semua data ODC
                $odps = ODP::all(); // <--- ini yang dibutuhkan

    return view('odc.index', compact('odcs','odps'));

    }
    // Tambah ODC
public function store(Request $request)
{
    $request->validate([
        'kode' => 'required|string|max:100',
        'nama' => 'required|string|max:255',
        'lat'  => 'nullable',
        'lng'  => 'nullable',
        'info' => 'nullable',
    ]);

    ODC::create([
        'kode' => $request->kode,
        'nama' => $request->nama,
        'lat'  => $request->lat,
        'lng'  => $request->lng,
        'info' => $request->info,
    ]);

    return redirect()->back()->with('success', 'ODC berhasil ditambahkan.');
}


    // Hapus ODC
    public function destroy($id)
    {
        $odc = ODC::findOrFail($id);
        $odc->delete();

        return redirect()->back()->with('success', 'ODC berhasil dihapus.');
    }
}
