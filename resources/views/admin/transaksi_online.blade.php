@extends('layouts.app')
@section('title', 'Transaksi Online')

@section('content')
<div class="pt-24 pb-20">

    <h2 class="text-lg font-bold mb-4">Transaksi Online</h2>

    @foreach($transaksiOnline as $trx)
    <div class="bg-white rounded-xl shadow p-3 mb-3 flex flex-col">
        <div class="flex justify-between items-start">
            <div>
                <div class="font-semibold text-gray-800">{{ $trx->pelanggan->nama ?? '-' }}</div>
                <div class="text-sm text-gray-500">
                    {{ $trx->pelanggan->no_hp ?? '-' }} • {{ $trx->pelanggan->rw ?? '-' }}
                </div>
                @if($trx->pelanggan->paket)
                    <span class="inline-block bg-yellow-100 text-yellow-700 px-2 py-1 text-xs rounded-full mt-1">
                        {{ $trx->pelanggan->paket }}
                    </span>
                @endif
            </div>
            <div class="text-right">
                <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs font-semibold">
                    {{ $trx->kode_transaksi }}
                </span>
                <div class="text-lg font-bold text-gray-800">
                    Rp {{ number_format($trx->jumlah, 0, ',', '.') }}
                </div>
                <div class="text-xs text-green-600">- Dskn : Rp. {{ number_format($trx->diskon ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>

        <div class="flex justify-between items-center text-xs mt-2 text-gray-600">
            <div>
                <span class="font-semibold">Pembayaran By :</span> {{ strtoupper($trx->dibayar_oleh ?? '-') }}
            </div>
            <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full">
                {{ strtoupper(\Carbon\Carbon::parse($trx->bulan)->translatedFormat('M Y')) }}
            </span>
        </div>

        <div class="flex justify-between items-center mt-1 text-xs text-gray-500">
            <span>Tgl Bayar : {{ \Carbon\Carbon::parse($trx->tanggal_bayar)->translatedFormat('d M Y • H:i') }} WIB</span>
            <div class="flex items-center gap-2">
                @if($trx->ip_address)
                    <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">
                        arp@{{ $trx->ip_address }}
                    </span>
                @endif
                <i class="fa fa-map-marker text-orange-400"></i>
                <i class="fa fa-file text-blue-400"></i>
            </div>
        </div>
    </div>
    @endforeach

</div>
@endsection
