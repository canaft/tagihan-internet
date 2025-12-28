@extends('layouts.app')
@section('title', 'Daftar Pelanggan')

@section('content')
<div class="pt-24 pb-20 px-3 sm:px-10 max-w-7xl mx-auto bg-[#F5EEEB] min-h-screen">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-3">

        {{-- Tombol Kembali --}}
        <a href="{{ route('sales.dashboard') }}" 
           class="flex items-center gap-2 text-white bg-[#2A4156] hover:bg-[#1f3146] font-semibold px-4 py-2 rounded-lg shadow transition">
            <i class="fa fa-arrow-left"></i> Kembali
        </a>

        {{-- Judul --}}
        <h2 class="text-2xl font-bold text-[#2A4156] text-center flex-1">Daftar Pelanggan</h2>

        {{-- Pencarian --}}
        <div class="w-full sm:w-auto">
            <input type="text" id="searchInput" placeholder="Cari pelanggan..."
                class="border border-[#2A4156] rounded-lg px-3 py-2 text-sm w-full sm:w-64 focus:outline-none focus:ring-2 focus:ring-[#2A4156] shadow-sm bg-[#F5EEEB] text-[#2A4156]">
        </div>

    </div>

    {{-- Grid Pelanggan --}}
    <div id="pelangganGrid" class="grid gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3">

        @forelse ($pelanggan as $p)
            @php
                // Status dan styling
                $status = $p->transaksi->isNotEmpty() ? 'Lunas' : 'Belum Lunas';
                $bgStatus = $status === 'Lunas' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';

                // Total tagihan
                $hargaPaket = $p->package->harga ?? 0;
                $biaya1 = $p->biaya_tambahan_1 ?? 0;
                $biaya2 = $p->biaya_tambahan_2 ?? 0;
                $diskon = $p->diskon ?? 0;
                $total = ($hargaPaket + $biaya1 + $biaya2) - (($hargaPaket + $biaya1 + $biaya2) * $diskon / 100);
            @endphp

<a href="{{ route('sales.pelanggan.detail', $p->id) }}" 
                class="pelanggan-card block bg-white rounded-xl shadow hover:shadow-lg transition transform hover:-translate-y-1 hover:scale-[1.02] p-5"
                data-name="{{ strtolower($p->name) }}"
                data-phone="{{ strtolower($p->phone ?? '') }}"
                data-area="{{ strtolower($p->area->nama_area ?? '') }}">

                <div class="flex flex-col justify-between h-full">

                    {{-- Info Pelanggan --}}
                    <div class="mb-3">
                        <h3 class="text-lg font-semibold text-[#2A4156] truncate">{{ $p->name }}</h3>

                        <p class="text-sm text-gray-600 truncate">
                            {{ $p->phone ?? '-' }} • {{ $p->area->nama_area ?? '-' }}
                        </p>

                        <p class="text-sm text-gray-600 truncate">
                            IP: {{ $p->device->ip_address ?? '-' }}
                        </p>

                        <p class="text-sm text-gray-600">
                            Tanggal Register: {{ \Carbon\Carbon::parse($p->tanggal_register)->format('d-m-Y') }}
                        </p>
                    </div>

                    {{-- Status + Total --}}
                    <div class="flex justify-between items-center mt-auto">
                        <span class="text-xs font-bold px-2 py-1 rounded-full {{ $bgStatus }}">
                            {{ $status }}
                        </span>

                        <span class="text-sm font-bold text-[#2A4156]">
                            Rp {{ number_format($total, 0, ',', '.') }}
                        </span>
                    </div>

                </div>
            </a>

        @empty
            <div class="text-center text-gray-500 col-span-full py-10">
                Belum ada pelanggan.
            </div>
        @endforelse

    </div>

    {{-- No Results --}}
    <div id="noResults" class="text-center text-gray-500 col-span-full py-10 hidden">
        Tidak ada pelanggan yang cocok.
    </div>

</div>

{{-- Script Filter --}}
<script>
    const searchInput = document.getElementById('searchInput');
    const pelangganCards = document.querySelectorAll('.pelanggan-card');
    const noResults = document.getElementById('noResults');

    searchInput.addEventListener('input', function() {
        const keyword = this.value.toLowerCase();
        let visible = 0;

        pelangganCards.forEach(card => {
            const name = card.dataset.name;
            const phone = card.dataset.phone;
            const area = card.dataset.area;

            if (name.includes(keyword) || phone.includes(keyword) || area.includes(keyword)) {
                card.classList.remove('hidden');
                visible++;
            } else {
                card.classList.add('hidden');
            }
        });

        noResults.classList.toggle('hidden', visible > 0);
    });
</script>
@endsection
