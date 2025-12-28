@extends('layouts.app')
@section('title', 'Belum Bayar')

@section('content')
<div class="pt-24 pb-20 px-3 sm:px-10 max-w-6xl mx-auto">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">

        <h2 class="text-2xl font-bold text-gray-800">Belum Bayar</h2>

        <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">

            <a href="{{ route('sales.dashboard') }}" 
               class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg text-sm flex items-center gap-1 w-full sm:w-auto justify-center">
                <i class="fa fa-arrow-left"></i> Kembali
            </a>

            <input 
                type="text" 
                id="searchInput" 
                placeholder="Cari pelanggan..." 
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-full sm:w-56 focus:ring-blue-500 focus:border-blue-500"
            >

            {{-- BUTTON FILTER --}}
            <button id="openFilter" 
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-1 w-full sm:w-auto justify-center">
                <i class="fa fa-filter"></i> Filter
            </button>
        </div>

    </div>

    {{-- BUILD $pelanggans --}}
    @php
        $pelanggans = collect($tagihan ?? collect())
            ->map(fn($t) => $t->pelanggan ?? null)
            ->filter()
            ->unique('id')
            ->values();
    @endphp

    {{-- GRID --}}
    <div id="pelangganGrid" class="grid gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3">

        @forelse ($pelanggans as $pelanggan)

            @php
                $transaksi = $pelanggan->transaksis ?? collect();
                $bulanSudahBayar = collect();

                foreach ($transaksi as $trx) {
                    if (!$trx->bulan) continue;
                    foreach (explode(',', $trx->bulan) as $b)
                        $bulanSudahBayar->push(\Carbon\Carbon::parse($b . '-01')->format('Y-m'));
                }

                $tagihanBelum = $pelanggan->tagihans->filter(fn($t) =>
                    $t->status === 'belum_lunas' &&
                    !$bulanSudahBayar->contains(\Carbon\Carbon::parse($t->bulan)->format('Y-m'))
                );

                $tagihanPertama = $tagihanBelum->sortBy('bulan')->first();
            @endphp

            @if (!$tagihanPertama) @continue @endif

