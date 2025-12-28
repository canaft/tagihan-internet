@extends('layouts.app')
@section('title', 'ODP Management')

@section('content')
<div class="min-h-screen bg-[#F5EEEB] pb-28">

    {{-- HEADER FIXED --}}
    <div class="fixed top-0 left-0 w-full bg-[#2A4156] text-white py-4 px-6 shadow-md z-50">
        <div class="flex justify-between items-center">
            <a href="{{ route('setting') }}" class="flex items-center space-x-2 hover:text-gray-200 transition">
                <i class="fas fa-arrow-left text-lg"></i>
                <span class="text-sm font-medium">Kembali</span>
            </a>
            <h1 class="text-lg font-semibold">ODP Management</h1>
            <span></span>
        </div>
    </div>

    {{-- KONTEN UTAMA --}}
    <div class="pt-28 px-4">

        {{-- Tombol Tambah ODP --}}
        <div class="mb-4">
            <button onclick="openModal('create')" 
                class="w-full bg-blue-600 text-white py-2 rounded-xl shadow hover:bg-blue-700 transition font-semibold">
                + Tambah ODP
            </button>
        </div>

        {{-- Daftar ODC & ODP --}}
        <div class="space-y-4">
            @forelse($odcs as $odc)
                <div class="bg-white p-4 rounded-2xl shadow-md">
                    {{-- Nama ODC --}}
                    <p class="font-semibold text-[#2A4156] mb-2">ODC: {{ $odc->nama }}</p>

                    @forelse($odc->odps as $odp)
                        <div class="flex justify-between items-center bg-gray-50 p-3 rounded-xl mb-2 shadow-sm hover:shadow-md transition">
                            <div>
                                <p class="font-medium text-[#2A4156]">ODP: {{ $odp->nama_odp }}</p>
                                @if($odp->lat && $odp->lng)
                                    <p class="text-gray-400 text-xs mt-1">Lat: {{ $odp->lat }}, Lng: {{ $odp->lng }}</p>
                                @endif
                            </div>
                            <div class="flex space-x-2">
                                <button onclick="openModal('edit', {{ $odp }})" 
                                        class="text-blue-500 font-medium hover:underline">Edit</button>
                                <form action="{{ route('odp.destroy', $odp->id) }}" method="POST" 
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus ODP ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 font-medium hover:underline">Hapus</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm ml-2">Belum ada ODP di ODC ini.</p>
                    @endforelse
                </div>
            @empty
                <p class="text-gray-500 text-center mt-10">Belum ada ODC/ODP.</p>
            @endforelse
        </div>

    </div>

{{-- MODAL --}}
<div id="odpModal" class="fixed inset-0 bg-black bg-opacity-40 hidden z-50 flex justify-center items-center px-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 relative animate-slide-in">
        <h2 id="modalTitle" class="text-2xl font-semibold mb-4 text-[#2A4156]">Tambah ODP</h2>

        <form id="odpForm" method="POST">
            @csrf
            <input type="hidden" name="_method" id="methodInput" value="POST">

            <div class="mb-3">
                <label class="block mb-1 font-medium text-gray-700">Nama ODP</label>
                <input type="text" name="nama_odp" id="nama_odp" class="w-full border rounded-lg px-3 py-2" required>
            </div>

            <div class="mb-3">
                <label class="block mb-1 font-medium text-gray-700">Pilih ODC</label>
                <select name="odc_id" id="odc_id" class="w-full border rounded-lg px-3 py-2" required>
                    <option value="">-- Pilih ODC --</option>
                    @foreach($odcs as $odcOption)
                        <option value="{{ $odcOption->id }}">{{ $odcOption->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="block mb-1 font-medium text-gray-700">Latitude</label>
                <input type="text" name="lat" id="lat" class="w-full border rounded-lg px-3 py-2">
            </div>

            <div class="mb-3">
                <label class="block mb-1 font-medium text-gray-700">Longitude</label>
                <input type="text" name="lng" id="lng" class="w-full border rounded-lg px-3 py-2">
            </div>

            <div class="flex justify-end space-x-3 mt-4">
                <button type="button" onclick="closeModal()" 
                        class="px-4 py-2 rounded-xl bg-gray-300 hover:bg-gray-400 transition">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700 transition">Simpan</button>
            </div>
        </form>
    </div>
</div>
</div>

{{-- Include Choices.js --}}
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

<script>
    function openModal(type, odp = null) {
        const modal = document.getElementById('odpModal');
        const form = document.getElementById('odpForm');
        const title = document.getElementById('modalTitle');
        const methodInput = document.getElementById('methodInput');

        if(type === 'create') {
            title.textContent = 'Tambah ODP';
            form.action = "{{ route('odp.store') }}";
            methodInput.value = 'POST';
            form.nama_odp.value = '';
            form.odc_id.value = '';
            form.lat.value = '';
            form.lng.value = '';
        }

        if(type === 'edit') {
            title.textContent = 'Edit ODP';
            form.action = `/odp/${odp.id}`;
            methodInput.value = 'PUT';
            form.nama_odp.value = odp.nama_odp;
            form.odc_id.value = odp.odc_id;
            form.lat.value = odp.lat ?? '';
            form.lng.value = odp.lng ?? '';
        }

        modal.classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('odpModal').classList.add('hidden');
    }

    // Initialize Choices.js untuk dropdown ODC
    new Choices('#odc_id', {
        searchEnabled: false,
        shouldSort: false
    });
</script>

<style>
@keyframes slide-in {
  0% { transform: translateY(-30px); opacity: 0; }
  100% { transform: translateY(0); opacity: 1; }
}
.animate-slide-in {
  animation: slide-in 0.3s ease-out;
}
</style>
@endsection
