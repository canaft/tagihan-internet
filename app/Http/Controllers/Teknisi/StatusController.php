<?php

namespace App\Http\Controllers\Teknisi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengaduan;

class StatusController extends Controller
{
       public function index()
    {
        $pengaduan = Pengaduan::whereIn('status', ['Menunggu Dikerjakan', 'Sedang Dikerjakan'])->get();
        return view('kangteknisi.status.index', compact('pengaduan'));
    }

    public function update(Request $request, $id)
    {
        $pengaduan = Pengaduan::findOrFail($id);
        $pengaduan->status = $request->status;
        $pengaduan->tanggal_update = now();
        $pengaduan->save();

        return back()->with('success', 'Status perbaikan berhasil diperbarui!');
    }
}

