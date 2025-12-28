@extends('layouts.app')
@section('title', 'Detail Pelanggan')

@section('content')
<div class="pt-24 pb-20 max-w-4xl mx-auto">

    {{-- Header dengan tombol modern --}}
    <div class="bg-white rounded-xl shadow p-6 mb-6 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">{{ $pelanggan->name }}</h2>
            <p class="text-gray-500">{{ $pelanggan->phone }} • {{ $pelanggan->area->nama_area ?? '-' }}</p>
        </div>
        <div class="flex gap-2 flex-wrap sm:flex-nowrap">
            <a href="{{ route('admin.pelanggan_semua') }}" 
               class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg flex items-center gap-1 transition">
                <i class="fa fa-arrow-left"></i> Kembali
            </a>
            <button onclick="openEditModal()" 
               class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg flex items-center gap-1 transition">
                <i class="fa fa-edit"></i> Edit
            </button>
        </div>
    </div>

    {{-- Status Aktif & Tagihan --}}
    <div class="flex gap-2 mb-6 flex-wrap">
        <span class="px-3 py-1 text-sm rounded-full 
            {{ $pelanggan->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
            {{ $pelanggan->is_active ? 'Aktif' : 'Nonaktif' }}
        </span>

        @php
            $statusTagihan = $pelanggan->tagihan()->latest('bulan')->value('status') ?? 'belum bayar';
        @endphp

        <span class="px-3 py-1 text-sm rounded-full
            {{ $statusTagihan == 'lunas' ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' }}">
            {{ $statusTagihan == 'lunas' ? 'Lunas' : 'Belum Bayar' }}
        </span>
    </div>

    {{-- Informasi Paket dan Tagihan --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div class="bg-blue-50 p-4 rounded-lg shadow flex flex-col items-center">
            <div class="text-blue-600 text-2xl mb-1"><i class="fa fa-wifi"></i></div>
            <div class="font-bold text-lg text-center">{{ $pelanggan->package->nama_paket ?? '-' }}</div>
            <div class="text-sm text-gray-500 text-center">Paket Internet</div>
        </div>

        <div class="bg-green-50 p-4 rounded-lg text-center shadow flex flex-col items-center">
            <div class="text-green-600 text-2xl mb-1"><i class="fa fa-money-bill"></i></div>
            <div class="font-bold text-lg">Rp{{ number_format($pelanggan->tagihan_terakhir ?? 0, 0, ',', '.') }}</div>
            <div class="text-sm text-gray-500">Tagihan Terakhir</div>
        </div>
    </div>

    {{-- Informasi Teknis --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="font-semibold text-gray-700 mb-2">Perangkat</h3>
            <p class="text-gray-800">{{ $pelanggan->device->ip_address ?? '-' }}</p>
            <p class="text-sm text-gray-500">Mikrotik / Modem</p>
        </div>

        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="font-semibold text-gray-700 mb-2">ODP & Koordinat</h3>
            <p class="text-gray-800 mb-1">Kode ODP: {{ $pelanggan->odp->kode ?? '-' }}</p>
        </div>
    </div>

    {{-- Info Tambahan --}}
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <h3 class="font-semibold text-gray-700 mb-3">Informasi Tambahan</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
            <div>
                <span class="text-gray-500">Tanggal Register:</span>
                <p class="font-medium">{{ \Carbon\Carbon::parse($pelanggan->tanggal_register)->format('d M Y') }}</p>
            </div>
            <div>
                <span class="text-gray-500">Tanggal Tagihan:</span>
                <p class="font-medium">{{ \Carbon\Carbon::parse($pelanggan->tanggal_tagihan)->format('d M Y') }}</p>
            </div>
            <div>
                <span class="text-gray-500">Area:</span>
                <p class="font-medium">{{ $pelanggan->area->nama_area ?? '-' }}</p>
            </div>
            <div>
                <span class="text-gray-500">Keterangan ODP:</span>
                <p class="text-gray-800 mb-1">{{ $pelanggan->odp->kode ?? '-' }}</p>
            </div>
        </div>
    </div>

</div>

{{-- ======================= MODAL EDIT PELANGGAN ======================= --}}
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-lg p-6 animate-fade">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Edit Pelanggan</h2>
        <form action="{{ route('admin.pelanggan.update', $pelanggan->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Nama --}}
            <div class="mb-3">
                <label class="text-sm text-gray-700">Nama</label>
                <input type="text" name="name" value="{{ $pelanggan->name }}"
                    class="w-full mt-1 px-3 py-2 border rounded-lg focus:ring focus:ring-blue-300">
            </div>

            {{-- Telepon --}}
            <div class="mb-3">
                <label class="text-sm text-gray-700">Telepon</label>
                <input type="text" name="phone" value="{{ $pelanggan->phone }}"
                    class="w-full mt-1 px-3 py-2 border rounded-lg focus:ring focus:ring-blue-300">
            </div>

            {{-- Area --}}
            <div class="mb-3">
                <label class="text-sm text-gray-700">Area</label>
                <select id="area_id_edit" name="area_id" class="w-full mt-1 px-3 py-2 border rounded-lg">
                    @foreach ($areas as $a)
                        <option value="{{ $a->id }}" {{ $pelanggan->area_id == $a->id ? 'selected' : '' }}>
                            {{ $a->nama_area }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- ODP --}}
            <div class="mb-3">
                <label class="text-sm text-gray-700">ODP</label>
                <select id="odp_id_edit" name="odp_id" class="w-full mt-1 px-3 py-2 border rounded-lg">
                    @foreach ($odps as $o)
                        <option value="{{ $o->id }}" {{ $pelanggan->odp_id == $o->id ? 'selected' : '' }}>
                            {{ $o->kode }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Status Aktif --}}
            <div class="mb-3">
                <label class="text-sm text-gray-700">Status</label>
                <select id="is_active_edit" name="is_active" class="w-full mt-1 px-3 py-2 border rounded-lg">
                    <option value="1" {{ $pelanggan->is_active ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ !$pelanggan->is_active ? 'selected' : '' }}>Berhenti</option>
                </select>
            </div>

            <div class="flex justify-end gap-2 mt-4">
                <button type="button" onclick="closeEditModal()"
                    class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">Batal</button>
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ======================= SCRIPT MODAL ======================= --}}
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

<script>
    function openEditModal() {
        document.getElementById('editModal').classList.remove('hidden');
        document.getElementById('editModal').classList.add('flex');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
        document.getElementById('editModal').classList.remove('flex');
    }

    // ===============================
    // Choices.js untuk dropdown modal edit
    // ===============================
    new Choices('#area_id_edit', { searchEnabled: false, shouldSort: false });
    new Choices('#package_id_edit', { searchEnabled: false, shouldSort: false });
    new Choices('#odp_id_edit', { searchEnabled: false, shouldSort: false });
    new Choices('#is_active_edit', { searchEnabled: false, shouldSort: false });
</script>


{{-- ======================= SCRIPT MODAL ======================= --}}
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

<script>
    function openEditModal() {
        document.getElementById('editModal').classList.remove('hidden');
        document.getElementById('editModal').classList.add('flex');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
        document.getElementById('editModal').classList.remove('flex');
    }

    // ===============================
    // Choices.js untuk dropdown modal edit
    // ===============================
    new Choices('#area_id_edit', { searchEnabled: false, shouldSort: false });
    new Choices('#odp_id_edit', { searchEnabled: false, shouldSort: false });
    new Choices('#is_active_edit', { searchEnabled: false, shouldSort: false });
</script>

{{-- Animasi sederhana --}}
<style>
    .animate-fade {
        animation: fadeIn 0.25s ease;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.95); }
        to   { opacity: 1; transform: scale(1); }
    }
</style>


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