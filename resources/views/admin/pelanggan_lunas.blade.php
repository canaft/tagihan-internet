@extends('layouts.app')
@section('title', 'Daftar Pelanggan Lunas')
@php
    $templateWALunas = \App\Models\Setting::where([
        ['category', 'lunas'],
        ['is_default', 1]
    ])->value('value');
@endphp

@section('content')
<div class="pt-24 pb-20 px-3 sm:px-10 max-w-6xl mx-auto">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-3">
        <h2 class="text-3xl font-bold text-gray-800">Pelanggan Lunas</h2>

        <div class="flex flex-row items-center gap-2 w-full sm:w-auto">

            <a href="{{ route('pelanggan.index') }}" 
               class="bg-white border shadow-sm hover:bg-gray-50 text-gray-800 px-4 py-2 rounded-xl text-sm">
                Kembali
            </a>

            <input type="text" id="searchInput" placeholder="Cari pelanggan..." 
                class="border border-gray-300 rounded-xl px-3 py-2 text-sm w-full sm:w-52">

            <button id="openFilter" 
                class="flex items-center gap-1 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-xl text-sm">
                Filter
            </button>

        </div>
    </div>

    {{-- Total --}}
    <div class="mb-4 text-right text-gray-700 font-semibold">
        Total Keseluruhan: <span id="totalKeseluruhan">Rp 0</span>
    </div>

    {{-- GRID --}}
    <div id="pelangganGrid" class="grid gap-4 grid-cols-1 sm:grid-cols-2 md:grid-cols-3">

        @forelse ($pelanggans as $pelanggan)

            @php
                // Ambil tagihan lunas yang benar-benar statusnya lunas
                $tagihanLunas = $pelanggan->tagihan
                    ->where('status', 'lunas')
                    ->values(); // penting: reset index untuk menghindari error

                // Jika tidak ada tagihan lunas, pelanggan ini TIDAK BOLEH TAMPIL
                if ($tagihanLunas->count() === 0) {
                    continue;
                }

                // Ambil transaksi lunas
                $transaksi = \App\Models\Transaksi::where('pelanggan_id', $pelanggan->id)
                    ->where('status','lunas')
                    ->get();

                // Format data riwayat
