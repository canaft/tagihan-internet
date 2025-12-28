@extends('layouts.app')
@section('title', 'Daftar Semua Pelanggan')

@section('content')
<div class="pt-24 pb-20 px-3 sm:px-10 max-w-6xl mx-auto">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-3">

        <h2 class="text-2xl font-bold text-gray-800">
            Daftar Pelanggan
        </h2>

        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">

            {{-- Tombol Kembali --}}
            <a href="{{ route('pelanggan.index') }}" 
               class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg text-sm font-medium transition text-center">
                Kembali
            </a>

            {{-- Input Search --}}
            <input type="text" id="searchInput" placeholder="Cari pelanggan..." 
                   class="border border-gray-300 rounded-lg px-4 py-2 text-sm w-full sm:w-56 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

    </div>


    {{-- Grid Pelanggan --}}
    <div id="pelangganGrid" class="grid gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3">
    @forelse ($pelanggans as $pelanggan)
        @php
            $status = $pelanggan->status ?? 'belum';
            $isLunas = strtolower($status) === 'lunas';

            $bgStatus = $isLunas ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';
            $labelStatus = $isLunas ? 'LUNAS' : 'BELUM BAYAR';

            $hargaPaket = $pelanggan->package->harga ?? 0;
            $biaya1 = $pelanggan->biaya_tambahan_1 ?? 0;
            $biaya2 = $pelanggan->biaya_tambahan_2 ?? 0;
            $diskon = $pelanggan->diskon ?? 0;

            $total = ($hargaPaket + $biaya1 + $biaya2) 
                    - (($hargaPaket + $biaya1 + $biaya2) * $diskon / 100);
        @endphp

        <a href="{{ route('admin.detail_pelanggan', $pelanggan->id) }}" 
           class="block pelanggan-card bg-white rounded-xl shadow hover:shadow-md transition transform hover:scale-[1.02] p-4"
           data-name="{{ strtolower($pelanggan->name) }}" 
           data-phone="{{ strtolower($pelanggan->phone ?? '') }}" 
           data-area="{{ strtolower($pelanggan->area->nama_area ?? '') }}">

            <div class="flex flex-col justify-between h-full">

                <div class="mb-2">
                    <h3 class="text-lg font-semibold text-gray-800 truncate">
                        {{ $pelanggan->name }}
                    </h3>

                    <p class="text-sm text-gray-500 truncate">
                        {{ $pelanggan->phone ?? '-' }} • {{ $pelanggan->area->nama_area ?? '-' }}
                    </p>

                    <p class="text-sm text-gray-500 truncate">
                        IP: {{ $pelanggan->device->ip_address ?? '-' }}
                    </p>

                    <p class="text-sm text-gray-500 truncate">
                        Tanggal Tagihan: {{ \Carbon\Carbon::parse($pelanggan->tanggal_tagihan)->format('d-m-Y') }}
                    </p>
                </div>

                <div class="flex justify-between items-center mt-2">
                    <span class="text-xs font-semibold px-2 py-1 rounded-full {{ $bgStatus }}">
                        {{ $labelStatus }}
                    </span>

                    <span class="text-sm font-bold text-gray-800">
                        Rp {{ number_format($total, 0, ',', '.') }}
                    </span>
                </div>

            </div>
        </a>

    @empty
        <div class="text-center text-gray-500 col-span-full py-10">
            Belum ada data pelanggan.
        </div>
    @endforelse
    </div>

    {{-- No Results --}}
    <div id="noResults" class="text-center text-gray-500 col-span-full py-10 hidden">
        Tidak ada pelanggan yang cocok.
    </div>

</div>

<script>
    const searchInput = document.getElementById('searchInput');
    const pelangganCards = document.querySelectorAll('.pelanggan-card');
    const noResults = document.getElementById('noResults');

    searchInput.addEventListener('input', function() {
        const keyword = this.value.toLowerCase();
        let visibleCount = 0;

        pelangganCards.forEach(card => {
            const name = card.dataset.name;
            const phone = card.dataset.phone;
            const area = card.dataset.area;

            if (name.includes(keyword) || phone.includes(keyword) || area.includes(keyword) || keyword === '') {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        noResults.classList.toggle('hidden', visibleCount > 0);
    });
</script>
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

