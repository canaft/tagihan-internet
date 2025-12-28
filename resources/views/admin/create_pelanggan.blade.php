@extends('layouts.app')
@section('title', 'Tambah Pelanggan')

@section('content')
<div class="pt-24 pb-20 max-w-lg mx-auto">
    <div class="bg-white p-6 rounded-xl shadow">

        {{-- Progress bar --}}
        <div class="flex justify-between mb-6 text-center">
            <div class="flex-1">
                <div class="font-semibold text-[var(--primary-color)]">Identitas</div>
                <div class="h-1 bg-[var(--primary-color)] rounded-full mt-1"></div>
            </div>
            <div class="flex-1">
                <div class="font-semibold text-gray-400">Modem</div>
                <div class="h-1 bg-gray-200 rounded-full mt-1"></div>
            </div>
            <div class="flex-1">
                <div class="font-semibold text-gray-400">ODP</div>
                <div class="h-1 bg-gray-200 rounded-full mt-1"></div>
            </div>
        </div>

        <h2 class="text-xl font-bold mb-4 text-[var(--primary-color)]">Tambah Pelanggan Baru</h2>

        <form action="{{ route('pelanggan.store') }}" method="POST">
            @csrf

            {{-- Nama --}}
            <div class="mb-3">
                <label class="block text-sm font-semibold mb-1">Nama Pelanggan</label>
                <input type="text" name="name" required class="w-full border rounded-lg px-3 py-2"
                       value="{{ old('name') }}">
            </div>

            {{-- Nomor HP --}}
            <div class="mb-3">
                <label class="block text-sm font-semibold mb-1">Nomor HP</label>
                <input type="text" name="phone" required class="w-full border rounded-lg px-3 py-2"
                       value="{{ old('phone') }}">
            </div>

            {{-- Paket --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold mb-1">Paket Internet</label>
                <select id="paket_id" name="paket_id" required class="w-full border rounded-lg px-3 py-2">
                    <option value="">-- Pilih Paket --</option>
                    @foreach($pakets as $paket)
                        <option value="{{ $paket->id }}">
                            {{ $paket->nama_paket }} - Rp {{ number_format($paket->harga,0,',','.') }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Tanggal Register --}}
            <div class="mb-3">
                <label class="block text-sm font-semibold mb-1">Tanggal Register</label>
                <input type="date" id="tanggal_register" name="tanggal_register" required
                       class="w-full border rounded-lg px-3 py-2"
                       value="{{ old('tanggal_register') }}">
            </div>

            {{-- Tanggal Tagihan (AUTO bulan berikutnya) --}}
            <div class="mb-3">
                <label class="block text-sm font-semibold mb-1">Tanggal Tagihan</label>
                <input type="date" id="tanggal_tagihan" name="tanggal_tagihan" required
                       class="w-full border rounded-lg px-3 py-2"
                       value="{{ old('tanggal_tagihan') }}">
            </div>

            {{-- Area --}}
            <div class="mb-3">
                <label class="block text-sm font-semibold mb-1">Area</label>
                <select id="area_id" name="area_id" class="w-full border rounded-lg px-3 py-2">
                    <option value="">-- Pilih Area --</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}">{{ $area->nama_area }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Diskon --}}
            <div class="mb-3">
                <label class="block text-sm font-semibold mb-1">Diskon (%)</label>
                <input type="number" name="diskon" min="0" max="100" step="0.01"
                       value="{{ old('diskon') ?? 0 }}"
                       class="w-full border rounded-lg px-3 py-2">
            </div>

            {{-- Toggle Biaya Tambahan 1 --}}
            <div class="mb-3">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="toggleBiaya1" class="form-checkbox">
                    <span class="ml-2 font-semibold text-sm">Tambah Biaya Tambahan 1?</span>
                </label>
            </div>

            <div id="biaya1Fields" class="mb-3 hidden">
                <label class="block text-sm font-semibold mb-1">Nama Biaya Tambahan 1</label>
                <input type="text" name="nama_biaya_1" class="w-full border rounded-lg px-3 py-2">

                <label class="block text-sm font-semibold mb-1 mt-2">Biaya Tambahan 1 (Rp)</label>
                <input type="number" name="biaya_tambahan_1" class="w-full border rounded-lg px-3 py-2">
            </div>

            {{-- Toggle Biaya Tambahan 2 --}}
            <div class="mb-3">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="toggleBiaya2" class="form-checkbox">
                    <span class="ml-2 font-semibold text-sm">Tambah Biaya Tambahan 2?</span>
                </label>
            </div>

            <div id="biaya2Fields" class="mb-3 hidden">
                <label class="block text-sm font-semibold mb-1">Nama Biaya Tambahan 2</label>
                <input type="text" name="nama_biaya_2" class="w-full border rounded-lg px-3 py-2">

                <label class="block text-sm font-semibold mb-1 mt-2">Biaya Tambahan 2 (Rp)</label>
                <input type="number" name="biaya_tambahan_2" class="w-full border rounded-lg px-3 py-2">
            </div>

            {{-- Tombol --}}
            <div class="flex justify-between mt-6">
                <a href="{{ route('pelanggan.index') }}"
                   class="bg-gray-400 text-white px-4 py-2 rounded-lg hover:opacity-80">
                   Kembali
                </a>

                <button type="submit" 
                   class="bg-[var(--primary-color)] text-white px-4 py-2 rounded-lg hover:opacity-90">
                    Lanjut
                </button>
            </div>

        </form>
    </div>
</div>

{{-- Include Choices.js --}}
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

<script>
    // Biaya tambahan toggle
    document.getElementById('toggleBiaya1').onchange = () =>
        document.getElementById('biaya1Fields').classList.toggle('hidden');

    document.getElementById('toggleBiaya2').onchange = () =>
        document.getElementById('biaya2Fields').classList.toggle('hidden');

    // ===============================
    // Auto Tanggal Tagihan = Bulan Berikutnya
    // ===============================
    const reg = document.getElementById('tanggal_register');
    const tagih = document.getElementById('tanggal_tagihan');

    reg.addEventListener('change', () => {
        if (!reg.value) return;

        let tgl = new Date(reg.value);

        // tambah 1 bulan
        tgl.setMonth(tgl.getMonth() + 1);

        // Format YYYY-MM-DD
        let bulan = (tgl.getMonth() + 1).toString().padStart(2, '0');
        let hari = tgl.getDate().toString().padStart(2, '0');
        let tahun = tgl.getFullYear();

        tagih.value = `${tahun}-${bulan}-${hari}`;
    });

    // Dropdown UI
    new Choices('#paket_id', { searchEnabled: false, shouldSort: false });
    new Choices('#area_id', { searchEnabled: false, shouldSort: false });
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
