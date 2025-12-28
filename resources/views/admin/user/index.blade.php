@extends('layouts.app')
@section('title', 'User Management')

@section('content')
<div class="min-h-screen bg-gray-100 pb-28">

    {{-- NAVBAR --}}
    <div class="fixed top-0 left-0 w-full bg-[#2A4156] text-white py-4 px-6 shadow-md z-50">
        <div class="flex justify-between items-center">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2 hover:text-gray-200 transition">
                <i class="fas fa-arrow-left text-lg"></i>
                <span class="text-sm font-medium">Kembali</span>
            </a>
            <h1 class="text-lg font-semibold">User Management</h1>
            <span></span>
        </div>
    </div>

    {{-- CONTENT --}}
    <div class="pt-28 px-4 max-w-4xl mx-auto">

        {{-- Tombol kembali tambahan --}}
        <a href="{{ route('admin.dashboard') }}"
           class="inline-block mb-4 bg-gray-400 text-white px-4 py-2 rounded-xl font-semibold hover:bg-gray-500 transition">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>

        {{-- Tombol Tambah User --}}
        <button onclick="openModal('create')" 
            class="w-full bg-blue-600 text-white py-2 rounded-xl shadow hover:bg-blue-700 transition font-semibold mb-4">
            + Tambah User
        </button>

        {{-- LIST USER --}}
        <div class="space-y-4">
            @forelse($users as $user)
                <div class="bg-white p-4 rounded-2xl shadow-md border">

                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-semibold text-[#2A4156] text-lg">{{ $user->name }}</p>
                            <p class="text-gray-500 text-sm">{{ $user->email }}</p>

                            <span class="mt-2 inline-block px-3 py-1 bg-gray-100 rounded-full text-xs text-gray-700 border">
                                Role: <strong>{{ ucfirst($user->role) }}</strong>
                            </span>
                        </div>

                        <div class="flex space-x-3">

                            {{-- EDIT --}}
                            <button onclick="openModal('edit', {{ $user }})" 
                                class="text-blue-600 font-semibold hover:underline">
                                Edit
                            </button>

                            {{-- DELETE --}}
                            <form action="{{ route('admin.user.destroy', $user->id) }}" method="POST"
                                onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                    class="text-red-600 font-semibold hover:underline">
                                    Hapus
                                </button>
                            </form>

                        </div>
                    </div>

                </div>
            @empty
                <p class="text-gray-500 text-center mt-10">Belum ada user.</p>
            @endforelse
        </div>

    </div>


    {{-- MODAL --}}
    <div id="userModal" 
        class="fixed inset-0 bg-black bg-opacity-40 hidden z-50 flex justify-center items-center">

        <div class="bg-white rounded-2xl shadow-lg w-full max-w-md p-6 relative animate-slide-in">

            <h2 id="modalTitle" 
                class="text-2xl font-semibold mb-4 text-[#2A4156]">
                Tambah User
            </h2>

            <form id="userForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="methodInput" value="POST">

                {{-- Nama --}}
                <div class="mb-3">
                    <label class="block mb-1 font-medium text-gray-700">Nama</label>
                    <input type="text" name="name" id="name"
                        class="w-full border rounded-lg px-3 py-2" required>
                </div>

                {{-- Email --}}
                <div class="mb-3">
                    <label class="block mb-1 font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email"
                        class="w-full border rounded-lg px-3 py-2" required>
                </div>

                {{-- Password --}}
                <div class="mb-3">
                    <label class="block mb-1 font-medium text-gray-700">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="password"
                            class="w-full border rounded-lg px-3 py-2 pr-10"
                            placeholder="Isi untuk mengubah password">

                        <span onclick="togglePassword()"
                            class="absolute right-3 top-3 cursor-pointer text-gray-600">
                            <i id="eyeIcon" class="fa fa-eye"></i>
                        </span>
                    </div>
                </div>

            {{-- Role --}}
<div class="mb-3">
    <label class="block mb-1 font-medium text-gray-700">Role User</label>
    <select name="role" id="role"
        class="w-full border rounded-lg px-3 py-2" required>
        <option value="">-- Pilih Role --</option>
        <option value="teknisi">Teknisi</option>
        <option value="sales">Sales</option>
    </select>
</div>


                {{-- BUTTON --}}
                <div class="flex justify-end space-x-3 mt-4">
                    <button type="button" onclick="closeModal()" 
                        class="px-4 py-2 rounded-xl bg-gray-300 hover:bg-gray-400 transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700 transition">
                        Simpan
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>

{{-- SCRIPT --}}
<script>
    
function openModal(type, user = null) {
    const modal = document.getElementById('userModal');
    const title = document.getElementById('modalTitle');
    const form = document.getElementById('userForm');
    const method = document.getElementById('methodInput');

    if (type === 'create') {
        title.textContent = 'Tambah User';
        form.action = "{{ route('admin.user.store') }}";
        method.value = 'POST';

        form.name.value = '';
        form.email.value = '';
        form.password.value = '';
        form.role.value = '';
    }

    if (type === 'edit') {
        title.textContent = 'Edit User';
        form.action = `/admin/user/${user.id}`;
        method.value = 'PUT';

        form.name.value = user.name;
        form.email.value = user.email;
        form.password.value = '';
        form.role.value = user.role;
    }

    modal.classList.remove('hidden');
}

function closeModal() {
    document.getElementById('userModal').classList.add('hidden');
}

function togglePassword() {
    const input = document.getElementById('password');
    const icon = document.getElementById('eyeIcon');

    if (input.type === "password") {
        input.type = "text";
        icon.classList.replace("fa-eye", "fa-eye-slash");
    } else {
        input.type = "password";
        icon.classList.replace("fa-eye-slash", "fa-eye");
    }
}

document.addEventListener('DOMContentLoaded', function () {

    // Dropdown Role (samain dengan halaman tambah pelanggan)
    const roleSelect = document.getElementById('role');
    if (roleSelect) {
        new Choices(roleSelect, {
            searchEnabled: false,
            shouldSort: false,
            itemSelectText: '',
        });
    }

});
</script>
{{-- Choices.js --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

<style>
@keyframes slide-in {
    0% { transform: translateY(-40px); opacity: 0; }
    100% { transform: translateY(0); opacity: 1; }
}
.animate-slide-in {
    animation: slide-in 0.3s ease-out;
}


</style>

@endsection
{{-- ======================= BOTTOM MENU MOBILE ======================= --}}
@section('bottom-menu')
<div class="flex justify-between gap-2 p-2 bg-white shadow-inner border-t fixed bottom-0 left-0 right-0 z-50">
   @php
        $mobileCards = [
            ['icon'=>'🏠','title'=>'Beranda','link'=>route('admin.dashboard')],
            ['icon'=>'💳','title'=>'Transaksi','link'=>route('admin.pelanggan_lunas')],
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