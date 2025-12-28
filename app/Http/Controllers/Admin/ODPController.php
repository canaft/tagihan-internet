<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ODP;
use App\Models\ODC;

class ODPController extends Controller
{

      public function index()
    {
            $odcs = ODC::all(); // ambil semua data ODC
                $odps = ODP::all(); // <--- ini yang dibutuhkan

    return view('odp.index', compact('odcs','odps'));

    }
    // Tambah ODP
    public function store(Request $request)
    {
        $request->validate([
            'nama_odp' => 'required|string|max:255',
            'odc_id' => 'required|exists:odcs,id',
        ]);

        ODP::create([
            'nama_odp' => $request->nama_odp,
            'odc_id' => $request->odc_id,
        ]);

        return redirect()->back()->with('success', 'ODP berhasil ditambahkan.');
    }

    // Hapus ODP
    public function destroy($id)
    {
        $odp = ODP::findOrFail($id);
        $odp->delete();

        return redirect()->back()->with('success', 'ODP berhasil dihapus.');
    }
}
