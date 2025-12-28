@extends('layouts.app')

@section('title', 'Dashboard KangTeknisi')

{{-- NAVBAR FIXED DI ATAS --}}
@section('navbar')
<nav class="fixed top-0 left-0 w-full z-50 bg-[var(--primary-color)] text-white p-4 flex justify-between items-center shadow-md">
    <div class="text-xl font-bold">@yield('app-name', 'Sistem KangTeknisi')</div>

    <!-- Logout Desktop -->
    <form method="POST" action="{{ route('logout') }}" class="hidden lg:block">
        @csrf
        <button type="submit"
            class="bg-[var(--accent-color)] px-4 py-2 rounded-lg font-semibold hover:opacity-90 transition">
            Logout
        </button>
    </form>
</nav>
@endsection


{{-- KONTEN UTAMA --}}
@section('content')

{{-- Jarak agar tidak ketimpa navbar --}}
<div class="pt-24"></div>

{{-- HEADER INFO TEKNISI --}}
<div class="bg-[var(--primary-color)] text-white rounded-2xl p-4 sm:p-6 shadow-md mb-6">
    <div class="flex justify-between items-start">
        <div>
            <div class="text-sm opacity-90">
                {{ \Carbon\Carbon::now()->translatedFormat('d M Y') }}
            </div>
            <div class="text-lg font-semibold mt-1">
                Hai, {{ Auth::user()->name ?? 'KangTeknisi' }}
            </div>
            <div class="text-sm opacity-90">
                ID: {{ Auth::user()->id ?? '00000' }}
            </div>
        </div>

     {{-- ICON BAGIAN KANAN ATAS --}}
<div class="flex items-center gap-4 text-xl">

    {{-- Notifikasi --}}
{{-- Notifikasi --}}
<a href="{{ route('kangteknisi.pengaduan.index') }}" 
   class="relative hover:text-[var(--accent-color)] transition">

    <i class="fas fa-bell text-xl"></i>

    {{-- Badge notifikasi --}}
    @if(isset($notifCount) && $notifCount > 0)
        <span
            class="absolute -top-2 -right-2 bg-red-500 text-white text-[10px]
                   w-4 h-4 flex items-center justify-center rounded-full font-bold">
            {{ $notifCount }}
        </span>
    @endif

</a>


    {{-- Absen --}}
<a href="{{ route('absen.index') }}" class="hover:text-[var(--accent-color)] transition inline-block">
    <i class="fas fa-user-check"></i>
</a>


    {{-- Tombol Setting --}}
    <a href="{{ route('kangteknisi.setting') }}" class="hover:text-[var(--accent-color)] transition">
        <i class="fas fa-cog"></i>
    </a>

</div>

    </div>

    {{-- Status Sistem --}}
    <div class="bg-white text-[var(--primary-color)] rounded-xl mt-4 p-3 flex justify-between items-center shadow-sm">
        <div>
            <div class="font-medium">Status Sistem</div>
            <div class="text-xs text-gray-500">Semua normal</div>
        </div>
        <div class="font-bold text-[var(--accent-color)]">Normal</div>
    </div>
</div>

{{-- MENU GRID --}}
<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mb-6">

    {{-- MENU 1: Daftar Pengaduan --}}
<a href="{{ route('kangteknisi.pengaduan.index') }}"
   class="menu-card group bg-white p-4 sm:p-5 rounded-2xl flex flex-col items-center text-center 
          shadow-sm hover:shadow-lg transition transform hover:-translate-y-1 hover:scale-105 duration-300">

        <div class="bg-[var(--accent-color)] text-white rounded-full p-3 sm:p-4 mb-2 sm:mb-3 text-2xl sm:text-3xl 
                    transition-all duration-300 group-hover:scale-110">
            🧾
        </div>
        <h3 class="font-semibold text-sm sm:text-lg group-hover:text-[var(--accent-color)] transition-colors duration-300">
            Daftar Pengaduan
        </h3>
        <p class="text-xs sm:text-sm text-gray-500 mt-1">Lihat semua pengaduan pelanggan</p>
    </a>

    <!-- {{-- MENU 2: Update Status Perbaikan --}}
<a href="{{ route('kangteknisi.status.index') }}" 
   class="menu-card group bg-white p-4 sm:p-5 rounded-2xl flex flex-col items-center text-center 
          shadow-sm hover:shadow-lg transition transform hover:-translate-y-1 hover:scale-105 duration-300">

        <div class="bg-[var(--accent-color)] text-white rounded-full p-3 sm:p-4 mb-2 sm:mb-3 text-2xl sm:text-3xl 
                    transition-all duration-300 group-hover:scale-110">
            ⚙️
        </div>
        <h3 class="font-semibold text-sm sm:text-lg group-hover:text-[var(--accent-color)] transition-colors duration-300">
            Update Status Perbaikan
        </h3>
        <p class="text-xs sm:text-sm text-gray-500 mt-1">Perbarui status tugas perbaikan</p>
    </a> -->

    {{-- MENU 3: Data Pelanggan --}}
<a href="{{ route('kangteknisi.pelanggan.index') }}"
   class="menu-card group bg-white p-4 sm:p-5 rounded-2xl flex flex-col items-center text-center 
          shadow-sm hover:shadow-lg transition transform hover:-translate-y-1 hover:scale-105 duration-300">

    <div class="bg-[var(--accent-color)] text-white rounded-full p-3 sm:p-4 mb-2 sm:mb-3 text-2xl sm:text-3xl 
                transition-all duration-300 group-hover:scale-110">
        👥
    </div>

    <h3 class="font-semibold text-sm sm:text-lg group-hover:text-[var(--accent-color)] transition-colors duration-300">
        Data Pelanggan
    </h3>

    <p class="text-xs sm:text-sm text-gray-500 mt-1">Lihat semua data pelanggan</p>
</a>


</div>

@endsection
