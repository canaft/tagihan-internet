@extends('layouts.app')
@section('title', 'Struk Pembayaran')

@section('content')
<div class="pt-24 pb-20 px-3 sm:px-10 max-w-xl mx-auto">

    {{-- Tombol Kembali --}}
    <div class="mb-6">
        <a href="{{ route('admin.pelanggan_lunas') }}" 
           class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-3 py-2 rounded flex items-center gap-1 text-sm">
            <i class="fa fa-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- Struk Card --}}
    <div class="bg-white rounded-2xl shadow p-6 text-gray-800">
        <h2 class="text-xl font-bold mb-4">Struk Pembayaran</h2>

        {{-- Info Pelanggan --}}
        <div class="mb-4 border-b pb-3">
            <p><span class="font-semibold">Nama:</span> {{ $pelanggan->name }}</p>
            <p><span class="font-semibold">No. HP:</span> {{ $pelanggan->phone ?? '-' }}</p>
            <p><span class="font-semibold">Area:</span> {{ $pelanggan->area->nama_area ?? '-' }}</p>
            <p><span class="font-semibold">IP Device:</span> {{ $pelanggan->device->ip_address ?? '-' }}</p>
            <p><span class="font-semibold">Tanggal Tagihan:</span> {{ \Carbon\Carbon::parse($pelanggan->tanggal_tagihan)->format('d-m-Y') }}</p>
        </div>

        {{-- Detail Pembayaran --}}
        <div class="mb-4">
            <h3 class="font-semibold mb-2">Rincian Pembayaran:</h3>
            <div class="flex justify-between mb-1">
                <span>Paket</span>
                <span>Rp {{ number_format($pelanggan->paket->harga ?? 0, 0, ',', '.') }}</span>
            </div>
            @if($pelanggan->biaya_tambahan_1)
            <div class="flex justify-between mb-1">
                <span>Biaya Tambahan 1</span>
                <span>Rp {{ number_format($pelanggan->biaya_tambahan_1, 0, ',', '.') }}</span>
            </div>
            @endif
            @if($pelanggan->biaya_tambahan_2)
            <div class="flex justify-between mb-1">
                <span>Biaya Tambahan 2</span>
                <span>Rp {{ number_format($pelanggan->biaya_tambahan_2, 0, ',', '.') }}</span>
            </div>
            @endif
            <hr class="my-2">
            <div class="flex justify-between font-bold text-gray-800">
                <span>Total</span>
                <span>Rp {{ number_format(($pelanggan->paket->harga ?? 0) + ($pelanggan->biaya_tambahan_1 ?? 0) + ($pelanggan->biaya_tambahan_2 ?? 0), 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Status Pembayaran --}}
        <div class="text-center mt-4">
            <span class="inline-block bg-green-100 text-green-700 px-3 py-1 rounded-full font-semibold">
                LUNAS
            </span>
        </div>

        {{-- Tombol Cetak --}}
        <div class="text-center mt-6">
            <button onclick="window.print()" 
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                Cetak Struk
            </button>
        </div>
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
        