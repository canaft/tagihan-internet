@extends('layouts.app')
@section('title', 'Detail Pelanggan - ' . $pelanggan->name)

@section('content')
<div class="pt-24 pb-20 max-w-md mx-auto px-3 bg-[#F5EEEB] min-h-screen">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-5">
        <a href="{{ route('sales.pelanggan.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fa fa-arrow-left"></i>
        </a>
        <h2 class="text-lg font-semibold text-[#2A4156]">Detail Pelanggan</h2>
        <div></div>
    </div>

    {{-- Kartu Pelanggan --}}
    <div class="bg-[#2A4156] text-white rounded-xl p-4 mb-4 shadow">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="font-bold text-lg capitalize">{{ $pelanggan->name }}</h3>
                <p class="text-sm opacity-90">{{ $pelanggan->phone }}</p>
            </div>
            <div class="bg-[#1E2F45] text-xs px-3 py-1 rounded-full uppercase">
                {!! optional($pelanggan->device)->ip_address ?: '&mdash;' !!}
            </div>
        </div>
    </div>

    {{-- Detail Pembayaran Bulanan --}}
    <div class="bg-white rounded-xl shadow p-4 mb-4">
        <p class="text-gray-500 text-sm font-semibold mb-1">Pembayaran Bulanan</p>
        <p class="text-gray-800 text-sm">{{ optional($pelanggan->package)->nama_paket ?: '-' }}</p>

        @php
            $biayaPerbulan = (optional($pelanggan->package)->harga ?: 0)
                            + ($pelanggan->biaya_tambahan_1 ?: 0)
                            + ($pelanggan->biaya_tambahan_2 ?: 0)
                            - (((optional($pelanggan->package)->harga ?: 0)
                            + ($pelanggan->biaya_tambahan_1 ?: 0)
                            + ($pelanggan->biaya_tambahan_2 ?: 0))
                            * ($pelanggan->diskon ?: 0) / 100);
        @endphp

        <div class="flex justify-between font-semibold mt-2 text-gray-800">
            <span>Biaya Perbulan</span>
            <span>Rp {{ number_format($biayaPerbulan,0,',','.') }}</span>
        </div>

        <div class="mt-2 text-sm text-gray-700">
            Bulan <strong>{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</strong>
        </div>
    </div>

    {{-- Total Bayar --}}
    <div class="bg-white rounded-xl shadow p-4 mb-4">
        <div class="flex justify-between font-semibold mt-2 text-gray-800">
            <span>Total Bayar</span>
            <span>Rp {{ number_format($biayaPerbulan,0,',','.') }}</span>
        </div>
    </div>

    {{-- Info --}}
    <div class="bg-blue-50 text-blue-700 text-xs text-center p-3 rounded-xl mb-4">
        Tekan <strong>BAYAR SEKARANG</strong> untuk menandai tagihan sebagai lunas.
    </div>

    {{-- Tombol Aksi --}}
    <div class="grid grid-cols-1 gap-2">

        {{-- DETAIL PELANGGAN MODAL --}}
        <div x-data="{ open: false }">
            <button @click="open = true" class="bg-[#2A4156] text-white font-semibold py-3 rounded-lg w-full">
                DETAIL PELANGGAN
            </button>

            <div x-show="open" x-transition.opacity class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
                <div @click.away="open = false" class="bg-white rounded-xl shadow-lg max-w-md w-full max-h-[90vh] overflow-y-auto p-6">

                    {{-- Header Modal --}}
                    <div class="flex justify-between items-center mb-4 sticky top-0 bg-white py-2 z-10">
                        <h3 class="text-lg font-bold text-gray-800">Detail Pelanggan</h3>
                        <button @click="open = false" class="text-gray-500 hover:text-gray-800">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>

                    {{-- Konten Modal --}}
                    <div class="text-sm text-gray-700 space-y-3">
                        <div class="flex justify-between"><span>Nama</span><span class="font-semibold">{{ $pelanggan->name }}</span></div>
                        <div class="flex justify-between"><span>Phone</span><span class="font-semibold">{{ $pelanggan->phone }}</span></div>
                        <div class="flex justify-between"><span>Status</span><span class="font-semibold">{{ $pelanggan->is_active ? 'Aktif' : 'Nonaktif' }}</span></div>
                        <div class="flex justify-between"><span>Paket</span><span class="font-semibold">{{ optional($pelanggan->package)->nama_paket ?: '-' }}</span></div>
                        <div class="flex justify-between"><span>Diskon</span><span class="font-semibold">{{ $pelanggan->diskon ?: 0 }}%</span></div>
                        <div class="flex justify-between"><span>Tanggal Register</span><span class="font-semibold">{{ $pelanggan->tanggal_register }}</span></div>
                        <div class="flex justify-between"><span>Tanggal Tagihan</span><span class="font-semibold">{{ $pelanggan->tanggal_tagihan }}</span></div>
                        <div class="flex justify-between"><span>Area</span><span class="font-semibold">{{ optional($pelanggan->area)->nama_area ?: '-' }}</span></div>
                        <div class="flex justify-between"><span>ODP</span><span class="font-semibold">{{ optional($pelanggan->odp)->kode ?: '-' }}</span></div>
                        <div class="flex justify-between"><span>Koordinat</span><span class="font-semibold">{{ $pelanggan->latitude ? $pelanggan->latitude . ', ' . $pelanggan->longitude : '-' }}</span></div>
                        <div class="flex justify-between"><span>Device IP</span><span class="font-semibold">{{ optional($pelanggan->device)->ip_address ?: '-' }}</span></div>
                        <div class="flex justify-between"><span>Biaya Tambahan 1</span><span class="font-semibold">Rp {{ number_format($pelanggan->biaya_tambahan_1 ?: 0,0,',','.') }}</span></div>
                        <div class="flex justify-between"><span>Biaya Tambahan 2</span><span class="font-semibold">Rp {{ number_format($pelanggan->biaya_tambahan_2 ?: 0,0,',','.') }}</span></div>
                    </div>

                    {{-- Tombol Tutup --}}
                    <div class="mt-6 text-right">
                        <button @click="open = false" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 font-semibold">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kirim WA --}}
        @php
            $nomor = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $pelanggan->phone));
            $pesan = "Halo {$pelanggan->name},\n\n"
                   . "Tagihan internet bulan ini:\n"
                   . "Nama Paket: " . (optional($pelanggan->package)->nama_paket ?: '-') . "\n"
                   . "Total Bayar: Rp " . number_format($biayaPerbulan,0,',','.') . "\n\nTerima kasih.";
        @endphp

        <a href="https://wa.me/{{ $nomor }}?text={{ urlencode($pesan) }}"
           target="_blank"
           class="bg-green-500 text-white font-semibold py-3 rounded-lg w-full text-center block">
           KIRIM WA TAGIHAN
        </a>

        {{-- BAYAR MULTI-BULAN --}}
        @if($pelanggan->tagihan()->where('status','!=','lunas')->count() > 0)
        <form id="formBayarMulti" action="{{ route('sales.pelanggan.bayar', $pelanggan->id) }}" method="POST">
            @csrf
            <input type="hidden" name="bulan_dipilih" id="bulanDipilih">
            <button type="button" id="btnBayarSekarang" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl mt-2">
                BAYAR SEKARANG
            </button>
        </form>
        @else
        <p class="text-red-500 text-sm text-center mt-3">Semua tagihan sudah lunas.</p>
        @endif
    </div>
