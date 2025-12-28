@extends('layouts.app')
@section('title', 'Setting Sales')

@php
    $hideNavbar = true;
@endphp

@section('content')
<div class="min-h-screen bg-[#F5EEEB] pb-28">

    {{-- HEADER FIXED --}}
    <div class="fixed top-0 left-0 w-full bg-[var(--primary-color)] text-white py-4 px-6 shadow-md z-50">
        <div class="flex justify-between items-center">
            <a href="{{ route('sales.dashboard') }}" 
                class="flex items-center space-x-2 hover:text-gray-200 transition">
                <i class="fas fa-arrow-left text-lg"></i>
                <span class="text-sm font-medium">Kembali</span>
            </a>
            <h1 class="text-lg font-semibold">Setting</h1>
            <span></span>
        </div>
    </div>

    {{-- KONTEN UTAMA --}}
    <div class="pt-28 md:pt-32 px-4">

        {{-- PROFIL PENGGUNA --}}
        <div class="bg-[var(--primary-color)] text-white p-6 rounded-3xl shadow-md mb-4 flex items-center space-x-4">
            <form action="{{ route('profile.update.photo') }}" method="POST" enctype="multipart/form-data" class="relative">
                @csrf
                @method('PUT')
                <label for="photo">
                    <img src="{{ Auth::user()->images ? asset('storage/' . Auth::user()->images) : asset('images/logo-dhs.png') }}" 
                        class="w-16 h-16 rounded-lg shadow-md object-cover cursor-pointer" alt="Foto Profil">
                    <span class="absolute bottom-0 right-0 bg-white text-gray-700 p-1 rounded-full shadow cursor-pointer">
                        <i class="fas fa-camera"></i>
                    </span>
                    <input type="file" name="photo" id="photo" class="hidden" onchange="this.form.submit()">
                </label>
            </form>

            <div>
                <h2 class="text-2xl font-semibold">{{ Auth::user()->name }}</h2>
                <p class="text-sm opacity-90">{{ Auth::user()->email }}</p>
                <span class="bg-green-600 text-xs px-3 py-1 rounded-full uppercase">
                    {{ Auth::user()->role ?? 'SALES' }}
                </span>
            </div>
        </div>

        {{-- BAGIAN AKUN --}}
        <div class="mb-4">
            <h3 class="text-gray-600 font-semibold mb-2">Akun</h3>

            {{-- Tombol Ganti Password --}}
            <button onclick="openConfirmPopup()" 
                class="w-full flex justify-between items-center bg-white p-3 rounded-xl shadow-sm hover:bg-gray-100 mb-2">
                <span>Ganti Password</span>
                <i class="fas fa-chevron-right text-gray-400"></i>
            </button>
        </div>

        {{-- BAGIAN LAIN-LAIN --}}
        <div class="mb-4">
            <h3 class="text-gray-600 font-semibold mb-2">Lain - lain</h3>

            <a href="#" class="flex justify-between items-center bg-white p-3 rounded-xl mb-2 shadow-sm hover:bg-gray-100">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-print text-gray-600"></i>
                    <span>Pilih Printer</span>
                </div>
                <span class="text-gray-400 text-sm">Tidak ada Printer</span>
            </a>

            <a href="#" class="flex justify-between items-center bg-white p-3 rounded-xl shadow-sm hover:bg-gray-100 text-gray-700 font-medium">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-info-circle text-blue-600"></i>
                    <span>Info Penting</span>
                </div>
            </a>

            {{-- Tombol History Hapus Lunas Bayar --}}
            <button onclick="openAdminPopup()" 
                class="w-full flex justify-between items-center bg-white p-3 rounded-xl shadow-sm hover:bg-gray-100 text-red-500 font-medium">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-trash"></i>
                    <span>History Hapus Lunas Bayar</span>
                </div>
            </button>
        </div>
    </div>

    {{-- LOGOUT BUTTON --}}
    <div class="fixed bottom-16 left-0 w-full bg-[#F5EEEB] py-4 px-6 border-t border-gray-200">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit"
                class="w-full bg-red-500 text-white py-3 rounded-full font-semibold text-lg shadow-md hover:bg-red-600 transition">
                LOGOUT
            </button>
        </form>
    </div>
</div>

{{-- POPUP KONFIRMASI GANTI PASSWORD --}}
<div id="confirmPopup" class="fixed inset-0 bg-black bg-opacity-40 hidden flex items-center justify-center z-[9999]">
    <div class="bg-white p-6 w-80 rounded-2xl shadow-lg text-center">
        <h3 class="text-lg font-semibold mb-3">Ganti Password</h3>
        <p class="text-gray-700 mb-6">Kirim permintaan konfirmasi ke admin untuk mengubah password.</p>

        <form action="{{ route('sales.updatePassword') }}" method="POST">
            @csrf

            <input 
                type="password" 
                name="new_password" 
                placeholder="Masukkan password baru"
                class="w-full border p-2 rounded mb-3"
                minlength="8"
                required
            >

            <button 
                type="submit"
                class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition mb-2"
            >
                Ganti Password
            </button>
        </form>

        <button onclick="closeConfirmPopup()" 
            class="w-full bg-gray-300 py-2 rounded-lg hover:bg-gray-400 transition">
            Tutup
        </button>
    </div>
</div>

{{-- POPUP HANYA ADMIN --}}
<div id="adminOnlyPopup" class="fixed inset-0 bg-black bg-opacity-40 hidden flex items-center justify-center z-[9999]">
    <div class="bg-white p-6 w-80 rounded-2xl shadow-lg text-center">
        <h3 class="text-lg font-semibold mb-3">Akses Ditolak</h3>
        <p class="text-gray-700 mb-6">Hanya admin yang dapat membuka halaman ini.</p>
        <button onclick="closeAdminPopup()" 
            class="w-full bg-gray-300 py-2 rounded-lg hover:bg-gray-400 transition">
            Tutup
        </button>
    </div>
</div>

{{-- SCRIPT POPUP --}}
<script>
    function openConfirmPopup() {
        document.getElementById('confirmPopup').classList.remove('hidden');
    }
    function closeConfirmPopup() {
        document.getElementById('confirmPopup').classList.add('hidden');
    }

    function openAdminPopup() {
        document.getElementById('adminOnlyPopup').classList.remove('hidden');
    }
    function closeAdminPopup() {
        document.getElementById('adminOnlyPopup').classList.add('hidden');
    }
</script>

@endsection
