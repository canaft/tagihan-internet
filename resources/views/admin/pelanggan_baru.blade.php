@extends('layouts.app')
@section('title', 'Pelanggan Baru')

@section('content')
<div class="pt-24 pb-24 px-4 sm:px-10 max-w-6xl mx-auto">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-3xl font-bold text-gray-800 tracking-tight">Pelanggan Baru</h2>
            <p class="text-sm text-gray-500">Daftar pelanggan baru bulan ini</p>
        </div>

        <a href="{{ route('pelanggan.index') }}"
            class="px-4 py-2 bg-[var(--primary-color)] text-white shadow-md rounded-xl hover:bg-opacity-90 transition">
            <i class="fa fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    {{-- SEARCH --}}
{{-- SEARCH --}}
<div class="flex justify-center mb-8">
    <div class="w-full sm:w-80">
        <input type="text" id="searchInput"
            placeholder="Cari nama, nomor HP, area atau paket..."
            class="border border-gray-300 rounded-full py-2 px-4 w-full focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition shadow-sm text-sm">
    </div>
</div>


    {{-- GRID CARD --}}
    <div id="pelangganGrid" class="grid gap-6 grid-cols-1 sm:grid-cols-2 md:grid-cols-3">
        @forelse($pelanggan as $p)
            @php
                $statusLabel = $p->tanggal_aktivasi ? 'Aktifkan Kembali' : 'Daftar Baru';
                $statusColor = $p->tanggal_aktivasi ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700';
                $tgl = $p->tanggal_aktivasi 
                        ? \Carbon\Carbon::parse($p->tanggal_aktivasi)->format('d M Y')
                        : $p->created_at->format('d M Y');
            @endphp

            <a href="{{ route('admin.detail_pelanggan_baru', $p->id) }}"
                class="pelanggan-card bg-white rounded-3xl p-5 shadow hover:shadow-lg border border-transparent hover:border-blue-200 transition cursor-pointer"
                data-nama="{{ $p->name }}"
                data-phone="{{ $p->phone ?? '-' }}"
                data-area="{{ $p->area->nama_area ?? '-' }}"
                data-paket="{{ $p->package->nama_paket ?? '-' }}"
            >
                <div class="space-y-1">
                    <h3 class="text-xl font-bold text-gray-800 truncate">{{ $p->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $p->phone ?? '-' }}</p>
                </div>

                <div class="mt-4 space-y-1">
                    <p class="text-sm text-gray-600"><span class="font-semibold">Area:</span> {{ $p->area->nama_area ?? '-' }}</p>
                    <p class="text-sm text-gray-600"><span class="font-semibold">Paket:</span> {{ $p->package->nama_paket ?? '-' }}</p>
                </div>

                <div class="flex items-center justify-between mt-5">
                    <span class="text-xs font-semibold px-3 py-1 rounded-full {{ $statusColor }}">
                        {{ $statusLabel }}
                    </span>
                    <span class="text-sm font-semibold text-gray-800">{{ $tgl }}</span>
                </div>
            </a>

        @empty
            <div class="text-center text-gray-500 col-span-full py-10">
                Belum ada pelanggan baru bulan ini.
            </div>
        @endforelse
    </div>

    <div id="noResults" class="text-center text-gray-500 col-span-full py-10 hidden">
        Tidak ada pelanggan yang cocok.
    </div>
</div>

{{-- SCRIPT SEARCH --}}
<script>
const searchInput = document.getElementById('searchInput');
const pelangganCards = document.querySelectorAll('.pelanggan-card');
const noResults = document.getElementById('noResults');

searchInput.addEventListener('input', () => {
    const keyword = searchInput.value.toLowerCase();
    let visible = 0;

    pelangganCards.forEach(card => {
        let dataText = (card.dataset.nama + card.dataset.phone + card.dataset.area + card.dataset.paket).toLowerCase();
        card.style.display = dataText.includes(keyword) ? '' : 'none';
        if (dataText.includes(keyword)) visible++;
    });

    noResults.classList.toggle('hidden', visible > 0);
});
</script>


@endsection


{{-- BOTTOM MENU --}}
@section('bottom-menu')
<div class="flex justify-between gap-1 p-2 bg-white shadow-[0_-3px_10px_rgba(0,0,0,0.05)] border-t fixed bottom-0 left-0 right-0 z-50">

    @php
        $menu = [
            ['icon'=>'🏠','title'=>'Home','link'=>route('admin.dashboard')],
            ['icon'=>'💳','title'=>'Transaksi','link'=>route('admin.transaksi_cash')],
            ['icon'=>'💰','title'=>'Bayar','link'=>route('admin.belum_bayar')],
            ['icon'=>'📩','title'=>'Aduan','link'=>route('pengaduan.index')],
            ['icon'=>'⚙️','title'=>'Setting','link'=>route('setting')],
        ];
    @endphp

    @foreach($menu as $m)
        <a href="{{ $m['link'] }}"
           class="flex flex-col items-center justify-center flex-1 text-xs font-medium text-gray-600 py-2 hover:text-[var(--primary-color)] transition">
            <div class="text-xl">{{ $m['icon'] }}</div>
            <span>{{ $m['title'] }}</span>
        </a>
    @endforeach
</div>
@endsection
