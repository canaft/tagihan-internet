@extends('layouts.app')
@section('title', 'Transaksi Cash')

@section('content')
<div class="pt-24 pb-20 px-3 sm:px-10 max-w-6xl mx-auto">

  {{-- Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">

    {{-- Title --}}
    <h2 class="text-2xl font-bold text-gray-800">
        Transaksi Cash
    </h2>

    {{-- Action Bar (Back + Filter) --}}
    <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">

        {{-- Tombol Kembali --}}
        <a href="{{ route('pelanggan.index') }}"
           class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg text-sm w-full sm:w-auto text-center">
            <i class="fa fa-arrow-left mr-1"></i> Kembali
        </a>

        {{-- Filter Bulan --}}
        <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-2 w-full sm:w-auto">

            @php $bulanValue = $bulan ?? now()->format('Y-m'); @endphp

            <input
                type="month"
                name="bulan"
                value="{{ $bulanValue }}"
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-full sm:w-auto
                       focus:ring-blue-500 focus:border-blue-500"
            >

            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition whitespace-nowrap">
                Filter
            </button>
        </form>

    </div>

  </div>

    {{-- Total pemasukan bulan --}}
    <div class="bg-green-50 border border-green-200 rounded-xl p-4 shadow-sm mb-6">
        <p class="text-green-800 font-semibold">
            Total Pemasukan Bulan 
            <span class="font-bold">
                {{ \Carbon\Carbon::parse(($bulan ?? now()->format('Y-m')) . '-01')->translatedFormat('F Y') }}
            </span> :
            <span class="text-xl font-bold">Rp {{ number_format($totalPemasukan ?? 0, 0, ',', '.') }}</span>
        </p>
    </div>

    {{-- Grid Transaksi --}}
    <div id="pelangganGrid" class="grid gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3">

        @forelse($transaksiCash as $trx)

            @php
                $pelanggan = $trx->pelanggan;

                // Nominal transaksi
                $amount = $trx->jumlah ?? 0;
                if (!$amount) {
                    $harga = $pelanggan->package->harga ?? 0;
                    $b1 = $pelanggan->biaya_tambahan_1 ?? 0;
                    $b2 = $pelanggan->biaya_tambahan_2 ?? 0;
                    $diskon = $pelanggan->diskon ?? 0;
                    $amount = ($harga + $b1 + $b2) - (($harga + $b1 + $b2) * $diskon / 100);
                }

                // Format multi bulan
                if ($trx->bulan) {
                    $bulanArray = explode(',', $trx->bulan);
                    $bulanBayarText = collect($bulanArray)->map(function($b) {
                        return \Carbon\Carbon::parse($b . '-01')->translatedFormat('F Y');
                    })->join(', ');
                } else {
                    $bulanBayarText = '-';
                }
            @endphp

            <div class="bg-white rounded-xl shadow hover:shadow-md transition transform hover:scale-[1.01] p-5 border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 truncate">{{ $pelanggan->name }}</h3>

                <p class="text-sm text-gray-500">
                    {{ $pelanggan->phone ?? '-' }} • {{ $pelanggan->area->nama_area ?? '-' }}
                </p>

                <p class="text-sm text-gray-500">IP: {{ $pelanggan->device->ip_address ?? '-' }}</p>

                <p class="text-sm text-gray-600 mt-3">
                    <span class="font-semibold text-gray-700">Tanggal Bayar:</span>
                    {{ $trx->tanggal_bayar ? \Carbon\Carbon::parse($trx->tanggal_bayar)->format('d-m-Y') : '-' }}
                </p>

                <p class="text-sm font-semibold text-blue-600 mt-1">
                    Bayar untuk: {{ $bulanBayarText }}
                </p>

                <div class="flex justify-between items-center mt-4">
                    <span class="text-xs font-semibold px-2 py-1 rounded-full bg-green-100 text-green-700">
                        Cash
                    </span>

                    <span class="text-base font-bold text-gray-800">
                        Rp {{ number_format($amount, 0, ',', '.') }}
                    </span>
                </div>
            </div>

        @empty
            <div class="text-center text-gray-500 col-span-full py-10">
                Tidak ada transaksi cash untuk bulan ini.
            </div>
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
