@extends('layouts.app')

@section('title', 'Pengaduan Pelanggan')

@section('content')
<div class="pt-24 px-4 max-w-4xl mx-auto">

    <h2 class="text-xl font-semibold mb-4">Pengaduan Pelanggan</h2>

    {{-- Tombol Kembali --}}
    <a href="{{ route('admin.dashboard') }}"
       class="inline-flex items-center gap-1 bg-gray-300 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-400 mb-4 transition">
        <span class="text-lg">&larr;</span>
        <span>Kembali</span>
    </a>

    {{-- Flash --}}
    @if(session('success'))
        <div class="bg-blue-100 text-blue-800 p-3 rounded-lg mb-4 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Tombol Tambah Pengaduan Baru --}}
    <button id="openModalBtn" 
        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 mb-4 w-full sm:w-auto transition">
        Tambah Pengaduan Baru
    </button>

    {{-- ================= TABEL ================= --}}
    <div class="overflow-x-auto bg-white p-4 rounded-2xl shadow-md">
        <table class="w-full border border-gray-200 text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 border-b text-left">No</th>
                    <th class="p-3 border-b text-left">Pelanggan</th>
                    <th class="p-3 border-b text-left">Keluhan</th>
                    <th class="p-3 border-b text-center">Status</th>
                    <th class="p-3 border-b text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pengaduans as $p)
                <tr class="odd:bg-gray-50 hover:bg-gray-100 transition">
                    <td class="p-3 border-b">{{ $loop->iteration }}</td>

                    <td class="p-3 border-b font-medium">
                        {{ $p->pelanggan->name ?? '-' }}
                    </td>

                    <td class="p-3 border-b">
                        <span class="font-semibold">{{ $p->jenis_pengaduan }}</span><br>
                        <span class="text-gray-600 text-xs">
                            {{ $p->deskripsi }}
                        </span>
                    </td>

                    <td class="p-3 border-b text-center">
                        @if($p->status == 'Menunggu')
                            <span class="px-2 py-1 bg-gray-200 rounded text-xs">
                                Menunggu
                            </span>
                        @elseif($p->status == 'Dikirim ke Teknisi')
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs block">
                                {{ $p->teknisi->name ?? 'Belum dipilih' }}
                            </span>
                        @else
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs">
                                Selesai
                            </span>
                        @endif
                    </td>

                    {{-- AKSI --}}
                    <td class="p-3 border-b text-center">
<button
    class="text-blue-600 hover:underline font-semibold text-sm"
    onclick="openDetailModal(
        '{{ $p->pelanggan->name ?? '-' }}',
        '{{ $p->jenis_pengaduan }}',
        '{{ $p->deskripsi }}',
        '{{ $p->status }}',
        '{{ $p->bukti_foto ? asset('storage/'.$p->bukti_foto) : '' }}',
        '{{ optional($p->created_at)->format('Y-m-d H:i:s') }}',
        '{{ $p->status === 'Selesai'
            ? ($p->teknisi->name ?? '-') . ', ' . optional($p->updated_at)->format('Y-m-d H:i:s')
            : '-' }}'
    )">
    Detail
</button>


                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            {{ $pengaduans->links() }}
        </div>
    </div>
</div>

