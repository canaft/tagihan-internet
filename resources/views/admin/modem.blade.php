@extends('layouts.app')
@section('title', 'Tambah User - Modem')

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

            {{-- Step Modem (aktif) --}}
            <div class="flex-1 text-center">
                <div class="font-semibold text-[var(--primary-color)]">Modem</div>
                <div class="h-1 bg-[var(--primary-color)] rounded-full mt-1"></div>
            </div>

            {{-- Step Koordinat & ODP (belum aktif) --}}
            <div class="flex-1 text-center">
                <div class="font-semibold text-gray-400"> ODP</div>
                <div class="h-1 bg-gray-200 rounded-full mt-1"></div>
            </div>
        </div>

        {{-- Tentang Mikrotik --}}
        <div class="text-center mb-6">
            <h2 class="font-bold text-lg text-gray-800 mb-2">Tentang Mikrotik Pelanggan</h2>
            <p class="text-sm text-gray-500 mb-4">Pilih Mikrotik (Opsional)</p>

           {{-- Dropdown Mikrotik --}}
{{-- Dropdown Mikrotik + Tombol LANJUT --}}
<form action="{{ route('admin.modem') }}" method="POST">
    @csrf

    <div class="mb-3">
        <label class="block text-sm font-semibold mb-1">Mikrotik (Opsional)</label>
        <select id="device_id" name="device_id" class="w-full border rounded-lg px-3 py-2">
            <option value="">-- Pilih Mikrotik --</option>
            @foreach($devices as $device)
                <option value="{{ $device->id }}" {{ old('device_id') == $device->id ? 'selected' : '' }}>
                    {{ $device->name }} ({{ $device->ip_address }})
                </option>
            @endforeach
        </select>
    </div>

    {{-- Tombol aksi --}}
    <div class="flex gap-3 justify-center mt-4">
        <a href="{{ route('pelanggan.create') }}" 
           class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 w-full text-center">
            KEMBALI
        </a>

        <button type="submit" 
           class="bg-[var(--primary-color)] text-white px-6 py-2 rounded-lg hover:opacity-90 w-full">
            LANJUT
        </button>
    </div>
</form>

{{-- Include Choices.js --}}
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />



        </div>

        {{-- Catatan --}}
        <div class="mt-4 text-sm text-gray-500 border rounded-lg p-3">
            <strong>Catatan:</strong><br>
            Anda bisa melewati proses ini jika tidak dibutuhkan.
        </div>
    </div>
</div>

<script>
    // Inisialisasi Choices untuk Mikrotik
   new Choices('#device_id', {
    searchEnabled: false, // ← ini bikin search-nya nonaktif
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
