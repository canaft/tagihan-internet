<!-- @extends('layouts.app')
@section('title', 'Notifikasi Admin')

@section('content')
<div class="pt-24 pb-20 px-3 sm:px-10 max-w-4xl mx-auto">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            Notifikasi Admin
        </h2>

        {{-- BUTTON KEMBALI --}}
        <a href="{{ route('admin.dashboard') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm">
            <i class="fa fa-arrow-left text-xs"></i>
            Kembali
        </a>
    </div>

    {{-- EMPTY STATE --}}
    @if($notifications->isEmpty())
        <div class="p-8 bg-white rounded-xl shadow text-center text-gray-500">
            <i class="fa fa-bell-slash text-4xl mb-3 text-gray-300"></i>
            <p class="text-sm">Tidak ada notifikasi.</p>
        </div>
    @else

        {{-- LIST NOTIFIKASI --}}
        <div class="space-y-4">
            @foreach($notifications as $n)
                <div class="bg-white p-4 rounded-xl shadow-sm hover:shadow-md transition
                            flex gap-4 items-start
                            {{ !$n->is_read ? 'border-l-4 border-blue-600' : '' }}">

                    {{-- ICON --}}
                    <div class="flex-shrink-0 mt-1">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center
                            {{ !$n->is_read ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-400' }}">
                            <i class="fa fa-bell"></i>
                        </div>
                    </div>

                    {{-- CONTENT --}}
                    <div class="flex-1">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-semibold text-gray-800 text-sm">
                                {{ ucfirst(str_replace('_', ' ', $n->type)) }}
                            </span>

                            @if(!$n->is_read)
                                <span class="text-xs px-2 py-0.5 bg-blue-100 text-blue-600 rounded-full">
                                    Baru
                                </span>
                            @endif
                        </div>

                        <p class="text-gray-600 text-sm mt-1">
                            {{ $n->message }}
                        </p>

                        <span class="text-xs text-gray-400 mt-2 block">
                            {{ $n->created_at->format('d M Y • H:i') }}
                        </span>
                    </div>

                    {{-- ACTION --}}
                    @if(!$n->is_read)
                        <form action="{{ route('admin.notifications.read', $n->id) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-xs
                                           hover:bg-blue-700 transition whitespace-nowrap">
                                Tandai Dibaca
                            </button>
                        </form>
                    @endif

                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection -->
