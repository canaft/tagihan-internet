@extends('layouts.app')

@section('title', 'Izin Absen')

@section('navbar')
<nav class="fixed top-0 left-0 w-full bg-[#142A63] text-white px-4 py-3 flex items-center shadow-md z-50">
    <a href="{{ route('absen.history') }}" class="mr-3 text-lg">
        <i class="fas fa-arrow-left"></i>
    </a>
    <p class="font-semibold text-base">Form Izin Absen</p>
</nav>
@endsection

@section('content')
<div class="pt-20 pb-32 min-h-screen bg-white">

    {{-- CARD FORM IZIN --}}
    <div class="bg-white mx-4 p-6 rounded-2xl shadow border">

        <p class="text-gray-700 font-semibold mb-4">Isi Form Izin:</p>

        <form action="{{ route('absen.izin.submit') }}" method="POST">
            @csrf

            {{-- Tanggal Izin --}}
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1" for="tanggal">Tanggal Izin</label>
                <input type="date" name="tanggal" id="tanggal"
                       class="w-full border rounded-xl px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#142A63]"
                       required>
            </div>

            {{-- Alasan Izin --}}
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1" for="alasan">Alasan</label>
                <textarea name="alasan" id="alasan" rows="4"
                          class="w-full border rounded-xl px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#142A63]"
                          placeholder="Tuliskan alasan izin..." required></textarea>
            </div>

            {{-- Submit --}}
            <div class="mt-6">
                <button type="submit"
                        class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-3 rounded-xl transition">
                    Kirim Izin
                </button>
            </div>

        </form>
    </div>

</div>
@endsection