</div>

{{-- Multi-bulan JS --}}
<script>
document.getElementById('btnBayarSekarang')?.addEventListener('click', async function() {

    const tagihanDb = @json($pelanggan->tagihan()->get()->keyBy(function($t){
        return \Carbon\Carbon::parse($t->bulan)->format('Y-m');
    }));

    const semuaBulan = [
        'Januari','Februari','Maret','April','Mei','Juni',
        'Juli','Agustus','September','Oktober','November','Desember'
    ];

    const bulanAngka = {
        'Januari': '01','Februari': '02','Maret': '03','April': '04',
        'Mei': '05','Juni': '06','Juli': '07','Agustus': '08',
        'September': '09','Oktober': '10','November': '11','Desember': '12'
    };

    let htmlCheckbox = '<div class="text-left">';
    semuaBulan.forEach((bulan, i) => {
        const key = new Date().getFullYear() + '-' + bulanAngka[bulan];
        let status = '', jumlahText = '';
        if(tagihanDb[key]){
            if(tagihanDb[key].status === 'lunas'){
                status = 'checked disabled';
            }
            jumlahText = ' - Rp ' + parseInt(tagihanDb[key].jumlah).toLocaleString('id-ID');
        }
        htmlCheckbox += `<div class="flex items-center gap-2 mb-1">
            <input type="checkbox" class="bulanCheckbox" value="${key}" id="bulan${i}" ${status}>
            <label for="bulan${i}">${bulan}${jumlahText}</label>
        </div>`;
    });
    htmlCheckbox += `</div><div class="mt-2">Total Bayar: <strong id="totalBayar">Rp 0</strong></div>`;

    const { value: selectedBulan } = await Swal.fire({
        title: 'Pilih Bulan yang Akan Dibayar',
        html: htmlCheckbox,
        showCancelButton: true,
        confirmButtonText: 'Bayar Sekarang',
        preConfirm: () => {
            let selected = [];
            document.querySelectorAll('.bulanCheckbox:checked:not([disabled])').forEach(cb => selected.push(cb.value));
            if(selected.length === 0){
                Swal.showValidationMessage('Pilih minimal 1 bulan!');
                return false;
            }
            return selected;
        },
        didOpen: () => {
            const checkboxes = document.querySelectorAll('.bulanCheckbox');
            const totalEl = document.getElementById('totalBayar');
            function hitungTotal(){
                let total = 0;
                checkboxes.forEach(c=>{
                    const key = c.value;
                    if(c.checked && !c.disabled && tagihanDb[key]) total += parseInt(tagihanDb[key].jumlah);
                });
                totalEl.textContent = 'Rp ' + total.toLocaleString('id-ID');
            }
            checkboxes.forEach(cb=>cb.addEventListener('change', hitungTotal));
            hitungTotal();
        }
    });

    if(selectedBulan){
        document.getElementById('bulanDipilih').value = selectedBulan.join(',');
        document.getElementById('formBayarMulti').submit();
    }
});
</script>
@endsection
