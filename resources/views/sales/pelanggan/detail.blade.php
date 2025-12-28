@extends('layouts.app')
@section('title', 'Belum Bayar - ' . $pelanggan->name)

@section('content')
<div class="pt-24 pb-20 max-w-md mx-auto px-3">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-5">
        <a href="{{ route('sales.pelanggan.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fa fa-arrow-left"></i>
        </a>
        <h2 class="text-lg font-semibold text-gray-800">Belum Bayar</h2>
        <div></div>
    </div>

    {{-- Kartu Pelanggan --}}
    <div class="bg-blue-500 text-white rounded-xl p-4 mb-4 shadow">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="font-bold text-lg capitalize">{{ $pelanggan->name }}</h3>
                <p class="text-sm opacity-90">{{ $pelanggan->phone }}</p>
            </div>
            <div class="bg-blue-700 text-xs px-3 py-1 rounded-full uppercase">
                {!! optional($pelanggan->device)->ip_address ?: '&mdash;' !!}
            </div>
        </div>
    </div>

    {{-- Detail Pembayaran --}}
    <div class="bg-white rounded-xl shadow p-4 mb-4">
        <p class="text-gray-500 text-sm font-semibold mb-1">Pembayaran</p>
        <p class="text-gray-800 text-sm">{{ optional($pelanggan->package)->nama_paket ?: '-' }}</p>

        <div class="flex justify-between font-semibold mt-2 text-gray-800">
            <span>Biaya Perbulan</span>
            <span>
                Rp {{ number_format(
                    (optional($pelanggan->package)->harga ?: 0)
                    + ($pelanggan->biaya_tambahan_1 ?: 0)
                    + ($pelanggan->biaya_tambahan_2 ?: 0)
                    - (((optional($pelanggan->package)->harga ?: 0)
                        + ($pelanggan->biaya_tambahan_1 ?: 0)
                        + ($pelanggan->biaya_tambahan_2 ?: 0))
                    * ($pelanggan->diskon ?: 0) / 100),
                0, ',', '.') }}
            </span>
        </div>

        <div class="mt-2 text-sm text-gray-700">
            Bulan <strong>{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</strong>
        </div>
    </div>

    {{-- Total Pembayaran --}}
    <div class="bg-white rounded-xl shadow p-4 mb-4">
        @php
            $totalPaket = (optional($pelanggan->package)->harga ?: 0)
                         + ($pelanggan->biaya_tambahan_1 ?: 0)
                         + ($pelanggan->biaya_tambahan_2 ?: 0);
            $totalBayar = $totalPaket - ($totalPaket * ($pelanggan->diskon ?: 0) / 100);
        @endphp

        <div class="flex justify-between font-semibold mt-2 text-gray-800">
            <span>Total Bayar</span>
            <span>Rp {{ number_format($totalBayar, 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- Info --}}
    <div class="bg-blue-50 text-blue-700 text-xs text-center p-3 rounded-xl mb-4">
        Jika tekan <strong>BAYAR SEKARANG</strong> maka pembayaran ini akan langsung ditandai sebagai lunas.
    </div>

    {{-- Tombol aksi --}}
    <div class="grid grid-cols-1 gap-2">

        <div class="grid grid-cols-2 gap-2">

            {{-- Modal DETAIL PELANGGAN --}}
            <div x-data="{ open: false }">
                <button @click="open = true" class="bg-blue-900 text-white font-semibold py-3 rounded-lg w-full">
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
                            <div class="flex justify-between"><span>Tanggal Isolir</span><span class="font-semibold">{{ $pelanggan->tanggal_isolir ?: '-' }}</span></div>
                            <div class="flex justify-between"><span>Area</span><span class="font-semibold">{{ optional($pelanggan->area)->nama_area ?: '-' }}</span></div>
                            <div class="flex justify-between"><span>ODP</span><span class="font-semibold">{{ optional($pelanggan->odp)->kode ?: '-' }}</span></div>
                            <div class="flex justify-between"><span>Koordinat</span><span class="font-semibold">{{ $pelanggan->latitude ? $pelanggan->latitude . ', ' . $pelanggan->longitude : '-' }}</span></div>
                            <div class="flex justify-between"><span>Device IP</span><span class="font-semibold">{{ optional($pelanggan->device)->ip_address ?: '-' }}</span></div>
                            <div class="flex justify-between"><span>Biaya Tambahan 1</span><span class="font-semibold">{{ $pelanggan->nama_biaya_1 ?: '-' }}: Rp {{ number_format($pelanggan->biaya_tambahan_1 ?: 0,0,',','.') }}</span></div>
                            <div class="flex justify-between"><span>Biaya Tambahan 2</span><span class="font-semibold">{{ $pelanggan->nama_biaya_2 ?: '-' }}: Rp {{ number_format($pelanggan->biaya_tambahan_2 ?: 0,0,',','.') }}</span></div>
                        </div>

                        {{-- Tombol Tutup --}}
                        <div class="mt-6 text-right">
                            <button @click="open = false" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 font-semibold">Tutup</button>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Tombol Kirim WA --}}
@php
$nomor = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $pelanggan->phone));
@endphp

