@extends('layouts.app')
@section('title', 'Belum Bayar')

@section('content')
<div class="pt-24 pb-20 px-3 sm:px-10 max-w-6xl mx-auto">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">

        {{-- Title --}}
        <h2 class="text-2xl font-bold text-gray-800">Belum Bayar</h2>

        {{-- Search + Back --}}
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">

            {{-- Button Back --}}
            <a href="{{ route('pelanggan.index') }}" 
               class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg text-sm flex items-center gap-1 w-full sm:w-auto justify-center">
                <i class="fa fa-arrow-left"></i> Kembali
            </a>

            {{-- Search Bar --}}
            <input 
                type="text" 
                id="searchInput" 
                placeholder="Cari pelanggan..." 
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-full sm:w-56 focus:ring-blue-500 focus:border-blue-500"
            >
        </div>

    </div>

    {{-- GRID --}}
    <div id="pelangganGrid" class="grid gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3">

        @forelse ($pelanggans as $pelanggan)

            @php
                // 1) Bulan sudah bayar
                $transaksi = $pelanggan->transaksis ?? collect();
                $bulanSudahBayar = collect();

                foreach ($transaksi as $trx) {
                    if (!$trx->bulan) continue;

                    foreach (explode(',', $trx->bulan) as $b) {
                        $bulanSudahBayar->push(\Carbon\Carbon::parse($b . '-01')->format('Y-m'));
                    }
                }

                // 2) Tagihan belum bayar
                $tagihanBelum = $pelanggan->tagihans->filter(function ($t) use ($bulanSudahBayar) {
                    return $t->status === 'belum_lunas'
                        && !$bulanSudahBayar->contains(\Carbon\Carbon::parse($t->bulan)->format('Y-m'));
                });

                // 3) Ambil tagihan pertama
                $tagihan = $tagihanBelum->sortBy('bulan')->first();
            @endphp

            @if (!$tagihan) @continue @endif

            {{-- CARD --}}
            <a href="{{ route('tagihan.detail_belum_bayar', $pelanggan->id) }}"
               class="pelanggan-card block bg-white rounded-2xl shadow hover:shadow-lg transition hover:-translate-y-1 flex flex-col justify-between transform hover:scale-[1.02]"
               data-name="{{ strtolower($pelanggan->name) }}"
               data-phone="{{ strtolower($pelanggan->phone ?? '') }}"
               data-area="{{ strtolower($pelanggan->area->nama_area ?? '') }}"
               style="min-height: 190px;">

                <div class="p-4 flex flex-col justify-between h-full">
                    <div>

                        <h3 class="text-base font-semibold text-gray-800 truncate">
                            {{ $pelanggan->name }}
                        </h3>

                        <p class="text-xs text-gray-500 mt-1 truncate">
                            📞 {{ $pelanggan->phone ?? '-' }}
                            • {{ $pelanggan->area->nama_area ?? '-' }}
                        </p>

                        <p class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded inline-block mt-1 truncate">
                            🌐 {{ $pelanggan->device->ip_address ?? '-' }}
                        </p>

                        <p class="text-xs text-gray-700 mt-1">
                            📅 {{ \Carbon\Carbon::parse($tagihan->tanggal_tagihan)->format('d-m-Y') }}
                        </p>

                    </div>

                    <div class="mt-3 text-right">

                        <span class="inline-block bg-blue-100 text-blue-700 text-xs font-semibold px-2 py-1 rounded-full mb-1">
                            DHS {{ $pelanggan->id }}
                        </span>

<div class="text-lg font-bold text-gray-800">
    Rp {{ number_format(
        optional($pelanggan->package)->harga 
        + ($tagihan->biaya_tambahan_1 ?: 0)
        + ($tagihan->biaya_tambahan_2 ?: 0)
        - ((optional($pelanggan->package)->harga 
            + ($tagihan->biaya_tambahan_1 ?: 0)
            + ($tagihan->biaya_tambahan_2 ?: 0)) 
           * ($pelanggan->diskon ?: 0) / 100),
    0, ',', '.') }}
</div>


                    </div>
                </div>

                {{-- FOOTER --}}
                <div class="p-2 text-center bg-gray-50 text-xs text-gray-600 rounded-b-2xl">
                    <span class="font-semibold">Bulan Tagihan</span> <br>
                    {{ \Carbon\Carbon::parse($tagihan->bulan)->translatedFormat('F Y') }}
                </div>
            </a>

        @empty
            <div class="text-center text-gray-500 col-span-full py-10" id="noResults">
                Tidak ada pelanggan yang belum bayar.
            </div>
        @endforelse

    </div>
</div>

<script>
    const searchInput = document.getElementById('searchInput');
    const cards = document.querySelectorAll('.pelanggan-card');

    searchInput.addEventListener('input', function() {
        const keyword = this.value.toLowerCase();
        let count = 0;

        cards.forEach(card => {
            const name = card.dataset.name;
            const phone = card.dataset.phone;
            const area = card.dataset.area;

            const match =
                name.includes(keyword) ||
                phone.includes(keyword) ||
                area.includes(keyword) ||
                keyword === '';

            card.style.display = match ? '' : 'none';
            if (match) count++;
        });

        document.getElementById('noResults')?.classList.toggle('hidden', count > 0);
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

