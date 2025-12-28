@extends('layouts.app')
@section('title', 'Data Pelanggan')

@section('content')

<div class="pt-24 pb-20">

    {{-- Tombol Kembali --}}
<div class="mb-4">
    <a href="{{ route('admin.dashboard') }}" 
       class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 px-4 rounded-lg shadow transition">
        <i class="fa fa-arrow-left"></i> Kembali
    </a>
</div>
{{-- Notifikasi --}}
<div class="bg-blue-50 text-blue-800 text-sm p-3 rounded-lg mb-3 flex items-center gap-2">
    <i class="fa fa-info-circle"></i>
    <span>
        Ada {{ $pelangganNunggak }} pelanggan belum bayar bulan 
        {{ \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F Y') }}
    </span>
</div>

{{-- Pilih Bulan (Compact Dropdown) --}}
<div class="relative inline-block mb-4">
    <button type="button" id="toggleBulanDropdown" class="flex items-center gap-2 bg-white p-2 rounded-xl shadow text-sm">
        <span class="font-semibold">Bulan :</span>
        <span class="text-[var(--accent-color)] font-bold">
            {{ \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F Y') }}
        </span>
        <i class="fa fa-calendar text-gray-500 text-lg"></i>
    </button>

    <div id="bulanDropdown" class="absolute mt-1 bg-white shadow-lg rounded-xl p-2 hidden z-50 min-w-[180px]">
        <form action="{{ route('pelanggan.index') }}" method="GET" class="flex flex-col gap-1 text-sm">
            <select name="bulan" class="border rounded px-2 py-1 text-sm w-full">
                @for($m=1;$m<=12;$m++)
                    <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::createFromDate($tahun, $m, 1)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>

            <select name="tahun" class="border rounded px-2 py-1 text-sm w-full">
                @for($y = now()->year-5; $y <= now()->year+1; $y++)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                @endfor
            </select>

            <button type="submit" class="bg-[var(--primary-color)] text-white px-2 py-1 rounded shadow hover:opacity-90 transition text-sm mt-1">
                Filter
            </button>
        </form>
    </div>
</div>


 {{-- Ringkasan Utama --}}
<div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 gap-3 mb-6">

    {{-- Pelanggan --}}
<a href="{{ route('admin.pelanggan_semua') }}" 
   class="bg-white p-3 rounded-lg text-center shadow hover:bg-gray-50 transition block">
    <div class="text-orange-500 text-2xl mb-1"><i class="fa fa-users"></i></div>
    <div class="font-bold text-lg">{{ number_format($totalPelanggan) }}</div>
    <div class="text-xs text-gray-500">Pelanggan Semua</div>
    <div class="text-sm font-semibold text-gray-700">
        Rp{{ number_format($totalBiaya, 0, ',', '.') }}
    </div>
</a>



    {{-- Tagihan Belum Bayar --}}
    <a href="{{ route('admin.belum_bayar') }}" class="bg-white p-3 rounded-lg text-center shadow hover:bg-gray-50 transition block">
        <div class="text-gray-600 text-2xl mb-1"><i class="fa fa-wifi"></i></div>
        <div class="font-bold text-lg">{{ number_format($tagihanBelumBayar) }}</div>
        <div class="text-xs text-gray-500">Tagihan Belum Bayar</div>
        <div class="text-sm font-semibold text-gray-700">
            Rp {{ number_format($totalBelumBayar, 0, ',', '.') }}
        </div>
    </a>

    {{-- Lunas Bayar --}}
    <a href="{{ route('admin.pelanggan_lunas') }}" class="bg-white p-3 rounded-lg text-center shadow hover:bg-gray-50 transition block">
        <div class="text-green-500 text-2xl mb-1"><i class="fa fa-check-circle"></i></div>
<div class="font-bold text-lg">{{ $totalPelangganLunas }}</div>
        <div class="text-xs text-gray-500">Lunas Bayar</div>
        <div class="text-sm font-semibold text-gray-700">
    Rp {{ number_format($totalBayarLunas, 0, ',', '.') }}
        </div>
    </a>

    <!-- {{-- Transaksi Cash --}}
    <a href="{{ route('admin.transaksi_cash') }}" class="bg-white p-3 rounded-lg text-center shadow hover:bg-gray-50 transition block">
        <div class="text-green-500 text-2xl mb-1"><i class="fa fa-money-bill-wave"></i></div>
        <div class="font-bold text-lg">{{ number_format($transaksiCash) }}</div>
        <div class="text-xs text-gray-500">Transaksi Cash</div>
        <div class="text-sm font-semibold text-gray-700">
        Rp {{ number_format($totalCash ?? 0, 0, ',', '.') }}
        </div>
    </a> -->

{{-- Pelanggan Baru --}}
<a href="{{ route('admin.pelanggan_baru') }}" class="bg-white p-3 rounded-lg text-center shadow hover:bg-gray-50 transition block">
    <div class="text-green-500 text-2xl mb-1"><i class="fa fa-user-plus"></i></div>
    <div class="font-bold text-lg">{{ number_format($pelangganBaru) }}</div>
    <div class="text-xs text-gray-500">Pelanggan Baru Bulan Ini</div>
    <div class="text-sm font-semibold text-gray-700">
        Rp {{ number_format($totalTagihanBaru, 0, ',', '.') }}
    </div>
</a>



{{-- Berhenti --}}
<a href="{{ route('admin.pelanggan_berhenti') }}" 
   class="bg-white p-3 rounded-lg text-center shadow hover:bg-gray-50 transition block">
    
    <div class="text-gray-700 text-2xl mb-1"><i class="fa fa-user-slash"></i></div>
    <div class="font-bold text-lg">{{ $pelangganBerhenti }}</div>
    <div class="text-xs text-gray-500">Berhenti</div>
    <div class="text-sm font-semibold text-gray-700">
        Rp {{ number_format($totalBerhenti ?? 0, 0, ',', '.') }}
    </div>

</a>




</div>


<!-- 
    {{-- Status Pelanggan --}}
    <div class="flex flex-wrap justify-between gap-2 mb-6 mt-6">
        <div class="flex-1 min-w-[100px] max-w-[33%] bg-red-50 p-3 rounded-lg text-center shadow">
            <div class="text-red-500 text-2xl mb-1"><i class="fa fa-user-lock"></i></div>
            <div class="font-semibold text-md">Transaksi Isolir</div>
            <div class="text-gray-700 text-sm">{{ $pelangganIsolir ?? 0 }}</div>
        </div>
        <div class="flex-1 min-w-[100px] max-w-[33%] bg-orange-50 p-3 rounded-lg text-center shadow">
            <div class="text-orange-500 text-2xl mb-1"><i class="fa fa-plane-departure"></i></div>
            <div class="font-semibold text-md">Isolir/Cuti</div>
            <div class="text-gray-700 text-sm">{{ $pelangganCuti ?? 0 }}</div>
        </div>
        <div class="flex-1 min-w-[100px] max-w-[33%] bg-red-100 p-3 rounded-lg text-center shadow">
            <div class="text-red-700 text-2xl mb-1"><i class="fa fa-user-slash"></i></div>
            <div class="font-semibold text-md">Berhenti</div>
            <div class="text-gray-700 text-sm">{{ $pelangganBerhenti }}</div>
        </div>
    </div> -->

    {{-- Tombol Aksi --}}
    <div class="grid grid-cols-1 gap-3 mb-6">
        <a href="{{ route('pelanggan.create') }}" 
        class="bg-[var(--primary-color)] text-white py-3 rounded-lg font-semibold text-center shadow hover:opacity-90 transition">
            Tambah Pelanggan
        </a>
    </div>

    {{-- Keterlambatan --}}
    <div class="bg-white p-4 rounded-lg shadow text-center">
        <div class="font-semibold mb-2">Keterlambatan Bulan Ini</div>
        <div class="flex justify-center gap-6">
            <a href="#" class="hover:bg-yellow-50 p-2 rounded-lg transition">
                <div class="text-yellow-500 text-2xl mb-1"><i class="fa fa-calendar-times"></i></div>
                <div class="font-semibold">{{ $pembayaranTelat ?? 0 }}</div>
                <div class="text-xs text-gray-500">Pemb. Telat</div>
            </a>

            <a href="#" class="hover:bg-red-50 p-2 rounded-lg transition">
                <div class="text-red-500 text-2xl mb-1"><i class="fa fa-calendar-xmark"></i></div>
                <div class="font-semibold">{{ $pelangganNunggak }}</div>
                <div class="text-xs text-gray-500">Nunggak</div>
            </a>
        </div>
    </div>

</div>
<script>
    const toggleBtn = document.getElementById('toggleBulanDropdown');
    const dropdown = document.getElementById('bulanDropdown');

    toggleBtn.addEventListener('click', () => {
        dropdown.classList.toggle('hidden');
    });

    document.addEventListener('click', function(event) {
        if (!toggleBtn.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });
</script>

@endsection

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