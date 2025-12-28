@extends('layouts.app')
@section('title', 'Detail Pelanggan Baru')

@section('content')
<div class="pt-24 pb-20 max-w-5xl mx-auto px-4 sm:px-10">

    {{-- Header --}}
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">{{ $pelanggan->name }}</h2>
            <p class="text-gray-500">{{ $pelanggan->phone }} • {{ $pelanggan->area->nama_area ?? '-' }}</p>
        </div>
        <div class="flex gap-2 flex-wrap sm:flex-nowrap">
            <a href="{{ route('admin.pelanggan_baru') }}" 
               class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg flex items-center gap-1 transition">
                <i class="fa fa-arrow-left"></i> Kembali
            </a>
            <button onclick="openEditModal()" 
               class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg flex items-center gap-1 transition">
                <i class="fa fa-edit"></i> Edit
            </button>
        </div>
    </div>

    {{-- Status --}}
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

    {{-- Paket & Tagihan --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div class="bg-blue-50 p-4 rounded-lg shadow flex flex-col items-center">
            <div class="text-blue-600 text-2xl mb-1"><i class="fa fa-wifi"></i></div>
            <div class="font-bold text-lg text-center">{{ $pelanggan->package->nama_paket ?? '-' }}</div>
            <div class="text-sm text-gray-500 text-center">Paket Internet</div>
        </div>

        <div class="bg-green-50 p-4 rounded-lg shadow flex flex-col items-center">
            <div class="text-green-600 text-2xl mb-1"><i class="fa fa-money-bill"></i></div>
            <div class="font-bold text-lg text-center">
    Rp {{ number_format($pelanggan->total_tagihan, 0, ',', '.') }}
            </div>
            <div class="text-sm text-gray-500 text-center">Tagihan Terakhir</div>
        </div>
    </div>

    {{-- Teknis --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="font-semibold text-gray-700 mb-2">Perangkat</h3>
            <p class="text-gray-800">{{ $pelanggan->device->ip_address ?? '-' }}</p>
        </div>

        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="font-semibold text-gray-700 mb-2">ODP</h3>
            <p class="text-gray-800">Kode ODP: {{ $pelanggan->odp->kode ?? '-' }}</p>
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
        </div>
    </div>

</div>


{{-- ======================= MODAL EDIT ======================= --}}
<div id="editModal" 
     class="fixed inset-0 bg-black/40 hidden justify-center items-center z-50 p-4">

    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg animate-scale modal-container relative">

        <div class="overflow-y-auto max-h-[82vh] p-6">

            <h2 class="text-xl font-bold text-gray-800 mb-4">Edit Pelanggan</h2>

            <form action="{{ route('admin.pelanggan.update', $pelanggan->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Nama --}}
                <div class="mb-4">
                    <label class="text-sm text-gray-700 mb-1 block">Nama</label>
                    <input type="text" name="name" value="{{ $pelanggan->name }}"
                        class="w-full border px-3 py-2 rounded-lg focus:ring focus:ring-blue-300">
                </div>

                {{-- Phone --}}
                <div class="mb-4">
                    <label class="text-sm text-gray-700 mb-1 block">Telepon</label>
                    <input type="text" name="phone" value="{{ $pelanggan->phone }}"
                        class="w-full border px-3 py-2 rounded-lg focus:ring focus:ring-blue-300">
                </div>

                {{-- Area --}}
                <div class="mb-4">
                    <label class="text-sm text-gray-700 mb-1 block">Area</label>
                    <select id="edit_area_id" name="area_id" class="w-full border px-3 py-2 rounded-lg">
                        <option value="">-- Pilih Area --</option>
                        @foreach ($areas as $a)
                            <option value="{{ $a->id }}" {{ $pelanggan->area_id == $a->id ? 'selected' : '' }}>
                                {{ $a->nama_area }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Status --}}
                <div class="mb-4">
                    <label class="text-sm text-gray-700 mb-1 block">Status</label>
                    <select id="edit_status" name="is_active" class="w-full border px-3 py-2 rounded-lg">
                        <option value="1" {{ $pelanggan->is_active ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ !$pelanggan->is_active ? 'selected' : '' }}>Berhenti</option>
                    </select>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        Batal
                    </button>

                    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Simpan
                    </button>
                </div>

            </form>
        </div>

    </div>
</div>

{-- Include Choices.js --}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

<script>
    function openEditModal() {
        document.getElementById('editModal').classList.remove('hidden');
        document.getElementById('editModal').classList.add('flex');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
        document.getElementById('editModal').classList.remove('flex');
    }

    // Initialize Choices.js untuk dropdown modal edit
    document.addEventListener('DOMContentLoaded', function () {
        new Choices('#edit_area_id', { searchEnabled: false, shouldSort: false });
        new Choices('#edit_status', { searchEnabled: false, shouldSort: false });
    });
</script>

<style>
    .animate-scale {
        animation: modalZoom 0.25s ease-out;
    }

    @keyframes modalZoom {
        from { opacity: 0; transform: scale(0.9); }
        to   { opacity: 1; transform: scale(1); }
    }

    @media screen and (max-width: 480px) {
        .modal-container {
            max-width: 92%;
            border-radius: 14px;
        }
    }
</style>

@endsection
