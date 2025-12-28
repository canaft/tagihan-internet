@extends('layouts.app')

@section('title', 'Detail Pengaduan')

@section('content')
<div class="min-h-screen bg-[#F5EEEB] pt-24 px-4 pb-20">

    <div class="bg-white rounded-2xl shadow-lg max-w-2xl md:max-w-3xl mx-auto p-6">

        {{-- HEADER --}}
        <div class="mb-6 border-b pb-4">
            <h2 class="text-xl font-bold text-[#2A4156]">Detail Pengaduan</h2>
            <p class="text-sm text-gray-500">Informasi lengkap pengaduan pelanggan</p>
        </div>

        {{-- DETAIL DATA --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">

            <div>
                <p class="text-gray-500">ID Pengaduan</p>
                <p class="font-semibold text-gray-800">#{{ $pengaduan->id }}</p>
            </div>

            <div>
                <p class="text-gray-500">ID Pelanggan</p>
                <p class="font-semibold text-gray-800">
                    {{ $pengaduan->pelanggan_id ?? '-' }}
                </p>
            </div>

            <div>
                <p class="text-gray-500">Jenis Pengaduan</p>
                <p class="font-semibold text-gray-800">
                    {{ $pengaduan->jenis_pengaduan ?? '-' }}
                </p>
            </div>

            <div>
                <p class="text-gray-500">Status</p>
                <span class="inline-block px-3 py-1 text-xs rounded-full font-semibold
                    {{ $pengaduan->status === 'selesai'
                        ? 'bg-green-100 text-green-700'
                        : 'bg-yellow-100 text-yellow-700' }}">
                    {{ ucfirst($pengaduan->status ?? 'pending') }}
                </span>
            </div>

            <div class="sm:col-span-2">
                <p class="text-gray-500">Deskripsi</p>
                <p class="font-medium text-gray-800 leading-relaxed">
                    {{ $pengaduan->deskripsi ?? '-' }}
                </p>
            </div>

            <div class="sm:col-span-2">
                <p class="text-gray-500">Tanggal Dibuat</p>
                <p class="font-medium text-gray-800">
                    {{ optional($pengaduan->created_at)->format('d M Y, H:i') ?? '-' }}
                </p>
            </div>

        </div>

        {{-- FOTO BUKTI --}}
        @if(!empty($pengaduan->bukti_foto))
        <div class="mt-8">
            <p class="font-semibold text-gray-700 mb-3">Bukti Foto</p>

            <div class="bg-gray-100 p-3 rounded-xl">
                <img
                    src="{{ asset('storage/'.$pengaduan->bukti_foto) }}"
                    alt="Bukti Foto"
                    class="w-full max-h-80 md:max-h-64 object-contain rounded-lg mx-auto">
            </div>
        </div>
        @endif

        {{-- ACTION --}}
        <div class="mt-8 flex justify-end">
            <a href="{{ route('kangteknisi.pengaduan.index') }}"
               class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-5 py-2 rounded-lg font-semibold transition">
                ← Kembali
            </a>
        </div>

    </div>
</div>
@endsection
