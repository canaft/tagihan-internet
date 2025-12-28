<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Http\Request;

class PengaduanController extends Controller
{
    /**
     * Tampilkan semua pengaduan (admin)
     */
    public function index()
    {
        // Ambil semua pengaduan + relasi
        $pengaduans = Pengaduan::with(['pelanggan', 'teknisi'])->latest()->paginate(20);

        // Ambil data teknisi (role teknisi)
        $teknisis = User::where('role', 'teknisi')->get();

        // Ambil semua pelanggan
        $pelanggans = Pelanggan::all();

        return view('admin.pengaduan.index', compact('pengaduans', 'teknisis', 'pelanggans'));
    }

    /**
     * Tambah pengaduan baru
     */
public function store(Request $request)
{
    $request->validate([
        'pelanggan_id'    => 'required|exists:pelanggan,id',
        'jenis_pengaduan' => 'required',
        'deskripsi'       => 'required|string',
        'id_teknisi'      => 'nullable|exists:users,id'
    ]);

    Pengaduan::create([
        'pelanggan_id'    => $request->pelanggan_id,
        'id_teknisi'      => $request->id_teknisi, 
        'jenis_pengaduan' => $request->jenis_pengaduan,
        'deskripsi'       => $request->deskripsi,
        'status'          => $request->id_teknisi ? 'Dikirim ke Teknisi' : 'Menunggu'
    ]);

    return redirect()->back()->with('success', 'Pengaduan berhasil ditambahkan.');
}



    /**
     * Admin kirim pengaduan ke Teknisi
     */
    public function kirimKeSales(Request $request, $id)
    {
        $request->validate([
        'id_teknisi' => 'required|exists:users,id',
    ]);

    $pengaduan = Pengaduan::findOrFail($id);
    $pengaduan->id_teknisi = $request->id_teknisi;
    $pengaduan->status     = 'Dikirim ke Teknisi';
    $pengaduan->save();

    return redirect()->back()->with('success', 'Pengaduan berhasil dikirim ke Teknisi.');
}

    /**
     * Detail pengaduan
     */
    public function show($id)
    {
        $pengaduan = Pengaduan::with(['pelanggan', 'teknisi'])->findOrFail($id);
        return view('admin.pengaduan.show', compact('pengaduan'));
    }

    /**
     * Edit pengaduan
     */
    public function edit($id)
    {
        $pengaduan = Pengaduan::findOrFail($id);
        $teknisis = User::where('role', 'teknisi')->get();

        return view('admin.pengaduan.edit', compact('pengaduan', 'teknisis'));
    }

    /**
     * Update pengaduan
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'jenis_pengaduan' => 'required',
            'deskripsi'       => 'required|string',
            'id_teknisi'      => 'nullable|exists:users,id',
            'status'          => 'required|in:Menunggu,Dikirim ke Teknisi,selesai'
        ]);

        $pengaduan = Pengaduan::findOrFail($id);
        $pengaduan->update($request->only(
            'jenis_pengaduan',
            'deskripsi',
            'id_teknisi',
            'status'
        ));

        return redirect()->back()->with('success', 'Pengaduan berhasil diupdate.');
    }

    /**
     * Hapus pengaduan
     */
    public function destroy($id)
    {
        $pengaduan = Pengaduan::findOrFail($id);
        $pengaduan->delete();

        return redirect()->back()->with('success', 'Pengaduan berhasil dihapus.');
    }

    public function selesai($id)
{
    $pengaduan = Pengaduan::findOrFail($id);
    $pengaduan->status = 'selesai'; // pastikan field status sesuai DB
    $pengaduan->save();

    return redirect()->back()->with('success', 'Pengaduan berhasil diselesaikan.');
}
}
