<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History Lunas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* Animasi lembut */
        .fade-in {
            animation: fadeIn 0.4s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>

<body class="bg-[#F5EEEB] min-h-screen">

    {{-- HEADER FIXED --}}
    <div class="fixed top-0 left-0 w-full bg-[#2A4156] text-white py-4 px-6 shadow-lg z-50">
        <div class="flex justify-between items-center">

            <a href="{{ url()->previous() }}" 
               class="flex items-center gap-2 hover:text-gray-200 transition">
                <i class="fas fa-arrow-left text-lg"></i>
                <span class="text-sm font-medium">Kembali</span>
            </a>

            <h1 class="text-lg font-semibold">History Lunas</h1>

            <span class="w-6"></span>
        </div>
    </div>


    {{-- KONTEN --}}
    <div class="pt-28 px-4 pb-10">

        {{-- TOMBOL HAPUS SEMUA --}}
        @if($tagihanLunas->count())
        <div class="mb-5 fade-in">
            <form action="{{ route('history.lunas.deleteAll') }}" method="POST"
                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus semua tagihan lunas?')">
                @csrf
                @method('DELETE')

                <button type="submit"
                    class="w-full bg-red-600 text-white py-3 rounded-2xl shadow-md 
                           hover:bg-red-700 active:scale-95 transition text-sm font-medium">
                    <i class="fas fa-trash mr-2"></i> Hapus Semua Tagihan Lunas
                </button>
            </form>
        </div>
        @endif


        {{-- LIST TAGIHAN LUNAS --}}
        <div class="space-y-4">

            @forelse($tagihanLunas as $tagihan)
            <div class="bg-white p-5 rounded-2xl shadow fade-in hover:shadow-lg transition">

                <div class="flex justify-between items-center">

                    <div>
                        <p class="font-semibold text-[#2A4156] text-base">
                            {{ $tagihan->pelanggan->name }}
                        </p>

                        <p class="text-sm text-gray-600">
                            Bulan: 
                            {{ \Carbon\Carbon::parse($tagihan->bulan)->translatedFormat('F Y') }}
                        </p>
                    </div>

                    <form action="{{ route('history.lunas.delete', $tagihan->id) }}" method="POST"
                          onsubmit="return confirm('Yakin ingin menghapus tagihan ini?')">
                        @csrf
                        @method('DELETE')

                        <button class="text-red-500 hover:text-red-700 transition text-lg">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>

                </div>

            </div>
            @empty

            <p class="text-center text-gray-500 mt-20 text-sm fade-in">
                Belum ada tagihan lunas.
            </p>

            @endforelse

        </div>

    </div>

</body>
</html>
