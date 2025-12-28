@extends('layouts.app')

@section('title', 'Detail Tagihan Lunas')

@section('content')
<div class="pt-24 pb-20 px-3 sm:px-10 max-w-4xl mx-auto">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-800">Tagihan Lunas</h2>

        <a href="{{ route('sales.pelanggan.lunas') }}"
           class="bg-white border shadow-sm hover:bg-gray-50 text-gray-800 px-4 py-2 rounded-xl text-sm transition">
            Kembali
        </a>
    </div>

    {{-- Card Data Pelanggan --}}
    <div class="bg-white rounded-2xl shadow p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-2">{{ $pelanggan->name }}</h3>

        <p class="text-sm text-gray-600">
            <strong>Paket:</strong> {{ $pelanggan->package->name ?? '-' }}
        </p>

        <p class="text-sm text-gray-600">
            <strong>Area:</strong> {{ $pelanggan->area->name ?? '-' }}
        </p>

        <p class="text-sm text-gray-600">
            <strong>No HP:</strong> {{ $pelanggan->phone ?? '-' }}
        </p>

        <p class="text-sm text-gray-600">
            <strong>IP Address:</strong> {{ $pelanggan->device->ip_address ?? '-' }}
        </p>
    </div>

    {{-- ===================== LIST TAGIHAN ===================== --}}
    <div class="grid gap-4">

        @forelse ($tagihan as $trx)

            @php
                // Fix bulan "2026-02,2026-03"
                $bulanFix = explode(',', $trx->bulan)[0];
                $tanggalFix = $bulanFix . '-01';
                $formatBulan = \Carbon\Carbon::parse($tanggalFix)->translatedFormat('F Y');
            @endphp

            <div class="bg-white rounded-2xl shadow p-5 hover:shadow-md transition">

                <div class="flex justify-between items-center">
                    <p class="text-lg font-bold text-gray-800">
                        {{ $formatBulan }}
                    </p>

                    <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700 font-semibold">
                        LUNAS
                    </span>
                </div>

                <p class="text-sm text-gray-600 mt-3">
                    Total Tagihan:
                    <span class="font-bold text-green-700">
                        Rp {{ number_format($trx->total_tagihan, 0, ',', '.') }}
                    </span>
                </p>

                <p class="text-xs text-gray-500 mt-1">
                    Dibayar pada:
                    <strong>
                        {{ $trx->tanggal_bayar ? \Carbon\Carbon::parse($trx->tanggal_bayar)->format('d-m-Y H:i') : '-' }}
                    </strong>
                </p>

            </div>

        @empty

            <div class="text-center text-gray-500 py-10 text-lg">
                Tidak ada tagihan lunas.
            </div>

        @endforelse

    </div>

</div>
@endsection



{{-- ======================= BOTTOM MENU ======================= --}}
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
            class="flex-1 flex flex-col items-center text-xs bg-white p-2 rounded-lg">
            <div class="text-xl mb-1">{{ $card['icon'] }}</div>
            <span>{{ $card['title'] }}</span>
        </a>
    @endforeach
</div>
@endsection