<a href="https://wa.me/{{ $nomor }}?text={{ urlencode($pesanWA) }}"
   target="_blank"
   class="bg-blue-500 text-white font-semibold py-3 rounded-lg w-full text-center block">
   KIRIM WA TAGIHAN
</a>



        </div>

      {{-- Tombol Bayar Sekarang + pilih bulan --}}
  @if($pelanggan->tagihan()->where('status','!=','lunas')->count() > 0)
        <form id="formBayarMulti" action="{{ route('tagihan.setLunasMulti', $pelanggan->id) }}" method="POST">
            @csrf
            <input type="hidden" name="pelanggan_id" value="{{ $pelanggan->id }}">
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

<script>
        Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: "{{ session('success') }}",
        confirmButtonColor: '#3085d6',
    });


document.getElementById('btnBayarSekarang').addEventListener('click', async function() {

    const bulanNama = [
        'Januari','Februari','Maret','April','Mei','Juni',
        'Juli','Agustus','September','Oktober','November','Desember'
    ];

    const bulanAngka = {
        'Januari':'01','Februari':'02','Maret':'03','April':'04',
        'Mei':'05','Juni':'06','Juli':'07','Agustus':'08',
        'September':'09','Oktober':'10','November':'11','Desember':'12'
    };

    const tagihanDb = @json(
        $pelanggan->tagihan()->get()->keyBy(function($t){
            return \Carbon\Carbon::parse($t->bulan)->format('Y-m');
        })
    );

    // ambil bulan-register pelanggan
    const startDate = new Date("{{ $pelanggan->tanggal_register }}");
    const thisMonth = new Date();

    // bikin list bulan: mulai bulan register -> bulan sekarang + 12 bulan ke depan
    let months = [];
    let loop = new Date(startDate);

    while (loop <= thisMonth || months.length < 18) {
        let y = loop.getFullYear();
        let m = loop.getMonth(); // 0-11

        const key = y + '-' + String(m+1).padStart(2,'0');
        const nama = bulanNama[m] + ' ' + y;

        months.push({
            key: key,
            bulan: bulanNama[m],
            tahun: y,
            namaLengkap: nama
        });

        // next month
        loop.setMonth(loop.getMonth() + 1);
    }

    // generate daftar checkbox
    let html = '<div class="text-left">';

    months.forEach((item, i) => {

        let status = '';
        let txt = '';

        // kalau ada di database
        if (tagihanDb[item.key]) {
            const t = tagihanDb[item.key];

            txt = ' - Rp ' + parseInt(t.jumlah).toLocaleString('id-ID');

            if (t.status === 'lunas') {
                status = 'checked disabled';
            }
        } else {
            txt = ' - Belum dibuat';
        }

        html += `
            <div class="flex items-center gap-2 mb-1">
                <input type="checkbox" class="bulanCheckbox"
                       data-key="${item.key}"
                       id="cb${i}"
                       value="${item.key}"
                       ${status}>
                <label for="cb${i}">${item.namaLengkap}${txt}</label>
            </div>
        `;
    });

    html += `
        </div>
        <div class="mt-2">Total Bayar: <strong id="totalBayar">Rp 0</strong></div>
    `;

    // SWEETALERT
    const { value: selected } = await Swal.fire({
        title: "Pilih Bulan yang Akan Dibayar",
        html: html,
        confirmButtonText: "Bayar Sekarang",
        showCancelButton: true,
        preConfirm: () => {
            let data = [];
            document.querySelectorAll(".bulanCheckbox:checked:not([disabled])").forEach(cb => {
                data.push(cb.value);
            });
            if (data.length === 0) {
                Swal.showValidationMessage("Pilih minimal 1 bulan!");
                return false;
            }
            return data;
        },

        didOpen: () => {
            const totalEl = document.getElementById('totalBayar');
            const boxes = document.querySelectorAll('.bulanCheckbox');

            function calcTotal() {
                let total = 0;
                boxes.forEach(cb => {
                    if (cb.checked && !cb.disabled) {
                        const key = cb.value;
                        if (tagihanDb[key]) total += parseInt(tagihanDb[key].jumlah);
                    }
                });
                totalEl.textContent = 'Rp ' + total.toLocaleString('id-ID');
            }

            boxes.forEach(cb => cb.addEventListener('change', calcTotal));
            calcTotal();
        }
    });

    if(selected){
        document.getElementById('bulanDipilih').value = selected.join(',');
        document.getElementById('formBayarMulti').submit();
    }

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