{{-- ================= MODAL TAMBAH PENGADUAN ================= --}}
<div id="pengaduanModal" 
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 p-4 overflow-y-auto">

    <div class="bg-white p-6 rounded-2xl w-full max-w-lg relative max-h-screen overflow-y-auto">

        <h3 class="text-lg font-semibold mb-4">Tambah Pengaduan Baru</h3>

        <button id="closeModalBtn" 
            class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-2xl">&times;</button>

        <form method="POST" action="{{ route('pengaduan.store') }}">
            @csrf

            {{-- Pelanggan --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold mb-1">Pelanggan</label>
       <select id="pelanggan_id"
        name="pelanggan_id"
        required
        class="w-full border rounded-lg px-3 py-2 bg-white">
    <option value="">-- Pilih Pelanggan --</option>
    @foreach($pelanggans as $pelanggan)
        <option value="{{ $pelanggan->id }}">{{ $pelanggan->name }}</option>
    @endforeach
</select>

            </div>

            {{-- Jenis --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold mb-1">Jenis Pengaduan</label>
             <select id="jenis_pengaduan"
        name="jenis_pengaduan"
        required
        class="w-full border rounded-lg px-3 py-2 bg-white">
    <option value="">-- Pilih Jenis --</option>
    <option value="Gangguan Koneksi">Gangguan Koneksi</option>
    <option value="Modem Rusak">Modem Rusak</option>
    <option value="Tagihan / Kuota">Tagihan / Kuota</option>
    <option value="Perlu Penggantian Alat">Perlu Penggantian Alat</option>
    <option value="Lainnya">Lainnya</option>
</select>

            </div>

            {{-- Deskripsi --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold mb-1">Deskripsi</label>
                <textarea name="deskripsi" rows="3" required class="w-full border rounded-lg px-3 py-2 bg-white"></textarea>
            </div>

            {{-- Teknisi --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold mb-1">Teknisi Penanggung Jawab</label>
            <select id="id_teknisi"
        name="id_teknisi"
        class="w-full border rounded-lg px-3 py-2 bg-white">
    <option value="">-- Pilih Teknisi --</option>
    @foreach($teknisis as $tek)
        <option value="{{ $tek->id }}">{{ $tek->name }}</option>
    @endforeach
</select>

            </div>

            <div class="flex justify-end gap-2 mt-4">
                <button type="button" id="closeModalBtn2"
                    class="px-4 py-2 rounded border hover:bg-gray-100">Batal</button>

                <button type="submit" 
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Tambah Pengaduan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ================= MODAL DETAIL ================= --}}
<div id="detailModal"
     class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">

    <div class="bg-white rounded-2xl w-full max-w-lg p-6 relative max-h-[90vh] overflow-y-auto">

        <h3 class="text-lg font-bold mb-4 text-[#2A4156]">Detail Pengaduan</h3>

        <button onclick="closeDetailModal()"
                class="absolute top-3 right-4 text-2xl text-gray-500 hover:text-gray-700">
            &times;
        </button>

        <div class="space-y-3 text-sm">
            <p><span class="text-gray-500">Pelanggan:</span>
               <span class="font-semibold" id="dPelanggan"></span></p>

            <p><span class="text-gray-500">Jenis:</span>
               <span class="font-semibold" id="dJenis"></span></p>

            <p><span class="text-gray-500">Status:</span>
               <span class="font-semibold" id="dStatus"></span></p>

            {{-- TAMBAHAN --}}
           <p><span class="text-gray-500">Dibuat:</span>
   <span class="font-semibold" id="dTanggalDibuat"></span></p>

<p><span class="text-gray-500">Diselesaikan:</span>
   <span class="font-semibold" id="dTanggalSelesai"></span></p>


            <div>
                <p class="text-gray-500 mb-1">Deskripsi:</p>
                <p class="font-medium text-gray-800" id="dDeskripsi"></p>
            </div>
        </div>

        {{-- FOTO --}}
        <div id="fotoWrapper" class="mt-5 hidden">
            <p class="font-semibold mb-2 text-gray-700">Bukti Foto</p>
            <div class="bg-gray-100 p-3 rounded-xl">
                <img id="dFoto" class="w-full max-h-72 object-contain rounded-lg">
            </div>
        </div>

        <div class="mt-6 text-right">
            <button onclick="closeDetailModal()"
                    class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded-lg font-semibold">
                Tutup
            </button>
        </div>
    </div>
</div>

{{-- ================= SCRIPT ================= --}}
<script>
const openBtn = document.getElementById('openModalBtn');
const modal = document.getElementById('pengaduanModal');
const closeBtn1 = document.getElementById('closeModalBtn');
const closeBtn2 = document.getElementById('closeModalBtn2');

openBtn.onclick = () => modal.classList.remove('hidden');
closeBtn1.onclick = () => modal.classList.add('hidden');
closeBtn2.onclick = () => modal.classList.add('hidden');
window.onclick = (e) => { if (e.target === modal) modal.classList.add('hidden'); }

function openDetailModal(pelanggan, jenis, deskripsi, status, foto, tanggalDibuat, tanggalSelesai) {
    document.getElementById('dPelanggan').innerText = pelanggan;
    document.getElementById('dJenis').innerText = jenis;
    document.getElementById('dDeskripsi').innerText = deskripsi;
    document.getElementById('dStatus').innerText = status;
    document.getElementById('dTanggalDibuat').innerText = tanggalDibuat;
    document.getElementById('dTanggalSelesai').innerText = tanggalSelesai || '-';

    const fotoWrapper = document.getElementById('fotoWrapper');
    const dFoto = document.getElementById('dFoto');

    if (foto) {
        dFoto.src = foto;
        fotoWrapper.classList.remove('hidden');
    } else {
        fotoWrapper.classList.add('hidden');
    }

    document.getElementById('detailModal').classList.remove('hidden');
}

function closeDetailModal() {
    document.getElementById('detailModal').classList.add('hidden');
}

let pelangganChoices, jenisChoices, teknisiChoices;

function initChoices() {
    if (!pelangganChoices) {
        pelangganChoices = new Choices('#pelanggan_id', {
            searchEnabled: false,
            shouldSort: false
        });
    }

    if (!jenisChoices) {
        jenisChoices = new Choices('#jenis_pengaduan', {
            searchEnabled: false,
            shouldSort: false
        });
    }

    if (!teknisiChoices) {
        teknisiChoices = new Choices('#id_teknisi', {
            searchEnabled: false,
            shouldSort: false
        });
    }
}

// buka modal
openBtn.onclick = () => {
    modal.classList.remove('hidden');
    setTimeout(initChoices, 100); // penting untuk modal
};

// close modal tetap sama
closeBtn1.onclick = () => modal.classList.add('hidden');
closeBtn2.onclick = () => modal.classList.add('hidden');

window.onclick = (e) => {
    if (e.target === modal) modal.classList.add('hidden');
}

</script>

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
{{-- Choices.js --}}
<link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

</div>
@endsection
