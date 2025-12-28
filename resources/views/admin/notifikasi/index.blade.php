@extends('layouts.app')
@section('title', 'Notifikasi Admin')

@section('content')

<div class="pt-24 pb-20 max-w-2xl mx-auto px-4">

    {{-- TITLE --}}
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold flex items-center gap-2">
            🔔 Notifikasi
        </h2>

        {{-- TANDAI SEMUA DIBACA --}}
        <a href="{{ route('admin.notif.readall') }}"
           class="text-sm font-semibold text-blue-600 hover:text-blue-800 hover:underline">
            Tandai semua dibaca
        </a>
    </div>

    {{-- LIST NOTIF --}}
    <div class="space-y-3">
        @forelse($notifications as $notif)

            <div class="
                rounded-xl border shadow-sm p-4 bg-white transition-all duration-200
                hover:shadow-md
                {{ !$notif->is_read ? 'border-blue-300 bg-blue-50/50' : 'border-gray-200' }}
            ">

                <div class="flex gap-3">

                    {{-- ICON BULLET (READ/UNREAD) --}}
                    <div class="flex-shrink-0">
                        <div class="w-3 h-3 rounded-full mt-2
                            {{ !$notif->is_read ? 'bg-blue-500' : 'bg-gray-400' }}">
                        </div>
                    </div>

                    {{-- TEKS NOTIF --}}
                    <div class="flex-1">
                        <p class="text-gray-800 font-semibold">
                            {{ $notif->message }}
                        </p>

                        <p class="text-sm text-gray-500 mt-1">
                            {{ $notif->created_at->diffForHumans() }}
                        </p>

                        {{-- KHUSUS NOTIF PENGADUAN SELESAI --}}
                        @if($notif->type === 'pengaduan_selesai' && isset($notif->pengaduan_id))
                            <a href="{{ route('admin.pengaduan.show', $notif->pengaduan_id) }}"
                               class="text-sm text-blue-600 hover:underline mt-1 inline-block">
                                Lihat detail pengaduan
                            </a>
                        @endif
                    </div>

                    {{-- TOMBOL ACTION UNTUK UNREAD --}}
                    @if(!$notif->is_read)
                        <div class="flex-shrink-0 self-center">
                            @if($notif->type === 'request_password_change')
                                <form action="{{ route('admin.notifications.approvePassword', $notif->id) }}" method="POST">
                                    @csrf
                                    <button
                                        class="px-3 py-1 text-xs bg-green-600 text-white rounded-lg 
                                               hover:bg-green-700 transition h-fit">
                                        Approve
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('admin.notif.read', $notif->id) }}"
                                   class="px-3 py-1 text-xs bg-blue-600 text-white rounded-lg 
                                          hover:bg-blue-700 transition h-fit">
                                    Tandai dibaca
                                </a>
                            @endif
                        </div>
                    @endif

                </div>
            </div>

        @empty
            <p class="text-center text-gray-500 mt-10">
                Tidak ada notifikasi.
            </p>
        @endforelse
    </div>

</div>

@endsection
