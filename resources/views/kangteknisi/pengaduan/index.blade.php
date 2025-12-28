@extends('layouts.app')

@section('title', 'Pengaduan')

@section('content')
<div class="min-h-screen bg-[#F5EEEB] pb-28">

    {{-- HEADER --}}
    <div class="fixed top-0 left-0 w-full bg-[#1E2C4A] text-white py-4 px-6 shadow-md z-50 flex justify-between items-center">
        <div class="flex items-center space-x-3">
            <a href="{{ url('/kangteknisi/dashboard') }}" class="text-white text-2xl">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <h1 class="text-lg font-semibold">Pengaduan</h1>
        </div>
    </div>

    {{-- TAB MENU --}}
    <div class="pt-24 px-6">
        <div class="flex justify-around border-b border-gray-300 mb-4">
            <button id="tabPending" class="w-1/2 py-2 text-center font-semibold text-sky-600 border-b-2 border-sky-600 transition">
                PENDING
            </button>
            <button id="tabSelesai" class="w-1/2 py-2 text-center font-semibold text-gray-500 hover:text-sky-600 transition">
                SELESAI
            </button>
        </div>

        {{-- PENDING --}}
        <div id="pendingContent">
            @if($pending->isEmpty())
                <p class="text-gray-500 text-center mt-10">Tidak ada pengaduan.</p>
            @else
                <div class="space-y-4">
                    @foreach($pending as $p)
                        <div class="bg-white rounded-xl shadow-md p-4 border border-gray-200" id="card-{{ $p->id }}">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h2 class="text-base font-semibold text-gray-800">{{ $p->pelanggan->name ?? 'Pelanggan' }}</h2>
                                    <p class="text-xs text-gray-500">ID: {{ $p->id }}</p>
                                </div>
                                <span class="px-3 py-1 text-xs bg-yellow-100 text-yellow-700 rounded-full font-semibold">
                                    Pending
                                </span>
                            </div>

                            <div class="mb-4">
                                <p class="text-xs text-gray-500">Jenis Pengaduan</p>
                                <p class="text-sm font-medium text-gray-800">{{ $p->jenis_pengaduan }}</p>
                            </div>

                            <div class="flex justify-between items-center mt-3">
                                <a href="{{ route('kangteknisi.pengaduan.show', $p->id) }}" class="text-sky-600 text-sm font-semibold hover:underline">
                                    Detail
                                </a>

                                <button type="button" onclick="openCamera({{ $p->id }})" class="px-4 py-2 bg-green-600 text-white rounded">
                                    Selesaikan
                                </button>
                            </div>

                            {{-- Preview foto sebelum kirim --}}
                            <div class="mt-2">
                                <img id="preview-{{ $p->id }}" class="w-full max-h-96 object-cover rounded-md hidden">
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- SELESAI --}}
        <div id="selesaiContent" class="hidden">
            @if($selesai->isEmpty())
                <p class="text-gray-500 text-center mt-10">Tidak ada pengaduan.</p>
            @else
                <div class="space-y-4">
                    @foreach($selesai as $p)
<div class="bg-white rounded-xl shadow-md p-4 border border-gray-200 
            max-w-xl mx-auto md:max-w-2xl lg:max-w-3xl">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h2 class="text-base font-semibold text-gray-800">{{ $p->pelanggan->name ?? 'Pelanggan' }}</h2>
                                    <p class="text-xs text-gray-500">ID: {{ $p->id }}</p>
                                </div>
                                <span class="px-3 py-1 text-xs bg-green-100 text-green-700 rounded-full font-semibold">
                                    Selesai
                                </span>
                            </div>

                            <div class="mb-4">
                                <p class="text-xs text-gray-500">Jenis Pengaduan</p>
                                <p class="text-sm font-medium text-gray-800">{{ $p->jenis_pengaduan }}</p>
                            </div>

                            <div class="flex justify-start mt-3 mb-2">
                                <a href="{{ route('kangteknisi.pengaduan.show', $p->id) }}" class="text-sky-600 text-sm font-semibold hover:underline">
                                    Detail
                                </a>
                            </div>

                            @if($p->bukti_foto)
<img src="{{ asset('storage/'.$p->bukti_foto) }}" 
     class="
        w-full 
        max-h-72 sm:max-h-80 md:max-h-64 lg:max-h-60
        object-contain 
        rounded-md 
        mt-2 
        bg-gray-100
     ">
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

