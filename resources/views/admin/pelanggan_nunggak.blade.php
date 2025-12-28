@extends('layouts.app')
@section('title', 'Pelanggan Nunggak')

@section('content')
<div class="pt-24 pb-20">
    <h2 class="text-lg font-bold mb-4">Pelanggan Belum Bayar (Nunggak)</h2>

    @forelse($pelangganNunggak as $p)
    <div class="bg-white rounded-lg shadow p-3 mb-2 flex justify-between">
        <div>
            <div class="font-semibold">{{ $p->nama }}</div>
            <div class="text-xs text-gray-500">{{ $p->no_hp }} • {{ $p->rw }}</div>
        </div>
        <div class="text-right text-sm text-red-600">Belum Bayar</div>
    </div>
    @empty
        <p class="text-center text-gray-500">Semua pelanggan sudah bayar bulan ini 🎉</p>
    @endforelse
</div>
@endsection
