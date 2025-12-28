@extends('layouts.app')

@section('title', 'Daftar Izin Bulan ' . DateTime::createFromFormat('!m', $bulan)->format('F'))

@section('navbar')
<nav class="fixed top-0 left-0 w-full bg-[var(--primary-color)] text-white px-4 py-3 flex items-center shadow-md z-50">
    <a href="{{ route('admin.absen.history') }}" class="mr-3 text-lg">
        <i class="fas fa-arrow-left"></i>
    </a>
    <p class="font-semibold text-base">Izin {{ $user->name }} - {{ DateTime::createFromFormat('!m', $bulan)->format('F') }} {{ $tahun }}</p>
</nav>
@endsection

@section('content')
<div class="pt-20 pb-32 min-h-screen bg-gray-50">

    <div class="mx-4 bg-white p-4 rounded-2xl shadow border">
        <p class="text-gray-500 text-sm">Teknisi:</p>
        <p class="text-[20px] font-semibold capitalize">{{ $user->name }}</p>
        <p class="text-gray-500 text-xs">Role: {{ $user->role }}</p>
    </div>

    <div class="px-4 mt-6">
        @forelse ($izin as $item)
            <div class="bg-white p-4 rounded-2xl shadow flex justify-between items-center mt-4">

                <div>
                    <p class="text-gray-700 font-semibold">{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('l, d M Y') }}</p>
                    <p class="text-gray-600 text-sm">Alasan: {{ $item->alasan }}</p>
                </div>

                <div class="flex gap-2">
                    @if($item->status === 'pending')
                        <form action="{{ route('admin.izin.setujui', $item->id) }}" method="POST">
                            @csrf
                            <button class="bg-green-500 text-white px-3 py-1 rounded">Setujui</button>
                        </form>
                        <form action="{{ route('admin.izin.tolak', $item->id) }}" method="POST">
                            @csrf
                            <button class="bg-red-500 text-white px-3 py-1 rounded">Tolak</button>
                        </form>
                    @else
                        <span class="px-3 py-1 rounded 
                            {{ $item->status=='disetujui' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ ucfirst($item->status) }}
                        </span>
                    @endif
                </div>

            </div>
        @empty
            <p class="text-center text-gray-500 mt-10">Belum ada izin untuk bulan ini.</p>
        @endforelse
    </div>

</div>
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