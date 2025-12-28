@extends('layouts.app')

@section('title', 'Absen')

@section('navbar')
<nav class="fixed top-0 left-0 w-full z-50 bg-[#142A63] text-white p-4 flex items-center shadow-md">
    <div class="text-xl font-bold">Absen</div>
</nav>
@endsection

@section('content')
<div class="min-h-screen bg-[#F5F5F5] pt-24 pb-32">

    {{-- TOMBOL KEMBALI DI BAWAH NAVBAR --}}
<div class="px-4 mb-4">
    <a href="{{ url('/teknisi/absen') }}"
       class="inline-flex items-center px-3 py-2 bg-gray-400 text-white rounded-xl shadow hover:bg-gray-500 transition">
        <i class="fas fa-arrow-left mr-2"></i> Kembali
    </a>
</div>


    <div class="px-4 space-y-4">

        {{-- INFORMASI KARYAWAN --}}
        <div class="bg-white p-5 rounded-xl shadow">
            <p class="text-gray-600">Karyawan:</p>
            <p class="font-semibold text-lg capitalize">{{ $user->name }}</p>
            <p class="text-gray-500 text-sm">{{ $user->role }}</p>
        </div>

        {{-- DETAIL ABSEN --}}
        <div class="bg-white p-5 rounded-xl shadow space-y-3">
            <div>
                <p class="text-gray-600">Bulan</p>
                <p class="font-semibold text-lg">{{ \Carbon\Carbon::now()->translatedFormat('M Y') }}</p>
            </div>

            <div>
                <p class="text-gray-600">Tanggal</p>
                <p class="font-semibold text-lg">
                    {{ $absen ? \Carbon\Carbon::parse($absen->tanggal)->translatedFormat('d M Y') : '-' }}
                </p>
            </div>

            <div>
                <p class="text-gray-600">Shift</p>
                <p class="font-semibold text-lg">Siang (09:00 - 16:00)</p>
            </div>

            @php
                $shiftMulai = \Carbon\Carbon::createFromTime(9,0,0);
                $jamMasuk = $absen && $absen->jam_masuk ? \Carbon\Carbon::parse($absen->jam_masuk) : null;
                $telat = $jamMasuk && $jamMasuk->gt($shiftMulai) ? $jamMasuk->diffInMinutes($shiftMulai) : 0;
            @endphp

            <div>
                <p class="text-gray-600">Telat</p>
                <p class="font-semibold">{{ $absen && $absen->status !== 'izin' ? $telat : '-' }} Menit</p>
            </div>

            <div class="flex justify-between mt-3 text-lg">
                <div>
                    <p class="text-gray-500 text-sm">Masuk</p>
                    <p class="font-semibold">
                        @if($absen)
                            @if($absen->status === 'izin')
                                Izin
                            @else
                                {{ $absen->jam_masuk ? \Carbon\Carbon::parse($absen->jam_masuk)->format('H:i') : '-' }}
                            @endif
                        @else
                            -
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Pulang</p>
                    <p class="font-semibold">
                        @if($absen)
                            @if($absen->status === 'izin')
                                Izin
                            @else
                                {{ $absen->jam_pulang ? \Carbon\Carbon::parse($absen->jam_pulang)->format('H:i') : '-' }}
                            @endif
                        @else
                            -
                        @endif
                    </p>
                </div>
            </div>
        </div>

        {{-- TOMBOL DINAMIS ABSEN --}}
        @if(!$absen)
            {{-- BELUM ADA ABSEN → TOMBOL MASUK --}}
            <form action="{{ route('absen.masuk') }}" method="POST">
                @csrf
                <button type="submit" class="w-full bg-[#142A63] text-white py-4 rounded-xl font-semibold mt-4">
                    Absen Masuk
                </button>
            </form>

        @elseif($absen && $absen->status !== 'izin')
            @if(!$absen->jam_masuk)
                {{-- TOMBOL MASUK --}}
                <form action="{{ route('absen.masuk', $absen->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-[#142A63] text-white py-4 rounded-xl font-semibold mt-4">
                        Absen Masuk
                    </button>
                </form>
            @elseif($absen->jam_masuk && !$absen->jam_pulang)
                {{-- TOMBOL PULANG --}}
                <form action="{{ route('absen.pulang', $absen->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-[#142A63] text-white py-4 rounded-xl font-semibold mt-4">
                        Absen Pulang
                    </button>
                </form>
            @else
                <p class="text-center text-gray-500 font-semibold mt-4">
                    Anda sudah selesai absen hari ini
                </p>
            @endif
        @else
            {{-- STATUS IZIN --}}
            <p class="text-center text-gray-500 font-semibold mt-4">
                Anda sedang izin hari ini
            </p>
        @endif

        <p class="text-center text-gray-600 mt-3">Atau</p>

        {{-- TOMBOL IZIN --}}
        <div class="mx-4 mt-2">
            <a href="{{ route('absen.izin') }}"
               class="w-full inline-block text-center bg-yellow-500 text-white py-3 rounded-xl font-semibold hover:bg-yellow-600 transition">
                Izin
            </a>
        </div>

    </div>
</div>
@endsection
