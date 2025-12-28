<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ODC Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#F5EEEB] min-h-screen">

    {{-- HEADER FIXED --}}
    <div class="fixed top-0 left-0 w-full bg-[#2A4156] text-white py-4 px-6 shadow-md z-50">
        <div class="flex justify-between items-center">
            <a href="{{ route('setting') }}" 
               class="flex items-center space-x-2 hover:text-gray-200 transition">
                <i class="fas fa-arrow-left text-lg"></i>
                <span class="text-sm font-medium">Kembali</span>
            </a>
            <h1 class="text-lg font-semibold">ODC Management</h1>
            <span></span> {{-- placeholder supaya header rata --}}
        </div>
    </div>

    {{-- KONTEN UTAMA --}}
    <div class="pt-28 px-4">

        {{-- Button Tambah ODC --}}
        <div class="mb-4">
            <button onclick="openModal('create')" 
                    class="w-full bg-blue-600 text-white py-2 rounded-xl shadow hover:bg-blue-700 transition font-semibold">
                + Tambah ODC
            </button>
        </div>

        {{-- Daftar ODC --}}
        <div class="space-y-3">
            @forelse($odcs as $odc)
            <div class="flex justify-between items-center bg-white p-4 rounded-2xl shadow-md hover:shadow-lg transition">
                <div>
                    <p class="font-medium text-[#2A4156]">{{ $odc->nama }}</p>
                    <p class="text-gray-500 text-sm">Kode: {{ $odc->kode }}</p>
                    @if($odc->lat && $odc->lng)
                        <p class="text-gray-400 text-xs mt-1">Lat: {{ $odc->lat }}, Lng: {{ $odc->lng }}</p>
                    @endif
                    @if($odc->info)
                        <p class="text-gray-400 text-xs mt-1">Info: {{ $odc->info }}</p>
                    @endif
                </div>
                <div class="flex space-x-2">
                    <button onclick="openModal('edit', {{ $odc }})" 
                            class="text-blue-500 font-medium hover:underline">Edit</button>
                    <form action="{{ route('odc.destroy', $odc->id) }}" method="POST" 
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus ODC ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 font-medium hover:underline">Hapus</button>
                    </form>
                </div>
            </div>
            @empty
            <p class="text-gray-500 text-center mt-10">Belum ada ODC.</p>
            @endforelse
        </div>
    </div>

    {{-- MODAL --}}
    <div id="odcModal" class="fixed inset-0 bg-black bg-opacity-40 hidden z-50 flex justify-center items-center">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 relative animate-slide-up mx-2">
            <h2 id="modalTitle" class="text-2xl font-semibold mb-4 text-[#2A4156]">Tambah ODC</h2>

            <form id="odcForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="methodInput" value="POST">

                <div class="mb-3">
                    <label class="block mb-1 font-medium text-gray-700">Kode ODC</label>
                    <input type="text" name="kode" id="kode" class="w-full border rounded-lg px-3 py-2" required>
                </div>
                <div class="mb-3">
                    <label class="block mb-1 font-medium text-gray-700">Nama ODC</label>
                    <input type="text" name="nama" id="nama" class="w-full border rounded-lg px-3 py-2" required>
                </div>
                <div class="mb-3">
                    <label class="block mb-1 font-medium text-gray-700">Latitude</label>
                    <input type="text" name="lat" id="lat" class="w-full border rounded-lg px-3 py-2">
                </div>
                <div class="mb-3">
                    <label class="block mb-1 font-medium text-gray-700">Longitude</label>
                    <input type="text" name="lng" id="lng" class="w-full border rounded-lg px-3 py-2">
                </div>
                <div class="mb-3">
                    <label class="block mb-1 font-medium text-gray-700">Info</label>
                    <input type="text" name="info" id="info" class="w-full border rounded-lg px-3 py-2">
                </div>

                <div class="flex justify-end space-x-3 mt-4">
                    <button type="button" onclick="closeModal()" 
                            class="px-4 py-2 rounded-xl bg-gray-300 hover:bg-gray-400 transition">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700 transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>

<script>
function openModal(type, odc = null) {
    const modal = document.getElementById('odcModal');
    const form = document.getElementById('odcForm');
    const title = document.getElementById('modalTitle');
    const methodInput = document.getElementById('methodInput');

    if(type === 'create') {
        title.textContent = 'Tambah ODC';
        form.action = "{{ route('odc.store') }}";
        methodInput.value = 'POST';
        form.kode.value = '';
        form.nama.value = '';
        form.lat.value = '';
        form.lng.value = '';
        form.info.value = '';
    }

    if(type === 'edit') {
        title.textContent = 'Edit ODC';
        form.action = `/odc/${odc.id}`;
        methodInput.value = 'PUT';
        form.kode.value = odc.kode;
        form.nama.value = odc.nama;
        form.lat.value = odc.lat ?? '';
        form.lng.value = odc.lng ?? '';
        form.info.value = odc.info ?? '';
    }

    modal.classList.remove('hidden');
}

function closeModal() {
    document.getElementById('odcModal').classList.add('hidden');
}
</script>

<style>
/* Animasi modal masuk dari bawah */
@keyframes slide-up {
  0% { transform: translateY(50px); opacity: 0; }
  100% { transform: translateY(0); opacity: 1; }
}
.animate-slide-up {
  animation: slide-up 0.3s ease-out;
}
</style>

</body>
</html>
