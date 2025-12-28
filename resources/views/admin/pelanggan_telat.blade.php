@extends('layouts.app')
@section('title', 'Pelanggan Telat')

@section('content')
<div class="pt-24 pb-20">
    <h2 class="text-lg font-bold mb-4">Pelanggan Telat Bayar</h2>

    @forelse($pelangganTelat as $p)
    <div class="bg-white rounded-lg shadow p-3 mb-2 flex justify-between">
        <div>
            <div class="font-semibold">{{ $p->nama }}</div>
            <div class="text-xs text-gray-500">{{ $p->no_hp }} • {{ $p->rw }}</div>
        </div>
        <div class="text-right text-sm text-yellow-600">Telat Bayar</div>
    </div>
    @empty
        <p class="text-center text-gray-500">Tidak ada pelanggan yang telat bayar bulan ini.</p>
    @endforelse
</div>
@endsection
