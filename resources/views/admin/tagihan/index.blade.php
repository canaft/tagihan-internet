@extends('layouts.app')

@section('title', 'Tagihan Bulanan')

@section('content')
<div class="pt-24 px-4">

    <h2 class="text-xl font-semibold mb-4">Tagihan Bulanan</h2>

    {{-- Tombol Kembali --}}
    <a href="{{ url()->previous() }}" 
       class="inline-block bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400 mb-4">
        &larr; Kembali
    </a>

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="bg-blue-100 text-blue-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(!$adaTagihan)
    <div class="bg-yellow-100 text-yellow-800 p-3 rounded mb-4 text-center">
        Belum ada tagihan di bulan ini.
    </div>
    <div class="flex justify-end mb-4">
        <form method="POST" action="{{ route('tagihan.generate') }}">
            @csrf
            <input type="hidden" name="bulan" value="{{ $bulan }}">
            <button type="submit" 
                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                Generate Tagihan
            </button>
        </form>
    </div>
@else
    {{-- Tampilkan tabel tagihan seperti biasa --}}
@endif


    {{-- Form Pilih Bulan --}}
    <form method="GET" action="{{ route('tagihan.index') }}" 
          class="flex flex-col sm:flex-row items-center gap-2 bg-white p-4 rounded-lg shadow mb-4">
        <label for="bulan" class="font-semibold text-sm sm:text-base">Pilih Bulan:</label>
        <input type="month" name="bulan" id="bulan" value="{{ $bulan }}" 
               class="border rounded px-3 py-2 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        <button type="submit" 
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm sm:text-base">
            Tampilkan
        </button>
    </form>

    {{-- Tabel Data Pelanggan --}}
    <div class="overflow-x-auto bg-white p-4 rounded-lg shadow">
        <table class="w-full table-auto border border-gray-200 rounded-lg min-w-[700px]">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="p-3 border-b text-left">No</th>
                    <th class="p-3 border-b text-left">Nama Pelanggan</th>
                    <th class="p-3 border-b text-left">Paket</th>
                    <th class="p-3 border-b text-right">Harga Paket</th>
                    <th class="p-3 border-b text-right">Biaya Tambahan 1</th>
                    <th class="p-3 border-b text-right">Biaya Tambahan 2</th>
                    <th class="p-3 border-b text-right">Total</th>
                    <th class="p-3 border-b text-left">Wilayah</th>
                </tr>
            </thead>
            <tbody>
                @php $grandTotal = 0; @endphp

                @if($pelanggans->isEmpty())
                    <tr>
                        <td colspan="8" class="p-3 text-center text-sm text-gray-500">
                            Tidak ada tagihan di bulan ini.
                        </td>
                    </tr>
                @else
                    @foreach($pelanggans as $index => $pelanggan)
                        @php
                            // ambil tagihan bulan ini, bisa null
                            $tagihan = $pelanggan->tagihans->where('bulan', $bulan)->first();
                            $hargaPaket = $tagihan->jumlah ?? 0;
                            $biaya1 = $pelanggan->biaya_tambahan_1 ?? 0;
                            $biaya2 = $pelanggan->biaya_tambahan_2 ?? 0;
                            $total = $hargaPaket + $biaya1 + $biaya2;
                            $grandTotal += $total;
                        @endphp
                        <tr class="odd:bg-gray-50 even:bg-white hover:bg-gray-100 transition">
                            <td class="p-2 sm:p-3 border-b text-left text-sm">{{ $index + 1 }}</td>
                            <td class="p-2 sm:p-3 border-b text-left text-sm">{{ $pelanggan->name }}</td>
                            <td class="p-2 sm:p-3 border-b text-left text-sm">{{ $pelanggan->package->nama_paket ?? '-' }}</td>
                            <td class="p-2 sm:p-3 border-b text-right text-sm">Rp {{ number_format($hargaPaket,0,',','.') }}</td>
                            <td class="p-2 sm:p-3 border-b text-right text-sm">Rp {{ number_format($biaya1,0,',','.') }}</td>
                            <td class="p-2 sm:p-3 border-b text-right text-sm">Rp {{ number_format($biaya2,0,',','.') }}</td>
                            <td class="p-2 sm:p-3 border-b text-right font-semibold text-sm">Rp {{ number_format($total,0,',','.') }}</td>
                            <td class="p-2 sm:p-3 border-b text-left text-sm">{{ $pelanggan->area->nama_area ?? '-' }}</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
            <tfoot class="bg-gray-200 font-semibold">
                <tr>
                    <td colspan="6" class="p-3 text-right text-sm">Total Keseluruhan</td>
                    <td class="p-3 text-right text-sm">Rp {{ number_format($grandTotal,0,',','.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>

</div>

{{-- Modal Generate --}}
<div id="generateModal" 
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 p-4">
    <div class="bg-white p-6 rounded-lg w-full max-w-md relative shadow-lg">
        <h3 class="text-lg font-semibold mb-2">Konfirmasi Generate Tagihan</h3>
        <p class="text-gray-600 mb-6 text-sm">
            Apakah Anda yakin ingin membuat tagihan untuk bulan 
            <span class="font-semibold">{{ \Carbon\Carbon::parse($bulan)->translatedFormat('F Y') }}</span>?
        </p>
        <div class="flex flex-col sm:flex-row justify-end gap-2">
            <button type="button" id="cancelGenerate"
                    class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-100 w-full sm:w-auto">
                Batal
            </button>
            <button type="submit" form="generateForm"
                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 w-full sm:w-auto">
                Ya, Generate Sekarang
            </button>
        </div>
        <button id="closeGenerateModal"
                class="absolute top-2 right-3 text-gray-500 hover:text-gray-700 text-2xl leading-none">
            &times;
        </button>
    </div>
</div>
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

{{-- Script Modal --}}
<script>
    const openModalBtn = document.getElementById('openGenerateModal');
    const modal = document.getElementById('generateModal');
    const closeModalBtn = document.getElementById('closeGenerateModal');
    const cancelBtn = document.getElementById('cancelGenerate');

    openModalBtn?.addEventListener('click', () => modal.classList.remove('hidden'));
    closeModalBtn?.addEventListener('click', () => modal.classList.add('hidden'));
    cancelBtn?.addEventListener('click', () => modal.classList.add('hidden'));

    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.add('hidden');
        }
    });
</script>

@endsection
