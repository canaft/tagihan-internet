@extends('layouts.app')

@section('title', 'Pelanggan Lunas')

@section('navbar')
<nav class="fixed top-0 left-0 w-full z-50 bg-[var(--primary-color)] text-white p-4 flex justify-between items-center shadow-md">
    <a href="{{ route('sales.pelanggan.index') }}" class="text-lg">
        <i class="fas fa-arrow-left"></i>
    </a>

    <div class="text-lg font-bold">Pelanggan Lunas</div>

    <form method="POST" action="{{ route('logout') }}" class="hidden lg:block">
        @csrf
        <button type="submit"
            class="bg-[var(--accent-color)] px-4 py-2 rounded-lg font-semibold hover:opacity-90 transition">
            Logout
        </button>
    </form>
</nav>
@endsection

@section('content')
<div class="pt-24 pb-20 px-4 max-w-4xl mx-auto">

    {{-- JUDUL --}}
    <h2 class="text-xl font-semibold mb-2 text-[var(--primary-color)]">
        Daftar Pelanggan Lunas
    </h2>

    {{-- KOTAK INFO --}}
    <div class="bg-[var(--primary-color)] text-white p-4 rounded-xl mb-4 shadow">
        <div class="font-medium">Wilayah: {{ Auth::user()->wilayah }}</div>
        <div class="text-sm opacity-80">
            Total Lunas: {{ $pelanggan->count() }} Pelanggan
        </div>
    </div>

    {{-- BUTTON KEMBALI --}}
    <div class="mb-6">
        <a href="{{ route('sales.dashboard') }}"
            class="inline-flex items-center gap-2 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-semibold hover:bg-gray-300 transition shadow-sm">
            <i class="fas fa-arrow-left"></i>
            Kembali ke Dashboard
        </a>
    </div>

    {{-- LIST --}}
    @forelse ($pelanggan as $p)

        @php
            $harga = $p->package->harga ?? 0;
            $b1 = $p->biaya_tambahan_1 ?? 0;
            $b2 = $p->biaya_tambahan_2 ?? 0;
            $diskon = $p->diskon ?? 0;

            $subtotal = $harga + $b1 + $b2;
            $jumlah = $subtotal - ($subtotal * $diskon / 100);

            $tagihanTerakhir = $p->tagihan()
                ->where('status','lunas')
                ->latest('bulan')
                ->first();
        @endphp

        {{-- CARD --}}
        <div class="bg-white p-4 rounded-xl shadow-sm mb-4 border border-gray-100 hover:shadow transition">

            {{-- HEADER --}}
            <div class="flex justify-between items-center">
                <div>
                    <div class="font-semibold text-[var(--primary-color)] text-lg capitalize">
                        {{ $p->name }}
                    </div>
                    <div class="text-sm text-gray-500">
                        {{ $p->alamat }}
                    </div>
                </div>

                <span class="bg-green-100 text-green-700 text-xs px-3 py-1 rounded-full font-semibold">
                    Lunas
                </span>
            </div>

            {{-- DETAIL --}}
            <div class="mt-3 flex justify-between text-sm">
                <div class="text-gray-500">Terakhir Bayar</div>
                <div class="font-medium">
                    @if($tagihanTerakhir)
                        {{ \Carbon\Carbon::parse($tagihanTerakhir->bulan)->translatedFormat('F Y') }}
                    @else
                        -
                    @endif
                </div>
            </div>

            <div class="mt-1 flex justify-between text-sm">
                <div class="text-gray-500">Nominal</div>
                <div class="font-bold text-[var(--accent-color)]">
                    Rp {{ number_format($jumlah, 0, ',', '.') }}
                </div>
            </div>

            {{-- BUTTON DETAIL --}}
            <div class="mt-4">
                <button onclick="showDetail({{ $p->id }})"
                    class="w-full text-center bg-[var(--accent-color)] text-white py-2 rounded-lg font-semibold hover:opacity-90 transition">
                    Detail Lunas
                </button>
            </div>
        </div>

    @empty
        <div class="text-center text-gray-600 mt-10">
            Tidak ada pelanggan lunas.
        </div>
    @endforelse

</div>

{{-- =============================== --}}
{{-- MODAL DETAIL LUNAS --}}
{{-- =============================== --}}

<style>
    .modal-fade {
        animation: fadeIn 0.25s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.95); }
        to   { opacity: 1; transform: scale(1); }
    }
</style>

<div id="modalDetail"
    class="fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm hidden items-center justify-center p-4 z-[999]">

    <div class="bg-white/90 backdrop-blur-md w-full max-w-md rounded-2xl shadow-2xl p-6 modal-fade">

        <h3 class="text-xl font-bold text-[var(--primary-color)] mb-1" id="modalNama"></h3>
        <p class="text-gray-600 mb-4" id="modalAlamat"></p>

        <div class="border-t pt-3">
            <h4 class="font-semibold mb-2">Riwayat Pembayaran:</h4>
            <div id="modalRiwayat" class="space-y-2 max-h-72 overflow-y-auto pr-1"></div>
        </div>

        <button onclick="closeModal()"
            class="mt-6 w-full bg-[var(--accent-color)] text-white py-2 rounded-xl font-semibold shadow hover:opacity-90 transition">
            Tutup
        </button>
    </div>
</div>

<script>
function showDetail(id) {
    fetch("{{ url('sales/pelanggan/detail-lunas') }}/" + id)
        .then(res => res.json())
        .then(data => {
            document.getElementById('modalNama').innerText = data.pelanggan.nama;
            document.getElementById('modalAlamat').innerText = data.pelanggan.alamat;

            let html = "";

            if (data.riwayat.length === 0) {
                html = `<div class="text-center text-gray-500 py-3">Belum ada riwayat pembayaran.</div>`;
            } else {
                data.riwayat.forEach(r => {
                    html += `
                        <div class="p-3 border rounded-xl bg-white shadow-sm">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Bulan</span>
                                <span class="font-semibold">${r.bulan}</span>
                            </div>

                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Nominal</span>
                                <span class="font-bold text-[var(--accent-color)]">Rp ${r.nominal}</span>
                            </div>

                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Tanggal Bayar</span>
                                <span>${r.tanggal_bayar}</span>
                            </div>

                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Dibayar Oleh</span>
                                <span>${r.dibayar_oleh}</span>
                            </div>
                        </div>
                    `;
                });
            }

            document.getElementById('modalRiwayat').innerHTML = html;

            let modal = document.getElementById('modalDetail');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });
}

function closeModal() {
    let modal = document.getElementById('modalDetail');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>

@endsection
