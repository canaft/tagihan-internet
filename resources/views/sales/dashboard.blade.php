@extends('layouts.app')

@section('title', 'Dashboard Sales')

{{-- NAVBAR FIXED DI ATAS --}}
@section('navbar')
<nav class="fixed top-0 left-0 w-full z-50 bg-[var(--primary-color)] text-white p-4 flex justify-between items-center shadow-md">
    <div class="text-xl font-bold">@yield('app-name', 'Sistem KangTeknisi')</div>

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

{{-- HEADER INFO SALES --}}
<div class="bg-[var(--primary-color)] text-white rounded-2xl p-4 sm:p-6 shadow-md mb-6">
    <div class="flex justify-between items-start">
        <div>
            <div class="text-sm opacity-90">
                {{ \Carbon\Carbon::now()->translatedFormat('d M Y') }}
            </div>
            <div class="text-lg font-semibold mt-1">
                Hai, {{ Auth::user()->name ?? 'Sales' }}
            </div>
            <div class="text-sm opacity-90">
                ID: {{ Auth::user()->id ?? '00000' }}
            </div>
        </div>

        <div class="flex items-center gap-4 text-xl">
            <!-- <a href="#" class="hover:text-[var(--accent-color)] transition"><i class="fas fa-bell"></i></a> -->
<a href="{{ route('sales.setting') }}" class="hover:text-[var(--accent-color)] transition">
    <i class="fas fa-cog"></i>
</a>
        </div>
    </div>

    <div class="bg-white text-[var(--primary-color)] rounded-xl mt-4 p-3 flex justify-between items-center shadow-sm">
        <div>
            <div class="font-medium">Status Sistem</div>
            <div class="text-xs text-gray-500">Semua normal</div>
        </div>
        <div class="font-bold text-[var(--accent-color)]">Normal</div>
    </div>
</div>

{{-- MENU GRID UNTUK SALES --}}
<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mb-6">
@php
    $menus = [
        [
            'icon' => '👥',
            'title' => 'Daftar Pelanggan',
            'desc' => 'Lihat pelanggan berdasarkan wilayah',
            'link' => route('sales.pelanggan.index'),
        ],
        [
            'icon' => '💰',
            'title' => 'Daftar Tagihan',
            'desc' => 'Pantau tagihan pelanggan',
            'link' => route('sales.tagihan.index'),
        ],
        [
            'icon' => '✅',
            'title' => 'Pelanggan Lunas',
            'desc' => 'Daftar pelanggan yang sudah membayar',
            'link' => route('sales.pelanggan.lunas'),
        ],
    ];
@endphp

    @foreach($menus as $menu)
    <a href="{{ $menu['link'] }}"
       class="menu-card group bg-white p-4 sm:p-5 rounded-2xl flex flex-col items-center text-center 
              shadow-sm hover:shadow-lg transition transform hover:-translate-y-1 hover:scale-105 duration-300">
        <div class="bg-[var(--accent-color)] text-white rounded-full p-3 sm:p-4 mb-2 sm:mb-3 text-2xl sm:text-3xl 
                    transition-all duration-300 group-hover:scale-110">
            {{ $menu['icon'] }}
        </div>
        <h3 class="font-semibold text-sm sm:text-lg group-hover:text-[var(--accent-color)] transition-colors duration-300">
            {{ $menu['title'] }}
        </h3>
        <p class="text-xs sm:text-sm text-gray-500 mt-1">{{ $menu['desc'] }}</p>
    </a>
    @endforeach
</div>

@endsection