$riwayatArray = $transaksi->map(function($trx) {
    return [
        'bulan'      => $trx->bulan ?? '-', 
        'bulan_raw'  => $trx->bulan,
        'jumlah'     => 'Rp ' . number_format($trx->jumlah ?? 0, 0, ',', '.'),
        'tanggal'    => $trx->tanggal_bayar
                            ? \Carbon\Carbon::parse($trx->tanggal_bayar)->format('d-m-Y')
                            : '-',
        'waktu'      => $trx->tanggal_bayar
                            ? \Carbon\Carbon::parse($trx->tanggal_bayar)->format('H:i')
                            : '-',
        'dibayar'    => $trx->dibayar_oleh ?? '-',  // <-- koma ditambahkan di sini
        'status'     => $trx->status ?? '-',   
       // ===== TAMBAHAN BIAYA =====
        'nama_biaya_1' => $trx->nama_biaya_1 ?? '-',
        'biaya_tambahan1' => $trx->biaya_tambahan_1 ?? 'Rp 0',
        'nama_biaya_2' => $trx->nama_biaya_2 ?? '-',
        'biaya_tambahan2' => $trx->biaya_tambahan_2 ?? 'Rp 0',
    ];
});


                // Ambil 2 bulan terakhir
                $bulanSingkat = $tagihanLunas->take(2)->map(function($t) {
                    return \Carbon\Carbon::parse($t->bulan.'-01')->translatedFormat('M Y');
                })->implode(', ');

                $totalPelanggan = $tagihanLunas->sum('jumlah');

                $dibayarArr = $transaksi->pluck('dibayar_oleh')->filter()->unique()->values()->toArray();
                $bulanArr = $tagihanLunas->pluck('bulan')->map(fn($b)=>\Carbon\Carbon::parse($b.'-01')->format('Y-m'))->toArray();

                $tanggalMin = $transaksi->min('tanggal_bayar');
                $tanggalMax = $transaksi->max('tanggal_bayar');
            @endphp

            {{-- CARD --}}
            <div 
                class="pelanggan-card bg-white rounded-2xl shadow hover:shadow-md transition transform hover:scale-[1.02] p-5 cursor-pointer"
                data-id="{{ $pelanggan->id }}"
                data-nama="{{ $pelanggan->name }}"
                data-phone="{{ $pelanggan->phone ?? '-' }}"
                data-area="{{ strtolower($pelanggan->area->nama_area ?? '-') }}"
                data-ip="{{ $pelanggan->device->ip_address ?? '-' }}"
                data-paket="{{ strtolower($pelanggan->package->nama_paket ?? '-') }}"
                data-riwayat='@json($riwayatArray)'
                data-search-name="{{ strtolower($pelanggan->name) }}"
                data-search-phone="{{ strtolower($pelanggan->phone ?? '') }}"
                data-search-area="{{ strtolower($pelanggan->area->nama_area ?? '') }}"
                data-search-paket="{{ strtolower($pelanggan->package->nama_paket ?? '') }}"
                data-tanggal-min="{{ $tanggalMin ?? '' }}"
                data-tanggal-max="{{ $tanggalMax ?? '' }}"
                data-bulan='@json($bulanArr)'
                data-dibayar='@json($dibayarArr)'
                data-dibayar-lower='@json(array_map("strtolower", $dibayarArr))'
                data-total="{{ $totalPelanggan }}"
            >

                <h3 class="text-lg font-bold text-gray-800 truncate">{{ $pelanggan->name }}</h3>
                <p class="text-sm text-gray-500">
                    {{ $pelanggan->phone ?? '-' }} • {{ $pelanggan->area->nama_area ?? '-' }}
                </p>
                <p class="text-sm text-gray-500">IP: {{ $pelanggan->device->ip_address ?? '-' }}</p>
                <p class="text-sm text-gray-500">Paket: {{ $pelanggan->package->nama_paket ?? '-' }}</p>
                <p class="text-sm text-gray-500 font-medium">
                    Dibayar Oleh: {{ implode(', ', $dibayarArr) ?: '-' }}
                </p>

                <div class="mt-3 text-xs text-gray-600">
                    <span class="font-semibold text-green-700">Sudah bayar:</span>
                    <span>{{ $tagihanLunas->count() }} bulan</span>
                </div>

                <p class="text-xs text-gray-500 mt-1">
                    Bulan: {{ $bulanSingkat }}{{ $tagihanLunas->count() > 2 ? ', ...' : '' }}
                </p>

                <div class="flex justify-between items-center mt-4">
                    <span class="text-xs font-semibold px-2 py-1 rounded-full bg-green-100 text-green-700">LUNAS</span>
                    <span class="text-base font-bold text-gray-800">
                        Rp {{ number_format($totalPelanggan, 0, ',', '.') }}
                    </span>
                </div>

            </div>

        @empty
            <div class="text-center text-gray-500 col-span-full py-10">Belum ada pelanggan lunas.</div>
        @endforelse
    </div>

    <div id="noResults" class="text-center text-gray-500 col-span-full py-10 hidden">
        Tidak ada pelanggan yang cocok.
    </div>

</div>




{{-- ====================== MODAL DETAIL ====================== --}}
<div id="detailModal" 
     class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-[9999] px-4">
    <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md relative">
        <button id="closeModal" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-xl">✖</button>
        <h2 class="text-xl font-bold mb-3 text-gray-800">Detail Pembayaran</h2>

        <div class="space-y-1 text-sm text-gray-700 mb-4">
            <p><strong>Nama:</strong> <span id="mNama"></span></p>
            <p><strong>No HP:</strong> <span id="mPhone"></span></p>
            <p><strong>Area:</strong> <span id="mArea"></span></p>
            <p><strong>IP Address:</strong> <span id="mIP"></span></p>
            <p><strong>Paket:</strong> <span id="mPaket"></span></p>
            <p><strong>Dibayar Oleh:</strong> <span id="mDibayar"></span></p>
        </div>

        <h3 class="text-md font-bold text-gray-800 mb-2">Riwayat Transaksi</h3>
        <div id="riwayatList" class="space-y-2 max-h-64 overflow-y-auto pr-1"></div>
        {{-- BUTTON KIRIM WA --}}
<div class="mt-4">
    <a id="btnKirimWA"
       href="#"
       target="_blank"
       class="block text-center bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-xl font-semibold text-sm">
        📩 Kirim WhatsApp
    </a>
</div>

    </div>
</div>




{{-- ====================== MODAL FILTER ====================== --}}
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

            <label>
                <span class="font-semibold">Dibayar Oleh</span>
                <select id="filterDibayar" class="border rounded-xl w-full px-3 py-2 mt-1">
                    <option value="">Semua</option>
                    @foreach(\App\Models\User::all() as $user)
                        <option value="{{ strtolower($user->name) }}">{{ $user->name }}</option>
                    @endforeach
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




{{-- ====================== SCRIPT ====================== --}}
<script>
    const TEMPLATE_WA_LUNAS = @json($templateWALunas);
</script>

<script>
document.addEventListener("DOMContentLoaded", () => {

    const searchInput = document.getElementById('searchInput');
    const pelangganCards = document.querySelectorAll('.pelanggan-card');
    const noResults = document.getElementById('noResults');
    const totalKeseluruhanEl = document.getElementById('totalKeseluruhan');

    const filterArea = document.getElementById('filterArea');
    const filterPaket = document.getElementById('filterPaket');
    const filterBulan = document.getElementById('filterBulan');
    const filterDibayar = document.getElementById('filterDibayar');
    const filterTanggalMin = document.getElementById('filterTanggalMin');
    const filterTanggalMax = document.getElementById('filterTanggalMax');

    const filterModal = document.getElementById("filterModal");
    const openFilter = document.getElementById("openFilter");
    const closeFilter = document.getElementById("closeFilter");

    const modal = document.getElementById('detailModal');
    const closeModal = document.getElementById('closeModal');
    const riwayatContainer = document.getElementById('riwayatList');
    const mDibayar = document.getElementById('mDibayar');

    function filterCards() {
        
        const keyword = searchInput.value.toLowerCase();
        const areaVal = filterArea.value;
        const paketVal = filterPaket.value;
        const bulanVal = filterBulan.value ? filterBulan.value.substring(0, 7) : "";
        const dibayarVal = filterDibayar.value.toLowerCase();
        const tanggalMin = filterTanggalMin.value ? new Date(filterTanggalMin.value) : null;
        const tanggalMax = filterTanggalMax.value ? new Date(filterTanggalMax.value) : null;

        let visibleCount = 0;
        let total = 0;

        pelangganCards.forEach(card => {
            const name = card.dataset.searchName ?? '';
            const phone = card.dataset.searchPhone ?? '';
            const area = card.dataset.searchArea ?? '';
            const paket = card.dataset.searchPaket ?? '';
            const totalCard = parseInt(card.dataset.total ?? 0);

            const bulanCardArr = JSON.parse(card.dataset.bulan ?? '[]');
            const dibayarCardArr = JSON.parse(card.dataset.dibayarLower ?? '[]');

            const tglMinCard = card.dataset.tanggalMin ? new Date(card.dataset.tanggalMin) : null;
            const tglMaxCard = card.dataset.tanggalMax ? new Date(card.dataset.tanggalMax) : null;

            const matchesSearch =
                name.includes(keyword) ||
                phone.includes(keyword) ||
                area.includes(keyword);

            const matchesArea = !areaVal || area === areaVal;
            const matchesPaket = !paketVal || paket === paketVal;
            const matchesDibayar = !dibayarVal || dibayarCardArr.includes(dibayarVal);
            const matchesBulan = !bulanVal || bulanCardArr.includes(bulanVal);

            const matchesTanggal =
                (!tanggalMin || (tglMaxCard && tglMaxCard >= tanggalMin)) &&
                (!tanggalMax || (tglMinCard && tglMinCard <= tanggalMax));

            const matchesAll =
                matchesSearch &&
                matchesArea &&
                matchesPaket &&
                matchesBulan &&
                matchesDibayar &&
                matchesTanggal;

            if (matchesAll) {
                card.style.display = "block";
                visibleCount++;
                total += totalCard;
            } else {
                card.style.display = "none";
            }
        });

        totalKeseluruhanEl.textContent = 'Rp ' + total.toLocaleString('id-ID');
        noResults.classList.toggle("hidden", visibleCount > 0);
    }

    // Auto filter on input
    const filterElements = [
        searchInput,
        filterArea,
        filterPaket,
        filterBulan,
        filterDibayar,
        filterTanggalMin,
        filterTanggalMax
    ];

    filterElements.forEach(el => {
        ['input', 'change', 'keyup'].forEach(evt => {
            el.addEventListener(evt, filterCards);
        });
    });

    // AUTO CLOSE FILTER MODAL
    function autoCloseFilter() {
        filterModal.classList.add("hidden");
        filterModal.classList.remove("flex");
    }

    [filterArea, filterPaket, filterBulan, filterDibayar, filterTanggalMin, filterTanggalMax].forEach(el => {
        el.addEventListener("change", () => {
            filterCards();
            autoCloseFilter();
        });
    });

    filterCards();

    // Open/Close Filter Modal
    openFilter.addEventListener("click", () => {
        filterModal.classList.remove("hidden");
        filterModal.classList.add("flex");
    });

    closeFilter.addEventListener("click", () => {
        filterModal.classList.add("hidden");
        filterModal.classList.remove("flex");
    });

    filterModal.addEventListener("click", (e) => {
        if (e.target === filterModal) {
            filterModal.classList.add("hidden");
            filterModal.classList.remove("flex");
        }
    });




    // ======================= DETAIL MODAL =======================

    document.querySelectorAll('.pelanggan-card').forEach(card => {
        card.addEventListener('click', () => {

            document.getElementById('mNama').textContent = card.dataset.nama;
            document.getElementById('mPhone').textContent = card.dataset.phone;
            document.getElementById('mArea').textContent = card.dataset.area;
            document.getElementById('mIP').textContent = card.dataset.ip;
            document.getElementById('mPaket').textContent = card.dataset.paket;

            mDibayar.textContent = JSON.parse(card.dataset.dibayar).length
                ? JSON.parse(card.dataset.dibayar).join(', ')
                : '-';

            const riwayat = JSON.parse(card.dataset.riwayat);

            riwayatContainer.innerHTML = riwayat.map(r => `
                <div class="p-2 border rounded-lg bg-gray-50">
                    <p class="text-sm font-semibold text-gray-800">${r.bulan}</p>
                    <p class="text-sm text-gray-700">Jumlah: ${r.jumlah}</p>
                    <p class="text-xs text-gray-500">
                        Tanggal: ${r.tanggal} • Jam: ${r.waktu} • Dibayar Oleh: ${r.dibayar}
                    </p>

                    <button 
                        class="mt-2 bg-red-500 text-white px-3 py-1 rounded text-xs cancel-btn"
                        data-bulan="${r.bulan_raw}"
                        data-id="${card.dataset.id}">
                        Batalkan
                    </button>
                </div>
            `).join('');

            // Button pembatalan
            setTimeout(() => {
                document.querySelectorAll(".cancel-btn").forEach(btn => {
                    btn.addEventListener("click", () => {
                        if (!confirm("Yakin batalkan transaksi bulan ini?")) return;

                        fetch("{{ route('transaksi.batal') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({
                                pelanggan_id: btn.dataset.id,
                                bulan: btn.dataset.bulan
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                alert("Transaksi berhasil dibatalkan!");
                                location.reload();
                            } else {
                                alert("Gagal membatalkan!");
                            }
                        });
                    });
                });
            }, 200);

            modal.classList.remove('hidden');
            modal.classList.add('flex');

                    // SET LINK WA
        const phoneRaw = card.dataset.phone.replace(/[^0-9]/g, '');
        const phoneFix = phoneRaw.startsWith('0')
            ? '62' + phoneRaw.substring(1)
            : phoneRaw;

const waMessage = generateWAMessage(card);

if (!waMessage) {
    btnKirimWA.href = '#';
    btnKirimWA.classList.add('opacity-50','pointer-events-none');
    return;
}

btnKirimWA.classList.remove('opacity-50','pointer-events-none');
btnKirimWA.href = `https://wa.me/${phoneFix}?text=${waMessage}`;


        });
    });

    closeModal.addEventListener('click', () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    });

    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    });

    const btnKirimWA = document.getElementById('btnKirimWA');

function generateWAMessage(card) {
    const riwayat = JSON.parse(card.dataset.riwayat ?? '[]');
    if (!riwayat.length) {
        alert('Tidak ada data transaksi');
        return null;
    }

    const trx = riwayat[0];

    // ❌ BLOK JIKA BUKAN LUNAS
    if (trx.status !== 'lunas') {
        alert('Transaksi ini belum LUNAS');
        return null;
    }

    // Format nomor WA (mirip PHP)
    let nomor = card.dataset.phone ?? '';
    nomor = nomor.replace(/[^0-9]/g, ''); // hapus karakter selain angka
    if (nomor.startsWith('0')) {
        nomor = '62' + nomor.substring(1);
    }
    const phoneFix = nomor;

    // Ambil template WA dari Blade
    let message = TEMPLATE_WA_LUNAS ?? '';

    // Mapping placeholder
    message = message.replace(/{nama}/g, card.dataset.nama ?? '-');
    message = message.replace(/{nomor}/g, phoneFix ?? '-');
    message = message.replace(/{area}/g, card.dataset.area ?? '-');

    message = message.replace(/{tanggal_bayar}/g, trx.tanggal ?? '-');
    message = message.replace(/{waktu_bayar}/g, trx.waktu ?? '-');
    message = message.replace(/{bulan_tagihan}/g, trx.bulan ?? '-');
    message = message.replace(/{jenis_paket}/g, card.dataset.paket ?? '-');

    message = message.replace(/{biaya_paket}/g, trx.jumlah ?? 'Rp 0');
    message = message.replace(/{diskon}/g, '0');
    message = message.replace(/{total}/g, trx.jumlah ?? 'Rp 0');
    message = message.replace(/{admin}/g, trx.dibayar ?? '-');

    return encodeURIComponent(message);
}




});

