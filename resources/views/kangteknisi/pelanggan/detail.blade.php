@extends('layouts.app')
@section('title', 'Detail Pelanggan')

@section('content')

<div class="pt-24 pb-20 max-w-4xl mx-auto">

    {{-- Tombol Kembali --}}
    <div class="fixed top-20 left-4 z-50">
        <a href="{{ route('kangteknisi.pelanggan.index') }}"
           class="inline-flex items-center px-3 py-2 bg-[#142A63] text-white rounded-full shadow hover:bg-[#0f204c] transition">
            <i class="fa-solid fa-arrow-left text-lg"></i>
        </a>
    </div>

    {{-- CARD INFORMASI PELANGGAN --}}
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ $pelanggan->name }}</h2>
                <p class="text-gray-500">{{ $pelanggan->phone }} • {{ $pelanggan->area->nama_area ?? '-'}}</p>
            </div>

            {{-- Status Aktif (Tetap Ada) --}}
            <span class="px-3 py-1 text-sm rounded-full 
                {{ $pelanggan->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                {{ $pelanggan->is_active ? 'Aktif' : 'Nonaktif' }}
            </span>
        </div>
    </div>

    {{-- INFORMASI PAKET & TAGIHAN --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div class="bg-blue-50 p-4 rounded-lg shadow-sm hover:shadow-md transition">
            <div class="text-blue-600 text-xl mb-1"><i class="fa fa-wifi"></i></div>
            <div class="font-bold text-lg">{{ $pelanggan->package->nama_paket ?? '-'}}</div>
            <div class="text-sm text-gray-500">Paket Internet</div>
        </div>

        <div class="bg-green-50 p-4 rounded-lg shadow-sm hover:shadow-md transition">
            <div class="text-green-600 text-xl mb-1"><i class="fa fa-money-bill"></i></div>
            <div class="font-bold text-lg">Rp{{ number_format($pelanggan->tagihan_terakhir ?? 0, 0, ',', '.') }}</div>
            <div class="text-sm text-gray-500">Tagihan Terakhir</div>
        </div>
    </div>

    {{-- INFORMASI TEKNIS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition">
            <h3 class="font-semibold text-gray-700 mb-2">Perangkat</h3>
            <p class="text-gray-800">{{ $pelanggan->device->ip_address ?? '-' }}</p>
            <p class="text-sm text-gray-500">Mikrotik / Modem</p>
        </div>

        <div class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition">
            <h3 class="font-semibold text-gray-700 mb-2">ODP & Koordinat</h3>
            <p class="text-gray-800 mb-1">Kode ODP: {{ $pelanggan->odp->kode ?? '-' }}</p>
            <p class="text-sm text-gray-500 break-words">Koordinat: {{ $pelanggan->koordinat ?? '-' }}</p>
        </div>
    </div>

    {{-- INFORMASI TAMBAHAN --}}
    <div class="bg-white p-4 rounded-lg shadow-sm mb-6 hover:shadow-md transition">
        <h3 class="font-semibold text-gray-700 mb-3">Informasi Tambahan</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">

            <div>
                <span class="text-gray-500">Tanggal Register:</span>
                <p class="font-medium">{{ \Carbon\Carbon::parse($pelanggan->tanggal_register)->format('d M Y') }}</p>
            </div>

            <div>
                <span class="text-gray-500">Tanggal Tagihan:</span>
                <p class="font-medium">{{ \Carbon\Carbon::parse($pelanggan->tanggal_tagihan)->format('d M Y') }}</p>
            </div>

            <div>
                <span class="text-gray-500">Area:</span>
                <p class="font-medium">{{ $pelanggan->area->nama_area ?? '-' }}</p>
            </div>

            <div>
                <span class="text-gray-500">Keterangan ODP:</span>
                <p class="font-medium">{{ $pelanggan->keterangan_odp ?? '-' }}</p>
            </div>
        </div>
    </div>

    {{-- RIWAYAT BULAN YANG SUDAH DIBAYAR --}}
    @php
        $riwayat = $pelanggan->transaksi()
            ->where('status','lunas')
            ->orderBy('id','DESC')
            ->get();
    @endphp

    <div class="bg-white p-4 rounded-xl shadow mb-6">
        <h3 class="font-semibold text-gray-700 mb-3">Riwayat Bulan yang Sudah Dibayar</h3>

        @if($riwayat->isEmpty())
            <p class="text-gray-500 text-sm italic">Belum ada pembayaran.</p>
        @else
            <div class="space-y-3">
                @foreach($riwayat as $t)
                    <div class="border p-3 rounded-lg hover:bg-gray-50 transition">
                        <div class="flex justify-between items-center">

                            {{-- Bulan --}}
                            <div>
                                <p class="font-medium text-gray-800">
                                    {{ str_replace(',', ', ', $t->bulan) }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    Metode: {{ ucfirst($t->metode) }} | Dibayar oleh: {{ $t->dibayar_oleh }}
                                </p>
                            </div>

                            {{-- Total --}}
                            <div class="font-bold text-green-600 text-lg">
                                Rp{{ number_format($t->jumlah, 0, ',', '.') }}
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection
