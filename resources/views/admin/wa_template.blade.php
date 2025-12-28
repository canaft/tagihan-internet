@extends('layouts.app')
@section('title', 'Template WA Tagihan')

@php $hideNavbar = true; @endphp

@section('content')
<div class="min-h-screen bg-[#F5EEEB] pb-28">

    {{-- HEADER --}}
    <div class="fixed top-0 left-0 w-full bg-[#2A4156] text-white py-4 px-6 shadow z-50">
        <div class="flex justify-between items-center">
            <a href="{{ route('setting') }}" class="flex items-center space-x-2">
                <i class="fas fa-arrow-left"></i>
                <span class="text-sm">Kembali</span>
            </a>
            <h1 class="text-lg font-semibold">Template WA Tagihan</h1>
            <span></span>
        </div>
    </div>

    <div class="pt-28 px-4">

        {{-- INFO --}}
        <div class="bg-white p-4 rounded-2xl shadow mb-4">
            <h2 class="text-lg font-semibold">Kustom Template WA</h2>
            <p class="text-sm text-gray-500">
                Gunakan variabel berikut:<br>
                <span class="font-medium">
                    {nama} {nomor} {area} {bulan_tagihan} {jenis_paket} {biaya_paket} {diskon} {total}
                </span>
            </p>
        </div>

        {{-- ALERT --}}
        @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded-xl mb-4">
            {{ session('success') }}
        </div>
        @endif

        {{-- FORM TAMBAH TEMPLATE --}}
        <div class="bg-white p-5 rounded-2xl shadow mb-6">
            <form action="{{ route('setting.wa_template.save') }}" method="POST">
                @csrf
                <label class="block mb-1 font-semibold">Kategori Template</label>
                <select name="category" class="w-full border rounded-xl p-2 mb-3">
                    <option value="belum_bayar">Belum Bayar</option>
                    <option value="lunas">Lunas</option>
                </select>

                <label class="block mb-2 font-semibold">Isi Template</label>
                <textarea name="template" rows="7"
                    class="w-full border rounded-xl p-3 text-sm"
                    placeholder="Masukkan template baru..."></textarea>

                <button class="mt-3 w-full bg-[#2A4156] text-white py-3 rounded-xl">
                    Simpan Template
                </button>
            </form>
        </div>

        {{-- TAB --}}
        <div class="flex mb-4 space-x-2">
            <a href="?tab=belum_bayar" class="flex-1 text-center py-2 rounded-xl font-semibold
                {{ request('tab','belum_bayar')=='belum_bayar' ? 'bg-[#2A4156] text-white' : 'bg-white text-[#2A4156]' }}">
                Belum Bayar
            </a>
            <a href="?tab=lunas" class="flex-1 text-center py-2 rounded-xl font-semibold
                {{ request('tab')=='lunas' ? 'bg-[#2A4156] text-white' : 'bg-white text-[#2A4156]' }}">
                Lunas
            </a>
        </div>

        {{-- TEMPLATE LIST --}}
        @php
            $tab = request('tab','belum_bayar');
            $tabTemplates = $templates->where('category', $tab);
        @endphp

        <div class="bg-white p-5 rounded-2xl shadow">
            @forelse($tabTemplates as $t)
            <div class="border rounded-xl p-3 mb-3">

                <form action="{{ route('setting.wa_template.save') }}" method="POST">
                    @csrf
                    <input type="hidden" name="category" value="{{ $t->category }}">
                    <input type="hidden" name="key_name" value="{{ $t->key_name }}">

                    <textarea name="template" rows="5" class="w-full border rounded-xl p-2 mb-2">{{ $t->value }}</textarea>

                    <button class="w-full bg-blue-600 text-white py-2 rounded-xl">
                        Update Template
                    </button>
                </form>

<form action="{{ route('setting.wa_template.gunakan', $t->key_name) }}" method="POST">
    @csrf
    <button type="submit" class="mt-2 w-full bg-green-500 text-white py-2 rounded-xl">
        Gunakan
    </button>
</form>


                @if($t->is_default)
                    <p class="text-center text-sm text-gray-500 mt-1">Sedang digunakan</p>
                @endif
            </div>
            @empty
                <p class="text-center text-gray-500">Belum ada template.</p>
            @endforelse
        </div>

    </div>
</div>
@endsection
