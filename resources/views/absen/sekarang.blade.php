@extends('layouts.app')

@section('title', 'Karyawan')

@section('navbar')
<nav class="fixed top-0 left-0 w-full z-50 bg-[#142A63] text-white p-4 flex justify-between items-center shadow-md">
    <div class="text-xl font-bold">Karyawan</div>

    {{-- Logout Desktop --}}
    <form method="POST" action="{{ route('logout') }}" class="hidden lg:block">
        @csrf
        <button type="submit"
            class="bg-white text-[#142A63] px-4 py-2 rounded-lg font-semibold hover:opacity-90 transition">
            Logout
        </button>
    </form>
</nav>
@endsection

@section('content')
<div class="min-h-screen bg-[#F5F5F5] pb-32 pt-24">

    <div class="px-4 mt-4">

        {{-- TOMBOL KEMBALI MINIMALIS --}}
        <a href="{{ url('/kangteknisi/dashboard') }}"
           class="inline-flex items-center text-[#142A63] bg-white px-4 py-2 rounded-full shadow hover:bg-gray-100 transition font-medium mb-4">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>

        {{-- KARTU NAMA USER --}}
        <div class="bg-white p-5 rounded-xl shadow">
            <p class="text-gray-600">Hai,</p>
            <p class="text-2xl font-semibold capitalize">{{ auth()->user()->name }}</p>
            <p class="text-gray-500 text-sm">{{ auth()->user()->role }}</p>
        </div>

        {{-- MENU 3 KOTAK --}}
        <div class="grid grid-cols-2 gap-4 mt-6">

            {{-- History Absen --}}
  <a href="{{ route('absen.history') }}"
   class="bg-white rounded-xl shadow p-5 flex flex-col items-center hover:bg-gray-100 transition">
    <div class="text-yellow-500 text-4xl mb-2">
        <i class="fas fa-clipboard-check"></i>
    </div>
    <p class="font-medium text-gray-700 text-sm">History Absen</p>
</a>


            {{-- BON --}}
            <a href="#"
               class="bg-white rounded-xl shadow p-5 flex flex-col items-center">
                <div class="text-red-500 text-4xl mb-2">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <p class="font-medium text-gray-700 text-sm">BON</p>
            </a>

            {{-- Keuangan --}}
<a href="{{ route('keuangan.index') }}" 
   class="bg-white rounded-xl shadow p-5 flex flex-col items-center">
    <div class="text-lime-600 text-4xl mb-2">
        <i class="fas fa-wallet"></i>
    </div>
    <p class="font-medium text-gray-700 text-sm">Keuangan</p>
</a>

        </div>

<a href="{{ route('absen.detail') }}"
   class="w-full block text-center bg-[#142A63] text-white py-3 rounded-xl font-semibold mt-6">
   Detail Absen
</a>











    </div>
</div>
@endsection