document.addEventListener("DOMContentLoaded", () => {

    // ===============================
    // CHOICES DROPDOWN (SAMAIN DENGAN ATAS)
    // ===============================
    const choiceConfig = {
        searchEnabled: false,
        shouldSort: false,
        itemSelectText: '',
    };

    const choiceFilterArea   = new Choices('#filterArea', choiceConfig);
    const choiceFilterPaket  = new Choices('#filterPaket', choiceConfig);
    const choiceFilterDibayar = new Choices('#filterDibayar', choiceConfig);

});
</script>

@endsection

{{-- Choices.js --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>





{{-- ====================== Mobile Bottom Menu ====================== --}}
@section('bottom-menu')
<div class="flex justify-between gap-2 p-2 bg-white shadow-inner border-t fixed bottom-0 left-0 right-0 z-50">
     @php
        $mobileCards = [
            ['icon'=>'🏠','title'=>'Beranda','link'=>route('admin.dashboard')],
            ['icon'=>'💳','title'=>'Transaksi','link'=>route('admin.pelanggan_lunas')],
            ['icon'=>'💰','title'=>'Bayar','link'=>route('admin.belum_bayar')],
            ['icon'=>'📩','title'=>'Pengaduan','link'=>route('pengaduan.index')],
            ['icon'=>'⚙️','title'=>'Setting','link'=>route('setting')],
        ];
    @endphp

    @foreach($mobileCards as $card)
    <a href="{{ $card['link'] }}" 
       class="flex-1 flex flex-col items-center text-xs bg-white p-2 rounded-lg">
        <div class="text-xl mb-1">{{ $card['icon'] }}</div>
        <span>{{ $card['title'] }}</span>
    </a>
    @endforeach
</div>
@endsection
