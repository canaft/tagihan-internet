@extends('layouts.app')
@section('title','Manajemen Area')

@section('content')
<div class="min-h-screen bg-gray-100 pb-28">

    {{-- NAVBAR --}}
    <div class="fixed top-0 left-0 w-full bg-[#2A4156] text-white py-4 px-6 shadow-md z-50">
        <div class="flex justify-between items-center">
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center space-x-2 hover:text-gray-200 transition">
                <i class="fas fa-arrow-left"></i>
                <span class="text-sm font-medium">Kembali</span>
            </a>
            <h1 class="text-lg font-semibold">Manajemen Area</h1>
            <span></span>
        </div>
    </div>

    {{-- CONTENT --}}
    <div class="pt-28 px-4 max-w-4xl mx-auto">

        {{-- Flash --}}
        @if(session('success'))
            <div class="mb-4 bg-green-100 text-green-700 p-3 rounded-xl shadow">
                {{ session('success') }}
            </div>
        @endif

        {{-- Tombol Tambah --}}
        <button onclick="openAddModal()"
            class="w-full bg-blue-600 text-white py-2 rounded-xl shadow hover:bg-blue-700 transition font-semibold mb-5">
            + Tambah Area
        </button>

        {{-- LIST AREA --}}
        <div class="space-y-4">
            @forelse($areas as $area)
                <div class="bg-white p-4 rounded-2xl shadow-md border">

                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-semibold text-[#2A4156] text-lg">
                                {{ $area->nama_area }}
                            </p>
                        </div>

                        <div class="flex space-x-3">
                            <button onclick="openEditModal({{ $area->id }})"
                                class="text-blue-600 font-semibold hover:underline">
                                Edit
                            </button>

                            <form action="{{ route('admin.areas.destroy',$area) }}"
                                  method="POST"
                                  onsubmit="return confirm('Yakin ingin menghapus area ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="text-red-600 font-semibold hover:underline">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>

                </div>

                {{-- MODAL EDIT --}}
                <div id="editModal-{{ $area->id }}"
                    class="fixed inset-0 bg-black bg-opacity-40 hidden z-50 flex justify-center items-center">

                    <div class="bg-white rounded-2xl shadow-lg w-full max-w-md p-6 relative animate-slide-in">

                        <h2 class="text-2xl font-semibold mb-4 text-[#2A4156]">
                            Edit Area
                        </h2>

                        <form action="{{ route('admin.areas.update',$area) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-4">
                                <label class="block mb-1 font-medium text-gray-700">
                                    Nama Area
                                </label>
                                <input type="text" name="nama_area"
                                    value="{{ $area->nama_area }}"
                                    class="w-full border rounded-lg px-3 py-2" required>
                            </div>

                            <div class="flex justify-end space-x-3">
                                <button type="button"
                                    onclick="closeEditModal({{ $area->id }})"
                                    class="px-4 py-2 rounded-xl bg-gray-300 hover:bg-gray-400 transition">
                                    Batal
                                </button>
                                <button type="submit"
                                    class="px-4 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700 transition">
                                    Update
                                </button>
                            </div>
                        </form>

                    </div>
                </div>

            @empty
                <p class="text-gray-500 text-center mt-10">Belum ada data area.</p>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $areas->links() }}
        </div>

    </div>
</div>

{{-- MODAL TAMBAH --}}
<div id="addModal"
    class="fixed inset-0 bg-black bg-opacity-40 hidden z-50 flex justify-center items-center">

    <div class="bg-white rounded-2xl shadow-lg w-full max-w-md p-6 relative animate-slide-in">

        <h2 class="text-2xl font-semibold mb-4 text-[#2A4156]">
            Tambah Area
        </h2>

        <form action="{{ route('admin.areas.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block mb-1 font-medium text-gray-700">
                    Nama Area
                </label>
                <input type="text" name="nama_area"
                    class="w-full border rounded-lg px-3 py-2"
                    placeholder="Nama area..." required>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeAddModal()"
                    class="px-4 py-2 rounded-xl bg-gray-300 hover:bg-gray-400 transition">
                    Batal
                </button>
                <button type="submit"
                    class="px-4 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700 transition">
                    Simpan
                </button>
            </div>
        </form>

    </div>
</div>

{{-- SCRIPT --}}
<script>
function openAddModal(){
    document.getElementById('addModal').classList.remove('hidden')
}
function closeAddModal(){
    document.getElementById('addModal').classList.add('hidden')
}
function openEditModal(id){
    document.getElementById('editModal-'+id).classList.remove('hidden')
}
function closeEditModal(id){
    document.getElementById('editModal-'+id).classList.add('hidden')
}
</script>

<style>
@keyframes slide-in {
    0% { transform: translateY(-30px); opacity: 0; }
    100% { transform: translateY(0); opacity: 1; }
}
.animate-slide-in {
    animation: slide-in .3s ease-out;
}
</style>

@endsection
{{-- ======================= BOTTOM MENU MOBILE ======================= --}}
@section('bottom-menu')
<div class="flex justify-between gap-2 p-2 bg-white shadow-inner border-t fixed bottom-0 left-0 right-0 z-50">
   @php
        $mobileCards = [
            ['icon'=>'🏠','title'=>'Beranda','link'=>route('admin.dashboard')],
            ['icon'=>'💳','title'=>'Transaksi','link'=>route('admin.pelanggan_lunas')],
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
