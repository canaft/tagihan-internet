<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History Lunas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#F5EEEB] min-h-screen">

    {{-- HEADER FIXED --}}
    <div class="fixed top-0 left-0 w-full bg-[#2A4156] text-white py-4 px-6 shadow-md z-50">
        <div class="flex justify-between items-center">
            <a href="{{ url()->previous() }}" 
               class="flex items-center space-x-2 hover:text-gray-200 transition">
                <i class="fas fa-arrow-left text-lg"></i>
                <span class="text-sm font-medium">Kembali</span>
            </a>
            <h1 class="text-lg font-semibold">History Lunas</h1>
            <span></span> {{-- placeholder supaya header tetap rata --}}
        </div>
    </div>

    {{-- KONTEN UTAMA --}}
    <div class="pt-28 px-4">

        {{-- Tombol Hapus Semua --}}
        @if($tagihanLunas->count())
        <div class="mb-4">
            <form action="{{ route('history.lunas.deleteAll') }}" method="POST" 
                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus semua tagihan lunas?')">
                @csrf
                @method('DELETE')
                <button type="submit" 
                    class="w-full bg-red-500 text-white py-2 rounded-xl shadow hover:bg-red-600 transition">
                    Hapus Semua Tagihan Lunas
                </button>
            </form>
        </div>
        @endif

        {{-- Daftar Tagihan Lunas --}}
        <div class="space-y-3">
            @forelse($tagihanLunas as $tagihan)
            <div class="flex justify-between items-center bg-white p-4 rounded-2xl shadow-md hover:bg-gray-50 transition">
                <div>
                    <p class="font-medium text-[#2A4156]">Pelanggan: {{ $tagihan->pelanggan->name }}</p>
                    <p class="text-sm text-gray-500">
                        Bulan: {{ \Carbon\Carbon::parse($tagihan->bulan)->format('F Y') }}
                    </p>
                </div>
                <form action="{{ route('history.lunas.delete', $tagihan->id) }}" method="POST" 
                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pelanggan ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="text-red-500 hover:text-red-700 transition">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
            @empty
            <p class="text-gray-500 text-center mt-10">Belum ada tagihan lunas.</p>
            @endforelse
        </div>

    </div>

</body>
</html>
