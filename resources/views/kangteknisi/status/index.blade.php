@extends('layouts.app')

@section('title', 'Update Status Perbaikan')

@section('content')
<div class="pt-24 px-4 min-h-screen bg-[#F5EEEB]">

    <h2 class="text-xl font-semibold mb-4 text-[#2A4156]">Update Status Perbaikan</h2>

    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded-lg mb-4 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Jika tidak ada data --}}
   {{-- Cek jika tidak ada pengaduan Pending --}}
@if ($pending->isEmpty())
    <div class="bg-white text-center p-6 rounded-2xl shadow-sm">
        <p class="text-gray-500">Tidak ada pengaduan Pending untuk diperbarui.</p>
    </div>
@else
    <div class="bg-white rounded-2xl shadow-sm p-4 overflow-x-auto">
        <table class="w-full border-collapse">
            <thead class="bg-[#2A4156] text-white">
                <tr>
                    <th class="p-3 text-left">Pelanggan</th>
                    <th class="p-3 text-left">Jenis Pengaduan</th>
                    <th class="p-3 text-left">Deskripsi</th>
                    <th class="p-3 text-left">Status</th>
                    <th class="p-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pending as $p)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3">{{ $p->pelanggan_id }}</td>
                        <td class="p-3">{{ $p->jenis_pengaduan }}</td>
                        <td class="p-3 text-sm text-gray-600">{{ $p->deskripsi }}</td>
                        <td class="p-3 font-medium">
                            <span class="text-yellow-600 bg-yellow-100 px-3 py-1 rounded-full text-xs">Pending</span>
                        </td>
                        <td class="p-3 text-center">
                            <form action="{{ route('kangteknisi.status.update', $p->id) }}" method="POST" class="inline-block">
                                @csrf
                                <input type="hidden" name="status" value="Sedang Dikerjakan">
                                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-lg text-xs">
                                    Sedang Dikerjakan
                                </button>
                            </form>

                            <form action="{{ route('kangteknisi.status.update', $p->id) }}" method="POST" class="inline-block ml-2">
                                @csrf
                                <input type="hidden" name="status" value="Selesai">
                                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-lg text-xs">
                                    Selesai
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

</div>
@endsection
