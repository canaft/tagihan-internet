@extends('layouts.app')
@section('title', 'Pelanggan Berhenti')

@section('content')
<div class="pt-24 pb-20 px-3 sm:px-10 max-w-6xl mx-auto">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-3xl font-bold text-gray-800 flex items-center gap-2">
            Pelanggan Berhenti
        </h2>

        <a href="{{ route('pelanggan.index') }}"
           class="px-4 py-2 bg-white border shadow-sm rounded-xl hover:bg-gray-50 transition text-gray-700 flex items-center gap-2">
            <i class="fa fa-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- Card Wrapper --}}
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">

        {{-- TABLE WRAPPER AGAR BISA SCROLL DI HP --}}
        <div class="overflow-x-auto">

            {{-- Table --}}
            <table class="w-full min-w-[600px]">
                <thead class="bg-gray-50 border-b">
                    <tr class="text-gray-600 text-sm uppercase tracking-wider">
                        <th class="p-4 text-left">Nama</th>
                        <th class="p-4 text-left">Telepon</th>
                        <th class="p-4 text-left">Area</th>
                        <th class="p-4 text-left">Paket</th>
                        <th class="p-4 text-left">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @forelse ($pelanggan as $p)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 font-semibold text-gray-800">{{ $p->name }}</td>
                            <td class="p-4 text-gray-700">{{ $p->phone }}</td>
                            <td class="p-4 text-gray-700">{{ $p->area->nama_area ?? '-' }}</td>
                            <td class="p-4 text-gray-700">{{ $p->package->nama_paket ?? '-' }}</td>

                            <td class="p-4">
                                <form action="{{ route('admin.pelanggan_aktifkan', $p->id) }}" method="POST"
                                      onsubmit="return confirm('Yakin ingin mengaktifkan ulang pelanggan ini?')">
                                    @csrf
                                    @method('PUT')

                                    <button
                                        class="text-green-600 font-semibold hover:text-green-700 hover:underline transition">
                                        <i class="fa fa-check-circle"></i>
                                        Aktifkan Ulang
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-6 text-gray-500">
                                <i class="fa fa-info-circle mr-1"></i>
                                Tidak ada pelanggan yang berhenti.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
        {{-- END WRAPPER --}}
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