@extends('layouts.app')

@section('title', 'Detail Karyawan')

@section('navbar')
<nav class="fixed top-0 left-0 w-full z-50 bg-[#142A63] text-white px-5 py-4 flex items-center shadow-md">
    <a href="{{ url('/karyawan') }}" class="mr-4 text-lg">
        <i class="fas fa-arrow-left"></i>
    </a>
    <p class="text-lg font-semibold">Detail Karyawan</p>
</nav>
@endsection

@section('content')
<div class="pt-20 pb-32 min-h-screen bg-[#F5F5F5]">



    {{-- =======================
            CARD KARYAWAN
    ======================== --}}
    <div class="bg-white mx-4 p-5 rounded-3xl shadow-sm">
        <p class="text-gray-500 text-sm mb-1">Karyawan</p>

        <p class="text-[22px] font-semibold capitalize leading-tight">
            {{ auth()->user()->name }}
        </p>

        <p class="text-gray-600 text-[13px] capitalize mt-1">
            {{ auth()->user()->role }}
        </p>



        {{-- CARD GAJI --}}
        <div class="bg-[#F8F8F8] p-4 rounded-3xl mt-5 border border-gray-200">

            <div class="grid grid-cols-2">
                <div>
                    <p class="text-gray-500 text-[13px]">Gaji</p>
                    <p class="text-[22px] font-semibold leading-tight">
                        Rp. {{ number_format(auth()->user()->gaji ?? 0, 0, ',', '.') }}
                    </p>
                </div>

                <div class="text-right">
                    <p class="text-gray-500 text-[13px]">Sisa BON</p>
                    <p class="text-[22px] font-semibold text-red-500 leading-tight">
                        Rp. {{ number_format(auth()->user()->sisa_bon ?? 0, 0, ',', '.') }}
                    </p>
                </div>
            </div>

            <p class="text-gray-500 text-[13px] mt-3">Tanggal Gajian</p>
            <p class="text-[22px] font-semibold leading-tight">
                {{ auth()->user()->tanggal_gajian ?? '-' }}
            </p>
        </div>
    </div>



    {{-- =======================
            HISTORY ABSENSI
    ======================== --}}
    <div class="bg-white mx-4 p-5 rounded-3xl shadow-sm mt-6">
        <div class="flex justify-between items-center mb-3">
            <p class="text-gray-700 font-semibold text-[16px]">History Absensi</p>

            <a href="{{ route('absen.detail') }}" class="text-[#142A63] font-semibold text-[14px]">
                Lihat Semua
            </a>
        </div>

        @if ($absenMini->count() == 0)
            <p class="text-center text-gray-400 text-sm mt-4">Tidak ada Data</p>
        @else
            @foreach ($absenMini as $row)
    <div class="py-3 border-b border-gray-200">
        <p class="text-[13px] text-gray-500">
            {{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('d M Y') }}
        </p>

        <p class="text-[17px] font-semibold text-gray-800 mt-1">
            Masuk : 
            @if($row->status === 'izin')
                Izin
            @else
                {{ $row->jam_masuk ? date('H:i', strtotime($row->jam_masuk)) . ' WIB' : '-' }}
            @endif
        </p>

        <p class="text-[17px] font-semibold text-gray-600">
            Pulang : 
            @if($row->status === 'izin')
                Izin
            @else
                {{ $row->jam_pulang ? date('H:i', strtotime($row->jam_pulang)) . ' WIB' : '-' }}
            @endif
        </p>
    </div>
@endforeach

        @endif
    </div>



    {{-- =======================
            HISTORY GAJIAN
    ======================== --}}
    <div class="bg-white mx-4 p-5 rounded-3xl shadow-sm mt-6">
        <p class="text-gray-700 font-semibold text-[16px]">History Gajian</p>
        <p class="text-gray-500 text-[13px] -mt-1">(Tap Tahan Untuk Opsi)</p>

        <p class="text-center text-gray-400 text-sm mt-5">Tidak ada Data</p>
    </div>

    {{-- TOMBOL KEMBALI --}}
    <div class="mx-4 mt-6">
        <a href="{{ route('absen.history') }}"
           class="w-full inline-block text-center bg-gray-400 text-white py-3 rounded-xl font-semibold hover:bg-gray-500 transition">
            Kembali
        </a>
    </div>

</div>
@endsection

