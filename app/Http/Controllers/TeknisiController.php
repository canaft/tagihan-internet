<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // atau model khusus Sales/Teknisi
use Illuminate\Support\Facades\Hash;

class TeknisiController extends Controller
{
    public function index()
    {
        $teknisis = User::where('role', 'teknisi')->get(); // asumsi ada field 'role'
        return view('admin.teknisi.index', compact('teknisis'));
    }

    public function create()
    {
        return view('admin.teknisi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users',
            'password' => 'required|string|min:6',
            'phone' => 'required|string|max:15',
            'wilayah' => 'required|string|max:100',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'wilayah' => $request->wilayah,
            'role' => 'teknisi',
        ]);

        return redirect()->route('teknisi.index')->with('success', 'Akun berhasil ditambahkan');
    }

    public function edit($id)
    {
        $teknisi = User::findOrFail($id);
        return view('admin.teknisi.edit', compact('teknisi'));
    }

    public function update(Request $request, $id)
    {
        $teknisi = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username,'.$id,
            'phone' => 'required|string|max:15',
            'wilayah' => 'required|string|max:100',
        ]);

        $teknisi->update([
            'name' => $request->name,
            'username' => $request->username,
            'phone' => $request->phone,
            'wilayah' => $request->wilayah,
        ]);

        return redirect()->route('teknisi.index')->with('success', 'Akun berhasil diperbarui');
    }

    public function destroy($id)
    {
        $teknisi = User::findOrFail($id);
        $teknisi->delete();
        return redirect()->route('teknisi.index')->with('success', 'Akun berhasil dihapus');
    }
}
 