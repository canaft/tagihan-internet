@extends('layouts.app')
@section('title', 'Daftar Paket Internet')

@section('content')
<div class="pt-24 pb-24 px-3 sm:px-8 max-w-5xl mx-auto">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-2xl font-bold text-gray-800">Daftar Paket Internet</h2>

        <a href="{{ route('admin.dashboard') }}"
           class="px-4 py-2 bg-white border shadow-sm rounded-lg hover:bg-gray-100 transition flex items-center gap-2">
            <i class="fa fa-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- Flash success --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-5 border">
            {{ session('success') }}
        </div>
    @endif

    {{-- Tombol Tambah --}}
    <button id="openModalBtn" 
        class="bg-blue-600 text-white px-5 py-2.5 rounded-lg hover:bg-blue-700 mb-5 shadow w-full sm:w-auto">
        + Tambah Paket Baru
    </button>

    {{-- TABEL --}}
    <div class="bg-white border rounded-xl shadow overflow-x-auto">
        <table class="w-full min-w-[500px]">
            <thead class="bg-gray-50 border-b">
                <tr class="text-gray-700 text-sm uppercase">
                    <th class="p-3 text-left">No</th>
                    <th class="p-3 text-left">Nama Paket</th>
                    <th class="p-3 text-left">Harga</th>
                    <th class="p-3 text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @foreach($packages as $package)
                <tr class="hover:bg-gray-50 transition border-b">
                    <td class="p-3 text-sm">{{ $loop->iteration }}</td>
                    <td class="p-3 text-sm font-medium">{{ $package->nama_paket }}</td>
                    <td class="p-3 text-sm">Rp {{ number_format($package->harga, 0, ',', '.') }}</td>

                    <td class="p-3 text-center">
                        <div class="flex justify-center gap-2">
                            <button class="editBtn px-3 py-1.5 bg-yellow-400 hover:bg-yellow-500 text-white rounded text-xs"
                                data-id="{{ $package->id }}"
                                data-nama="{{ $package->nama_paket }}"
                                data-harga="{{ $package->harga }}">
                                Edit
                            </button>

                            <form action="{{ route('paket.destroy', $package->id) }}" method="POST"
                                  onsubmit="return confirm('Yakin ingin menghapus paket ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white rounded text-xs">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach

                @if($packages->isEmpty())
                <tr>
                    <td colspan="4" class="py-6 text-center text-gray-500">
                        <i class="fa fa-info-circle"></i> Belum ada data paket.
                    </td>
                </tr>
                @endif

            </tbody>
        </table>

        <div class="p-3">
            {{ $packages->links() }}
        </div>
    </div>

</div>

{{-- ================= Modal Tambah ================= --}}
<div id="paketModal"
     class="fixed inset-0 bg-black bg-opacity-40 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white p-6 rounded-xl shadow-xl w-full max-w-md relative">

        <h3 class="text-lg font-semibold mb-4">Tambah Paket Baru</h3>
        <button id="closeModalBtn"
                class="absolute top-3 right-4 text-gray-500 hover:text-gray-700 text-2xl">&times;</button>

        <form method="POST" action="{{ route('paket.store') }}">
            @csrf

            <div class="mb-4">
                <label class="font-medium text-sm">Nama Paket</label>
                <input type="text" name="nama_paket" required
                       class="w-full border rounded-lg px-3 py-2 mt-1">
            </div>

            <div class="mb-4">
                <label class="font-medium text-sm">Harga</label>
                <input type="number" name="harga" required
                       class="w-full border rounded-lg px-3 py-2 mt-1">
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" id="closeModalBtn2"
                        class="px-4 py-2 border rounded-lg hover:bg-gray-100">Batal</button>

                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Simpan
                </button>
            </div>

        </form>
    </div>
</div>

{{-- ================= Modal Edit ================= --}}
<div id="editModal"
     class="fixed inset-0 bg-black bg-opacity-40 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white p-6 rounded-xl shadow-xl w-full max-w-md relative">

        <h3 class="text-lg font-semibold mb-4">Edit Paket</h3>
        <button id="closeEditBtn"
                class="absolute top-3 right-4 text-gray-500 hover:text-gray-700 text-2xl">&times;</button>

        <form method="POST" id="editForm">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="font-medium text-sm">Nama Paket</label>
                <input type="text" name="nama_paket" id="editNama" required
                       class="w-full border rounded-lg px-3 py-2 mt-1">
            </div>

            <div class="mb-4">
                <label class="font-medium text-sm">Harga</label>
                <input type="number" name="harga" id="editHarga" required
                       class="w-full border rounded-lg px-3 py-2 mt-1">
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" id="closeEditBtn2"
                        class="px-4 py-2 border rounded-lg hover:bg-gray-100">Batal</button>

                <button type="submit"
                        class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600">
                    Update
                </button>
            </div>

        </form>

    </div>
</div>

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

{{-- Script Modal --}}
<script>
    const modal = document.getElementById('paketModal');
    const openModal = document.getElementById('openModalBtn');
    const closeModal = () => modal.classList.add('hidden');

    openModal.addEventListener('click', () => modal.classList.remove('hidden'));
    document.getElementById('closeModalBtn').onclick = closeModal;
    document.getElementById('closeModalBtn2').onclick = closeModal;

    const editModal = document.getElementById('editModal');
    const closeEdit = () => editModal.classList.add('hidden');

    document.querySelectorAll('.editBtn').forEach(btn => {
        btn.onclick = () => {
            editForm.action = "{{ route('paket.update', ':id') }}".replace(':id', btn.dataset.id);
            editNama.value = btn.dataset.nama;
            editHarga.value = btn.dataset.harga;
            editModal.classList.remove('hidden');
        };
    });

    document.getElementById('closeEditBtn').onclick = closeEdit;
    document.getElementById('closeEditBtn2').onclick = closeEdit;
</script>
 
@endsection
