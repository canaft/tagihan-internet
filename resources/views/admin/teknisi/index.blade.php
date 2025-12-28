@extends('layouts.app')
@section('title', 'Data Teknisi & Sales')

@section('content')
<div class="px-4 pt-20 pb-24 max-w-4xl mx-auto">

    {{-- HEADER PAGE --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Data Teknisi & Sales</h2>
            <p class="text-xs text-gray-500 -mt-0.5">Kelola daftar teknisi dan sales dengan mudah</p>
        </div>

        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center gap-1 px-3 py-1.5 bg-gray-800 text-white text-sm font-medium rounded-xl shadow hover:bg-gray-900 active:scale-[0.97] transition-all">
            <span class="text-lg">←</span>
            <span>Kembali</span>
        </a>
    </div>

    {{-- BUTTON TAMBAH --}}
    <div class="flex justify-end mb-4">
        <a href="{{ route('teknisi.create') }}"
           class="bg-green-600 text-white px-4 py-2 rounded-xl text-sm font-semibold shadow hover:bg-green-700 transition">
            + Tambah Akun
        </a>
    </div>

    {{-- FLASH MESSAGE --}}
    @if(session('success'))
        <div class="bg-blue-100 text-blue-800 p-3 rounded-xl shadow mb-4 text-sm">
            {{ session('success') }}
        </div>
    @endif


    {{-- TABLE WRAPPER --}}
    <div class="bg-white p-4 rounded-2xl shadow-md border overflow-x-auto">

        <table class="w-full min-w-[760px]">
            <thead>
                <tr class="bg-gray-100 text-gray-700 border-b text-sm">
                    <th class="p-3 text-left">No</th>
                    <th class="p-3 text-left">Nama</th>
                    <th class="p-3 text-left">Username</th>
                    <th class="p-3 text-left">Nomor HP</th>
                    <th class="p-3 text-left">Wilayah Kerja</th>
                    <th class="p-3 text-left">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($teknisis as $index => $t)
                <tr class="odd:bg-gray-50 even:bg-white hover:bg-gray-100 transition text-sm">
                    <td class="p-3">{{ $index + 1 }}</td>
                    <td class="p-3 font-semibold text-gray-800">{{ $t->name }}</td>
                    <td class="p-3">{{ $t->username }}</td>
                    <td class="p-3">{{ $t->phone }}</td>
                    <td class="p-3">{{ $t->wilayah }}</td>

                    <td class="p-3">
                        <div class="flex gap-2">

                            <a href="{{ route('teknisi.edit', $t->id) }}"
                                class="bg-blue-600 text-white px-3 py-1 rounded-lg shadow hover:bg-blue-700 text-xs transition">
                                Edit
                            </a>

                            <form method="POST" action="{{ route('teknisi.destroy', $t->id) }}"
                                  onsubmit="return confirm('Yakin ingin menghapus akun ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="bg-red-600 text-white px-3 py-1 rounded-lg shadow hover:bg-red-700 text-xs transition">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>

                @empty
                <tr>
                    <td colspan="6" class="p-4 text-center text-gray-500 text-sm">
                        Belum ada data teknisi atau sales.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
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
