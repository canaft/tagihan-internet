@extends('layouts.app')
@section('title', 'Data Pelanggan Teknisi')

@section('content')
<div class="pt-24 pb-20 px-4 sm:px-10 max-w-7xl mx-auto">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h2 class="text-3xl font-bold text-gray-900">Daftar Pelanggan</h2>
            <a href="{{ route('kangteknisi.dashboard') }}" 
                class="mt-2 inline-flex bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg items-center gap-2 text-sm font-medium transition">
                <i class="fa fa-arrow-left"></i> Kembali
            </a>
        </div>

        {{-- Search --}}
        <input type="text" id="searchInput" placeholder="Cari pelanggan..." 
            class="border border-gray-300 rounded-lg px-4 py-2 text-sm w-full sm:w-64 focus:outline-none focus:ring-2 focus:ring-blue-400">
    </div>

    {{-- Grid Pelanggan --}}
    <div id="pelangganGrid" class="grid gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3">

        @foreach ($pelanggans as $pelanggan)

            <a href="{{ route('kangteknisi.pelanggan.detail', $pelanggan->id) }}" 
                class="pelanggan-card block bg-white rounded-2xl shadow-md hover:shadow-xl transition transform hover:scale-105 border border-gray-100 overflow-hidden"
                data-name="{{ strtolower($pelanggan->name) }}">
                
                <div class="p-5 flex flex-col justify-between h-full">
                    {{-- Nama --}}
                    <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $pelanggan->name }}</h3>

                    {{-- Info --}}
                    <div class="mt-3 space-y-1 text-gray-600 text-sm">
                        <p class="flex items-center gap-1"><i class="fa fa-phone text-gray-400"></i> {{ $pelanggan->phone ?? '-' }}</p>
                        <p class="flex items-center gap-1"><i class="fa fa-network-wired text-gray-400"></i> {{ $pelanggan->device->ip_address ?? '-' }}</p>
                        <p class="flex items-center gap-1"><i class="fa fa-box text-gray-400"></i> Paket: {{ $pelanggan->package->nama_paket ?? '-' }}</p>
                        <p class="flex items-center gap-1"><i class="fa fa-calendar text-gray-400"></i> {{ \Carbon\Carbon::parse($pelanggan->tanggal_register)->format('d M Y') }}</p>
                    </div>

                    {{-- STATUS / BULAN LUNAS --}}
                    <div class="mt-4">
                        @php
                            $bulanLunas = [];

                            foreach ($pelanggan->transaksi as $trx) {
                                if ($trx->bulan) {
                                    $bulanArray = explode(',', $trx->bulan);
                                    foreach ($bulanArray as $b) {
                                        $b = trim($b);
                                        if ($b) $bulanLunas[] = $b;
                                    }
                                }
                            }

                            $bulanLunas = collect($bulanLunas)
                                ->unique()
                                ->sort()
                                ->values();
                        @endphp

                        @if ($bulanLunas->isEmpty())
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                Belum Pernah Bayar
                            </span>
                        @else
                            <div class="flex flex-wrap gap-1">
                                @foreach ($bulanLunas as $bulan)
                                    @php
                                        try {
                                            $formatted = \Carbon\Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('M Y');
                                        } catch (\Exception $e) {
                                            $formatted = $bulan; // fallback kalau parsing gagal
                                        }
                                    @endphp

                                    <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        {{ $formatted }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                </div>
            </a>

        @endforeach

    </div>
</div>

<script>
const searchInput = document.getElementById('searchInput');
const cards = document.querySelectorAll('.pelanggan-card');

searchInput.addEventListener('input', function() {
    const key = this.value.toLowerCase();

    cards.forEach(card => {
        const name = card.dataset.name;
        card.style.display = name.includes(key) ? '' : 'none';
    });
});
</script>

@endsection
