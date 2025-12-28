@extends('layouts.app')
@section('title', 'Setting')

@php
    $hideNavbar = true; // sembunyikan navbar default di layout
@endphp

@section('content')
<div class="min-h-screen bg-[#F5EEEB] pb-28">

    {{-- HEADER CUSTOM --}}
    <div class="fixed top-0 left-0 w-full bg-[#2A4156] text-white py-4 px-6 shadow-md z-50">
        <div class="flex justify-between items-center">
<a href="/admin/dashboard" 
   class="flex items-center space-x-2 hover:text-gray-200 transition">
    <i class="fas fa-arrow-left text-lg"></i>
    <span class="text-sm font-medium">Kembali</span>
</a>

            <h1 class="text-lg font-semibold">Setting</h1>
            <span></span> {{-- Placeholder supaya justify-between rapi --}}
        </div>
    </div>

    {{-- KONTEN UTAMA --}}
    <div class="pt-28 md:pt-32 px-4">
        
        {{-- PROFIL USER --}}
        <div class="bg-[#2A4156] text-white p-6 rounded-3xl shadow-md mb-4 flex items-center space-x-4">
            <form action="{{ route('profile.update.photo') }}" method="POST" enctype="multipart/form-data" class="relative">
                @csrf
                @method('PUT')
                <label for="photo">
                    <img src="{{ Auth::user()->images ? asset('storage/' . Auth::user()->images) : asset('images/logo-dhs.png') }}" 
                         class="w-16 h-16 rounded-lg shadow-md object-cover cursor-pointer" 
                         alt="Foto Profil">

                    <span class="absolute bottom-0 right-0 bg-white text-gray-700 p-1 rounded-full shadow cursor-pointer">
                        <i class="fas fa-camera"></i>
                    </span>
                    <input type="file" name="photo" id="photo" class="hidden" onchange="this.form.submit()">
                </label>
            </form>
            <div>
                <h2 class="text-2xl font-semibold">{{ Auth::user()->name ?? 'Nama Pengguna' }}</h2>
                <p class="text-sm opacity-90">{{ Auth::user()->email ?? '-' }}</p>
                <span class="bg-green-600 text-xs px-3 py-1 rounded-full uppercase">
                    {{ Auth::user()->role ?? 'USER' }}
                </span>
            </div>
        </div>

        {{-- BAGIAN AKUN --}}
        <div class="mb-4">
            <h3 class="text-gray-600 font-semibold mb-2">Akun</h3>
            <button onclick="togglePasswordForm()" 
                    class="w-full flex justify-between items-center bg-white p-3 rounded-xl shadow-sm hover:bg-gray-100">
                <span>Ganti Password</span>
                <i class="fas fa-chevron-right text-gray-400"></i>
            </button>

            {{-- FORM GANTI PASSWORD --}}
            <div id="passwordForm" class="hidden mt-4 bg-white p-4 rounded-xl shadow-sm">
                <form action="{{ route('password.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="block text-gray-700 text-sm font-medium mb-1">Password Lama</label>
                        <input type="password" name="current_password" class="w-full border rounded-lg px-3 py-2" required>
                    </div>
                    <div class="mb-3">
                        <label class="block text-gray-700 text-sm font-medium mb-1">Password Baru</label>
                        <input type="password" name="password" class="w-full border rounded-lg px-3 py-2" required>
                    </div>
                    <div class="mb-3">
                        <label class="block text-gray-700 text-sm font-medium mb-1">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="w-full border rounded-lg px-3 py-2" required>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg font-medium hover:bg-blue-700 transition">
                        Simpan Password
                    </button>
                </form>
            </div>
        </div>

        {{-- BAGIAN ODC DAN ODP --}}
        <div class="mb-4">
            <h3 class="text-[#2A4156] font-semibold mb-2">ODC dan ODP</h3>

            <a href="{{ route('odc.index') }}" class="flex justify-between items-center bg-white p-3 mb-2 rounded-xl shadow-sm hover:bg-gray-100">
    <div class="flex items-center space-x-2">
        <i class="fas fa-network-wired text-blue-500"></i>
        <span>ODC</span>
    </div>
    <i class="fas fa-chevron-right text-gray-400"></i>
</a>


<a href="{{ route('odp.index') }}" 
   class="flex justify-between items-center bg-white p-3 rounded-xl shadow-sm hover:bg-gray-100">
    <div class="flex items-center space-x-2">
        <i class="fas fa-network-wired text-purple-500"></i>
        <span>ODP</span>
    </div>
    <i class="fas fa-chevron-right text-gray-400"></i>
</a>

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

            <a href="{{ route('history.lunas') }}" 
               class="flex justify-between items-center bg-white p-3 rounded-xl mb-2 shadow-sm hover:bg-gray-100 text-red-500 font-medium">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-trash"></i>
                    <span>History Hapus Lunas Bayar</span>
                </div>
            </a>

            <a href="{{ route('setting.wa_template') }}" 
   class="flex justify-between items-center bg-white p-3 rounded-xl mb-2 shadow-sm hover:bg-gray-100 text-[#2A4156] font-medium">
    <div class="flex items-center space-x-2">
        <i class="fas fa-comment-dots text-green-600"></i>
        <span>Template WA Tagihan</span>
    </div>
    <i class="fas fa-chevron-right text-gray-400"></i>
</a>


            <a href="#" class="flex justify-between items-center bg-white p-3 rounded-xl mb-2 shadow-sm hover:bg-gray-100 text-[#2A4156] font-medium">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-info-circle text-blue-600"></i>
                    <span>Info Penting Pake Banget</span>
                </div>
            </a>

            <a href="#" class="flex justify-between items-center bg-white p-3 rounded-xl shadow-sm hover:bg-gray-100 text-gray-700 font-medium">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-bars"></i>
                    <span>Log Develop</span>
                </div>
            </a>
        </div>

    </div>

    {{-- LOGOUT BUTTON FIXED --}}
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

<script>
function togglePasswordForm() {
    const form = document.getElementById('passwordForm');
    form.classList.toggle('hidden');
}
</script>
@endsection


{{-- ======================= BOTTOM MENU MOBILE ======================= --}}
@section('bottom-menu')
<div class="flex justify-between gap-2 p-2 bg-white shadow-inner border-t fixed bottom-0 left-0 right-0 z-50">
     @php
        $mobileCards = [
            ['icon'=>'🏠','title'=>'Beranda','link'=>route('admin.dashboard')],
            ['icon'=>'💳','title'=>'Transaksi','link'=>route('admin.transaksi_cash')],
            ['icon'=>'💰','title'=>'Bayar','link'=>route('admin.belum_bayar')],
            ['icon'=>'📩','title'=>'Pengaduan','link'=>route('pengaduan.index')],
            ['icon'=>'⚙️','title'=>'Setting','link'=>route('setting')],
        ];
    @endphp

    @foreach($mobileCards as $card)
    <a href="{{ $card['link'] }}" 
       class="flex-1 min-w-[60px] max-w-[80px] flex flex-col items-center justify-center text-[var(--primary-color)] text-xs bg-white p-2 rounded-lg shadow-sm hover:shadow-md hover:bg-[var(--accent-color)] hover:text-white transition transform hover:-translate-y-1 duration-200">
        <div class="text-xl mb-1">{{ $card['icon'] }}</div>
        <span class="text-center">{{ $card['title'] }}</span>
    </a>
    @endforeach
</div>
@endsection

