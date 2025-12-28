@extends('layouts.app')
@section('title', 'Tambah User - Koordinat dan ODP')

@section('content')
<div class="pt-24 pb-20 max-w-lg mx-auto">
    <div class="bg-white p-6 rounded-xl shadow">
        {{-- Progress bar step --}}
        <div class="flex justify-between items-center mb-6">
            {{-- Step Identitas (selesai) --}}
            <div class="flex-1 text-center">
                <div class="font-semibold text-[var(--primary-color)]">Identitas</div>
                <div class="h-1 bg-[var(--primary-color)] rounded-full mt-1"></div>
            </div>

            {{-- Step Modem (selesai) --}}
            <div class="flex-1 text-center">
                <div class="font-semibold text-[var(--primary-color)]">Modem</div>
                <div class="h-1 bg-[var(--primary-color)] rounded-full mt-1"></div>
            </div>

            {{-- Step Koordinat & ODP (aktif sekarang) --}}
            <div class="flex-1 text-center">
                <div class="font-semibold text-[var(--primary-color)]">ODP</div>
                <div class="h-1 bg-[var(--primary-color)] rounded-full mt-1"></div>
            </div>
        </div>
@if ($errors->any())
    <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

        {{-- Form Koordinat dan ODP --}}
        <h2 class="font-bold text-lg text-gray-800 mb-4">Koordinat dan ODP</h2>
        <form action="{{ route('admin.simpan') }}" method="POST">
            @csrf

            {{-- Koordinat --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold mb-1">Koordinat Pelanggan</label>
                <input type="text" name="koordinat" placeholder="-6.987654, 110.456789"
                       class="w-full border rounded-lg px-3 py-2" required>
                <p class="text-xs text-gray-500 mt-1">Masukkan titik lokasi pelanggan (latitude, longitude)</p>
            </div>

            {{-- Nomor ODP --}}
<div class="mb-4">
    <label class="block text-sm font-semibold mb-1">Nomor ODP</label>
   <select id="kode_odp" name="kode_odp" class="w-full border rounded-lg px-3 py-2">
    <option value="" disabled selected>Pilih ODP</option>
    @foreach($odp as $odp)
        <option value="{{ $odp->kode }}">{{ $odp->kode }} - {{ $odp->lat ?? '' }} - {{ $odp->lng ?? '' }}</option>
    @endforeach
</select>

<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

</div>



            {{-- Keterangan ODP --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold mb-1">Keterangan Tambahan</label>
                <textarea name="keterangan_odp" rows="3"
                          class="w-full border rounded-lg px-3 py-2"
                          placeholder="Opsional, misalnya posisi tiang, arah kabel, atau catatan teknisi..."></textarea>
            </div>

            {{-- Tombol Simpan --}}
            <div class="flex justify-between mt-6">
                <a href="{{ route('admin.modem') }}" 
                   class="bg-gray-400 text-white px-4 py-2 rounded-lg hover:opacity-90">
                    Kembali
                </a>
                <button type="submit"
                        class="bg-[var(--primary-color)] text-white px-4 py-2 rounded-lg hover:opacity-90">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    new Choices('#kode_odp', {
        searchEnabled: true,
        shouldSort: false,
        itemSelectText: '',
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
