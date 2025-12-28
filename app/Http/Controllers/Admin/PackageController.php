<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{

public function index()
{
    $packages = Package::orderBy('id', 'desc')->paginate(10); // 10 per halaman
    return view('admin.paket.index', compact('packages'));
}


    public function create()
    {
        return view('admin.paket.create');
    }

public function store(Request $request)
{
    $request->validate([
        'nama_paket' => 'required|string|max:255',
        'harga' => 'required|numeric',
    ]);

    Package::create([
        'nama_paket' => $request->nama_paket,
        'harga' => $request->harga,
    ]);

    return redirect()->route('admin.paket.index')->with('success', 'Paket berhasil ditambahkan.');
}


    public function edit($id)
    {
        $package = Package::findOrFail($id);
        return view('admin.paket.edit', compact('package'));
    }

   public function update(Request $request, $id)
{
    $request->validate([
        'nama_paket' => 'required|string|max:255',
        'harga' => 'required|numeric',
    ]);

    $package = Package::findOrFail($id);
    $package->nama_paket = $request->nama_paket;
    $package->harga = $request->harga;
    $package->save();

    return redirect()->route('admin.paket.index')->with('success', 'Paket berhasil diperbarui.');
}


    public function destroy($id)
    {
        $package = Package::findOrFail($id);
        $package->delete();

        return redirect()->route('admin.paket')->with('success', 'Paket berhasil dihapus.');
    }
}