<a href="{{ route('sales.tagihan.detail', $tagihanPertama->id) }}"
               class="pelanggan-card block bg-white rounded-2xl shadow hover:shadow-lg transition hover:-translate-y-1 flex flex-col justify-between transform hover:scale-[1.02]"
               data-name="{{ strtolower($pelanggan->name) }}"
               data-phone="{{ strtolower($pelanggan->phone ?? '') }}"
               data-area="{{ strtolower($pelanggan->area->nama_area ?? '') }}"
               data-paket="{{ strtolower($pelanggan->package->nama_paket ?? '') }}"
               data-bulan="{{ \Carbon\Carbon::parse($tagihanPertama->bulan)->format('Y-m') }}"
               data-tanggal="{{ \Carbon\Carbon::parse($tagihanPertama->tanggal_tagihan)->format('Y-m-d') }}"
               style="min-height: 190px;">

                <div class="p-4 flex flex-col justify-between h-full">
                    <div>

                        <h3 class="text-base font-semibold text-gray-800 truncate">
                            {{ $pelanggan->name }}
                        </h3>

                        <p class="text-xs text-gray-500 mt-1 truncate">
                            📞 {{ $pelanggan->phone ?? '-' }} • {{ $pelanggan->area->nama_area ?? '-' }}
                        </p>

                        <p class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded inline-block mt-1 truncate">
                            🌐 {{ $pelanggan->device->ip_address ?? '-' }}
                        </p>

                        <p class="text-xs text-gray-700 mt-1">
                            📅 {{ \Carbon\Carbon::parse($tagihanPertama->tanggal_tagihan)->format('d-m-Y') }}
                        </p>

                    </div>

                    <div class="mt-3 text-right">
                        <span class="inline-block bg-blue-100 text-blue-700 text-xs font-semibold px-2 py-1 rounded-full mb-1">
                            DHS {{ $pelanggan->id }}
                        </span>

                        <div class="text-lg font-bold text-gray-800">
                            Rp {{ number_format($tagihanPertama->jumlah, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                <div class="p-2 text-center bg-gray-50 text-xs text-gray-600 rounded-b-2xl">
                    <span class="font-semibold">Bulan Tagihan</span> <br>
                    {{ \Carbon\Carbon::parse($tagihanPertama->bulan)->translatedFormat('F Y') }}
                </div>
            </a>

        @empty
            {{-- Kosong, tetap grid --}}
        @endforelse

        {{-- NO RESULTS --}}
        <div class="text-center text-gray-500 col-span-full py-10 hidden" id="noResults">
            Tidak ada pelanggan yang sesuai filter.
        </div>

    </div>
</div>

{{-- FILTER MODAL --}}
<div id="filterModal" 
     class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-[9999] px-4">
    <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md relative">

        <button id="closeFilter" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-xl">✖</button>
        <h2 class="text-xl font-bold mb-3 text-gray-800">Filter Pelanggan</h2>

        <div class="space-y-3 text-sm text-gray-700">

            <label>
                <span class="font-semibold">Area</span>
                <select id="filterArea" class="border rounded-xl w-full px-3 py-2 mt-1">
                    <option value="">Semua</option>
                    @foreach(\App\Models\Area::all() as $area)
                        <option value="{{ strtolower($area->nama_area) }}">{{ $area->nama_area }}</option>
                    @endforeach
                </select>
            </label>

            <label>
                <span class="font-semibold">Status Paket</span>
                <select id="filterPaket" class="border rounded-xl w-full px-3 py-2 mt-1">
                    <option value="">Semua</option>
                    @foreach(\App\Models\Package::all() as $package)
                        <option value="{{ strtolower($package->nama_paket) }}">{{ $package->nama_paket }}</option>
                    @endforeach
                </select>
            </label>

            <label>
                <span class="font-semibold">Bulan</span>
                <input type="month" id="filterBulan" class="border rounded-xl w-full px-3 py-2 mt-1">
            </label>

            <label class="opacity-40">
                <span class="font-semibold">Dibayar Oleh</span>
                <select id="filterDibayar" class="border rounded-xl w-full px-3 py-2 mt-1 bg-gray-100 cursor-not-allowed" disabled>
                    <option value="">Tidak Tersedia</option>
                </select>
            </label>

            <div class="flex gap-2">
                <label class="flex-1">
                    <span class="font-semibold">Dari Tanggal</span>
                    <input type="date" id="filterTanggalMin" class="border rounded-xl w-full px-3 py-2 mt-1">
                </label>

                <label class="flex-1">
                    <span class="font-semibold">Sampai Tanggal</span>
                    <input type="date" id="filterTanggalMax" class="border rounded-xl w-full px-3 py-2 mt-1">
                </label>
            </div>

        </div>

    </div>
</div>

{{-- SCRIPT SEARCH + FILTER --}}
<script>
const searchInput = document.getElementById('searchInput');
const cards = document.querySelectorAll('.pelanggan-card');
const noResults = document.getElementById('noResults');

// SEARCH FUNCTION
searchInput.addEventListener('input', applyFilter);

// FILTER MODAL CONTROL
document.getElementById('openFilter').onclick = () => {
    document.getElementById('filterModal').classList.remove('hidden');
    document.getElementById('filterModal').classList.add('flex');
};

document.getElementById('closeFilter').onclick = () => {
    document.getElementById('filterModal').classList.add('hidden');
    document.getElementById('filterModal').classList.remove('flex');
};

// FILTER FUNCTION
function applyFilter() {
    const keyword = searchInput.value.toLowerCase();
    const area = document.getElementById('filterArea').value;
    const paket = document.getElementById('filterPaket').value;
    const bulan = document.getElementById('filterBulan').value;
    const minTgl = document.getElementById('filterTanggalMin').value;
    const maxTgl = document.getElementById('filterTanggalMax').value;

    let visible = 0;

    cards.forEach(card => {
        let match = true;

        const name = card.dataset.name;
        const phone = card.dataset.phone;
        const areaData = card.dataset.area;
        const paketData = card.dataset.paket;
        const bulanData = card.dataset.bulan;
        const tglData = card.dataset.tanggal;

        if (keyword && !name.includes(keyword) && !phone.includes(keyword) && !areaData.includes(keyword))
            match = false;

        if (area && areaData !== area)
            match = false;

        if (paket && paketData !== paket)
            match = false;

        if (bulan && bulanData !== bulan)
            match = false;

        if (minTgl && tglData < minTgl)
            match = false;

        if (maxTgl && tglData > maxTgl)
            match = false;

        card.style.display = match ? "" : "none";
        if (match) visible++;
    });

    noResults.classList.toggle('hidden', visible > 0);
}

// SEMUA FILTER INPUT AUTO APPLY + CLOSE MODAL
['filterArea','filterPaket','filterBulan','filterTanggalMin','filterTanggalMax'].forEach(id => {
    document.getElementById(id).addEventListener('change', () => {
        applyFilter();
        // TUTUP MODAL OTOMATIS
        document.getElementById('filterModal').classList.add('hidden');
        document.getElementById('filterModal').classList.remove('flex');
    });
});
</script>

@endsection
