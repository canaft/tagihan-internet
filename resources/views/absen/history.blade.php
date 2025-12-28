@extends('layouts.app')

@section('title', 'History Absensi')

@section('navbar')
{{-- NAVBAR MIRIP APLIKASI --}}
<nav class="fixed top-0 left-0 w-full bg-[#142A63] text-white px-4 py-3 flex items-center shadow-md z-50">
    <a href="{{ url('/karyawan') }}" class="mr-3 text-lg">
        <i class="fas fa-arrow-left"></i>
    </a>
    <p class="font-semibold text-base">History Absensi</p>
</nav>
@endsection

@section('content')
<div class="pt-20 pb-32 min-h-screen bg-white">

    {{-- CARD KARYAWAN --}}
    <div class="bg-white mx-4 p-4 rounded-2xl shadow border">
        <p class="text-gray-600 text-sm">Karyawan :</p>
        <p class="text-[20px] font-semibold capitalize">{{ auth()->user()->name }}</p>
        <p class="text-gray-500 text-xs">{{ auth()->user()->role }}</p>

<div class="flex justify-end mt-3">
    @if(!$absenHariIni)
        <a href="{{ route('absen.detail') }}"
           class="bg-green-500 text-white font-semibold px-6 py-2 rounded-xl">
            ABSEN
        </a>
    @else
        <p class="text-gray-500 font-semibold">Anda sudah absen hari ini.</p>
    @endif
</div>

    

    {{-- LIST ABSEN HARI INI --}}
    <div class="px-4 mt-6">
        <p class="text-gray-700 mb-3 text-sm">List Absen Hari ini :</p>

        @foreach ($absens as $absen)
        <div class="mb-6">

            {{-- TANGGAL --}}
            <p class="text-gray-900 font-semibold text-[17px]">
                {{ \Carbon\Carbon::parse($absen->tanggal)->translatedFormat('l, d M Y') }}
            </p>

            {{-- CARD --}}
            <a href="{{ route('absen.detailhistory', $absen->id) }}" class="block">
    <div class="bg-[#E3F3FF] mt-2 p-5 rounded-2xl shadow flex justify-between items-center
                transition transform hover:scale-[1.01] active:scale-[0.98] cursor-pointer">

        {{-- KIRI --}}
{{-- KIRI --}}
<div>
    <p class="text-[15px] font-semibold text-gray-700">Shift</p>
    <p class="text-[13px] text-gray-600 -mt-1">{{ $absen->shift }}</p>

    <p class="text-[15px] font-semibold text-gray-700 mt-3">Masuk</p>
    <p class="text-[#142A63] font-bold text-[22px] leading-none">
        @if($absen->status === 'izin')
            Izin
        @else
            {{ $absen->jam_masuk ? date('H:i', strtotime($absen->jam_masuk)) . ' WIB' : '-' }}
        @endif
    </p>
</div>

        {{-- ICON PANAH --}}
        <div>
            <i class="fas fa-chevron-right text-gray-400 text-lg"></i>
        </div>

        {{-- KANAN --}}
<div class="text-right">
    <p class="text-[15px] font-semibold text-gray-700">Pulang</p>
    <p class="text-[#FBBF24] font-bold text-[22px] leading-none">
        @if($absen->status === 'izin')
            Izin
        @else
            {{ $absen->jam_pulang ? date('H:i', strtotime($absen->jam_pulang)) . ' WIB' : '-' }}
        @endif
    </p>
</div>

    </div>
</a>


        </div>
        @endforeach
{{-- TOMBOL KEMBALI --}}
<div class="mx-4 mt-6">
    <a href="{{ route('absen.index') }}"
       class="w-full inline-block text-center bg-gray-400 text-white py-3 rounded-xl font-semibold hover:bg-gray-500 transition">
        Kembali
    </a>
</div>

    </div>

</div>
@endsection