{{-- MODAL CAMERA --}}
<div id="cameraModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
    <div class="bg-white rounded-lg w-11/12 max-w-md p-4">
        <h2 class="text-lg font-semibold mb-2">Ambil Foto Bukti</h2>
        <video id="video" autoplay class="w-full rounded-md mb-2"></video>
        
        <canvas id="canvas" class="hidden"></canvas>
        <div class="flex justify-between mt-2">
            <button id="captureBtn" class="px-4 py-2 bg-blue-600 text-white rounded">Ambil Foto</button>
            <button id="retakeBtn" class="px-4 py-2 bg-yellow-500 text-white rounded hidden">Ulangi</button>
            <button id="submitFotoBtn" class="px-4 py-2 bg-green-600 text-white rounded hidden">Kirim</button>
            <button onclick="closeCamera()" class="px-4 py-2 bg-red-500 text-white rounded">Batal</button>
        </div>

        {{-- FORM UPLOAD --}}
<form id="formSelesai">
    @csrf
    <input type="file" name="bukti_foto" id="fotoInput" hidden>
</form>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // TAB
    const tabPending = document.getElementById('tabPending');
    const tabSelesai = document.getElementById('tabSelesai');
    const pendingContent = document.getElementById('pendingContent');
    const selesaiContent = document.getElementById('selesaiContent');

    tabPending.addEventListener('click', () => {
        tabPending.classList.add('text-sky-600','border-b-2','border-sky-600');
        tabSelesai.classList.remove('text-sky-600','border-b-2','border-sky-600');
        tabSelesai.classList.add('text-gray-500');
        pendingContent.classList.remove('hidden');
        selesaiContent.classList.add('hidden');
    });

    tabSelesai.addEventListener('click', () => {
        tabSelesai.classList.add('text-sky-600','border-b-2','border-sky-600');
        tabPending.classList.remove('text-sky-600','border-b-2','border-sky-600');
        tabPending.classList.add('text-gray-500');
        selesaiContent.classList.remove('hidden');
        pendingContent.classList.add('hidden');
    });

    // CAMERA
    let video = document.getElementById('video');
    let canvas = document.getElementById('canvas');
    let captureBtn = document.getElementById('captureBtn');
    let retakeBtn = document.getElementById('retakeBtn');
    let submitFotoBtn = document.getElementById('submitFotoBtn');
    let cameraModal = document.getElementById('cameraModal');
    let fotoInput = document.getElementById('fotoInput');
    let currentPengaduanId = null;
    let stream = null;

    window.openCamera = function(pengaduanId) {
        currentPengaduanId = pengaduanId;
        cameraModal.classList.remove('hidden');
        navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
            .then(s => { stream = s; video.srcObject = s; })
            .catch(err => { alert('Tidak dapat mengakses kamera'); });
    }

    captureBtn.addEventListener('click', () => {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);

        canvas.toBlob(blob => {
            const file = new File([blob], 'foto.jpg', { type: 'image/jpeg' });
            const dt = new DataTransfer();
            dt.items.add(file);
            fotoInput.files = dt.files;

            const previewImg = document.getElementById(`preview-${currentPengaduanId}`);
            previewImg.src = URL.createObjectURL(file);
            previewImg.classList.remove('hidden');

            video.classList.add('hidden');
            canvas.classList.remove('hidden');
            captureBtn.classList.add('hidden');
            retakeBtn.classList.remove('hidden');
            submitFotoBtn.classList.remove('hidden');
        }, 'image/jpeg', 0.9);
    });

    retakeBtn.addEventListener('click', () => {
        canvas.classList.add('hidden');
        video.classList.remove('hidden');
        captureBtn.classList.remove('hidden');
        retakeBtn.classList.add('hidden');
        submitFotoBtn.classList.add('hidden');
        fotoInput.value = '';

        const previewImg = document.getElementById(`preview-${currentPengaduanId}`);
        previewImg.src = '';
        previewImg.classList.add('hidden');
    });

   submitFotoBtn.addEventListener('click', async () => {
    if (!fotoInput.files[0]) {
        alert('Ambil foto dulu!');
        return;
    }

    const url = `/kangteknisi/pengaduan/${currentPengaduanId}/selesai`;
    const fd = new FormData();

    fd.append('_token', '{{ csrf_token() }}');
    fd.append('bukti_foto', fotoInput.files[0]);

    try {
        const res = await fetch(url, {
            method: 'POST',
            body: fd
        });

        if (res.ok) {
            alert('Foto berhasil dikirim!');
            location.reload();
        } else {
            alert('Gagal kirim foto');
        }
    } catch (err) {
        console.error(err);
        alert('Terjadi error saat kirim foto');
    }
});


    window.closeCamera = function() {
        cameraModal.classList.add('hidden');
        if(stream) stream.getTracks().forEach(t=>t.stop());
        video.classList.remove('hidden');
        canvas.classList.add('hidden');
        captureBtn.classList.remove('hidden');
        retakeBtn.classList.add('hidden');
        submitFotoBtn.classList.add('hidden');
        fotoInput.value = '';

        const previewImg = document.getElementById(`preview-${currentPengaduanId}`);
        previewImg.src = '';
        previewImg.classList.add('hidden');
    }
});
</script>
@endsection
