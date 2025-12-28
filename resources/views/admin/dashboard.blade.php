@extends('layouts.app')

@section('title', 'Dashboard Admin')

{{-- NAVBAR FIXED DI ATAS --}}
@section('navbar')
<nav class="fixed top-0 left-0 w-full z-50 bg-[var(--primary-color)] text-white p-4 flex justify-between items-center shadow-md">
    <div class="text-xl font-bold">@yield('app-name', 'Tagihan Internet')</div>

    <!-- Logout Desktop -->
    <form method="POST" action="{{ route('logout') }}" class="hidden lg:block">
        @csrf
        <button type="submit" class="bg-[var(--accent-color)] px-4 py-2 rounded-lg font-semibold hover:opacity-90 transition">
            Logout
        </button>
    </form>
</nav>
@endsection


{{-- ======================= BOTTOM MENU MOBILE ======================= --}}
@section('bottom-menu')
<div class="flex justify-between gap-2 p-2 bg-white shadow-inner border-t fixed bottom-0 left-0 right-0 z-50">
     @php
        $mobileCards = [
            ['icon'=>'🏠','title'=>'Beranda','link'=>route('admin.dashboard')],
            ['icon'=>'💳','title'=>'Transaksi','link'=>route('admin.pelanggan_lunas')],
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


{{-- KONTEN UTAMA --}}
@section('content')
<div class="pt-24"></div>

{{-- HEADER INFO AKUN --}}
<div class="bg-[var(--primary-color)] text-white rounded-2xl p-4 sm:p-6 shadow-md mb-6">
    <div class="flex justify-between items-start">
        <div>
            <div class="text-sm opacity-90">{{ \Carbon\Carbon::now()->translatedFormat('d M Y') }}</div>
            <div class="text-lg font-semibold mt-1">Hai, {{ Auth::user()->name ?? 'Admin' }}</div>
            <div class="text-sm opacity-90">DHS ({{ Auth::user()->id ?? '00000' }})</div>
        </div>
        <div class="flex items-center text-xl gap-4">
           @php
    $notifCount = \App\Models\Notification::where('admin_id', Auth::id())
                    ->where('is_read', false)
                    ->count();
@endphp

<div class="relative cursor-pointer">
    <a href="{{ route('admin.notif.index') }}" class="hover:opacity-80">
        <i class="fas fa-bell text-xl"></i>

        @if($notifCount > 0)
            <span class="absolute -top-1 -right-1 bg-red-600 text-white text-xs px-1.5 py-0.5 rounded-full">
                {{ $notifCount }}
            </span>
        @endif
    </a>
</div>

            <a href="{{ route('setting') }}" class="cursor-pointer hover:opacity-80 transition">
                <i class="fas fa-cog"></i>
            </a>
        </div>
    </div>

{{-- Info Pembayaran --}}
<div class="bg-white text-[var(--primary-color)] rounded-xl mt-4 p-3 flex justify-between items-center shadow-sm">
    <div>
        <div class="font-medium">Pembayaran Diterima</div>
        <div class="text-xs text-gray-500">Total Lunas</div>
    </div>
    <div class="font-bold text-[var(--accent-color)]">
Rp {{ number_format($totalPemasukan ?? 0, 0, ',', '.') }}
    </div>
</div>



    <!-- {{-- Ringkasan Keuangan --}}
    <div class="flex justify-around mt-4">
        <div class="text-center">
            <div class="text-sm">Pemasukkan</div>
            <div class="text-lg font-semibold"></div>
        </div>
        <div class="text-center">
            <div class="text-sm">Pengeluaran</div>
            <div class="text-lg font-semibold">Rp. 0</div>
        </div>
    </div> -->
</div>

{{-- MENU GRID --}}
<div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 gap-4 mb-6 max-w-6xl mx-auto">
 @php
    $cards = [
        ['icon'=>'👤','title'=>'Pelanggan','desc'=>'Data pelanggan aktif','link'=>route('pelanggan.index')],
        ['icon'=>'📦','title'=>'Paket','desc'=>'Kelola paket internet','link'=>'paket'],
        ['icon'=>'📩','title'=>'Pengaduan','desc'=>'Laporan gangguan pelanggan','link'=>'pengaduan'],
        ['icon'=>'🧑‍💼','title'=>'User','desc'=>'Kelola user, teknisi & sales','link'=>route('admin.user.index')],
        ['icon'=>'🕒','title'=>'History Absen','desc'=>'Riwayat absen teknisi','link'=>route('admin.absen.history')],
[
    'icon'  => '🗺️',
    'title' => 'Area',
    'desc'  => 'Kelola data area',
    'link'  => route('admin.areas.index') // pakai route name admin.*
],
    ];
@endphp

    @foreach($cards as $card)
    <a href="{{ $card['link'] }}" 
       class="menu-card group bg-white p-4 sm:p-5 rounded-2xl flex flex-col items-center text-center shadow-sm hover:shadow-lg transition transform hover:-translate-y-1 hover:scale-105 duration-300">
        <div class="bg-[var(--accent-color)] text-white rounded-full p-3 sm:p-4 mb-2 sm:mb-3 text-2xl sm:text-3xl transition-all duration-300 group-hover:scale-110">
            {{ $card['icon'] }}
        </div>
        <h3 class="font-semibold text-sm sm:text-lg group-hover:text-[var(--accent-color)] transition-colors duration-300">
            {{ $card['title'] }}
        </h3>
        <p class="text-xs sm:text-sm text-gray-500 mt-1">{{ $card['desc'] }}</p>
    </a>
    @endforeach
</div>

@endsection
