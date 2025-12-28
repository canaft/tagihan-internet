@extends('layouts.app')

@section('title', 'Keuangan')

@section('navbar')
<nav class="fixed top-0 left-0 w-full z-50 bg-[#142A63] text-white p-4 flex justify-between items-center shadow-md">
    <div class="text-xl font-bold tracking-wide">Keuangan</div>
</nav>
@endsection

@section('content')

{{-- MAX WRAPPER --}}
<div class="min-h-screen bg-[#F5F5F5] pt-24 pb-20 px-4 flex justify-center">
    <div class="w-full max-w-3xl">

        {{-- BACK BUTTON DI ATAS --}}
        <div class="mb-4 flex justify-start">
            <a href="{{ url('/teknisi/absen') }}"
                class="bg-red-500 px-5 py-2 rounded-lg text-white text-sm font-semibold shadow hover:bg-red-600 transition inline-block">
                ← Kembali 
            </a>
        </div>

        {{-- CARD FILTER --}}
        <div class="bg-white rounded-xl shadow-md p-5 border border-gray-100 mb-6">

            <form action="{{ route('keuangan.index') }}" method="GET" class="space-y-4">

                <div class="flex flex-wrap gap-4 items-end">

                    {{-- TANGGAL --}}
                    <div class="flex flex-col flex-1 min-w-[150px]">
                        <label class="text-xs text-gray-600 font-semibold mb-1">Tanggal</label>
                        <input type="date"
                            name="tanggal"
                            value="{{ $tanggal }}"
                            class="border border-gray-300 px-3 py-2 rounded-lg text-gray-700 focus:ring-2 focus:ring-[#142A63] outline-none transition">
                    </div>

                    {{-- KARYAWAN --}}
                    <div class="flex flex-col flex-1 min-w-[150px]">
                        <label class="text-xs text-gray-600 font-semibold mb-1">Karyawan</label>
                        <select name="karyawan"
                            class="border border-gray-300 px-3 py-2 rounded-lg text-gray-700 focus:ring-2 focus:ring-[#142A63] outline-none transition">
                            <option value="">Semua</option>
                            @foreach($karyawanList as $k)
                                <option value="{{ $k->id }}" {{ $karyawan_id == $k->id ? 'selected' : '' }}>
                                    {{ $k->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- BUTTONS --}}
                    <div class="flex gap-2">
                        <button type="submit"
                            class="bg-[#142A63] px-4 py-2 rounded-lg text-white font-semibold shadow hover:bg-[#203c85] transition text-sm">
                            Cari
                        </button>

                        <a href="{{ route('keuangan.index') }}"
                            class="bg-gray-300 px-4 py-2 rounded-lg text-gray-800 font-semibold shadow hover:bg-gray-400 transition text-sm">
                            Reset
                        </a>
                    </div>

                </div>
            </form>

        </div>


        {{-- LIST KEUANGAN --}}
        <div class="bg-white rounded-xl shadow-md overflow-hidden">

            <div class="text-center bg-[#142A63] text-white py-3">
                <p class="text-sm font-medium tracking-wide">
                    {{ $tanggal ? date('d M Y', strtotime($tanggal)) : 'Hari Ini' }}
                </p>
            </div>

            @if($data->count() == 0)
                <p class="text-gray-500 font-medium py-6 text-center">Tidak ada data keuangan</p>
            @else
                @foreach($data as $row)
                    <div class="p-4 border-b border-gray-100 flex justify-between items-center hover:bg-gray-50 transition">

                        {{-- LEFT --}}
                        <div>
                            <p class="font-semibold text-gray-800 text-sm leading-tight">
                                {{ $row->deskripsi }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ date('d M Y', strtotime($row->tanggal)) }}
                            </p>
                        </div>

                        {{-- NOMINAL --}}
                        <div class="font-bold text-sm 
                            {{ $row->tipe == 'pengeluaran' ? 'text-red-600' : 'text-green-600' }}">
                            Rp{{ number_format($row->nominal,0,',','.') }}
                        </div>

                    </div>
                @endforeach
            @endif

        </div>

    </div>
</div>

@endsection
