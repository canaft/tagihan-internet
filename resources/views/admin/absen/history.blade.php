@extends('layouts.app')

@section('title', 'History Absensi Teknisi')

@section('navbar')
<nav class="fixed top-0 left-0 w-full bg-[var(--primary-color)] text-white px-4 py-3 flex items-center shadow-md z-50">
    <a href="{{ route('admin.dashboard') }}" class="mr-3 text-lg hover:opacity-80">
        <i class="fas fa-arrow-left"></i>
    </a>
    <p class="font-semibold text-base">History Absensi Teknisi</p>
</nav>
@endsection

@section('content')
<div class="pt-24 pb-28 min-h-screen bg-gray-100">

    {{-- BUTTON KEMBALI --}}
    <div class="mx-4 mb-4">
        <a href="{{ url()->previous() }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-[var(--primary-color)] text-white rounded-xl shadow hover:opacity-90">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- FILTER SECTION --}}
    <div class="mx-4 bg-white p-4 rounded-2xl shadow-md border mt-3">
        <form>
            {{-- PILIH TAHUN --}}
            <label class="text-sm text-gray-600 font-semibold">Pilih Tahun</label>
            <select id="filter_tahun" name="tahun"
                    class="mt-1 w-full border rounded-xl px-3 py-2"
                    onchange="this.form.submit()">
                @php $nowYear = date('Y'); @endphp
                @for ($t = $nowYear; $t >= $nowYear - 5; $t--)
                    <option value="{{ $t }}" {{ request('tahun', $nowYear) == $t ? 'selected' : '' }}>
                        {{ $t }}
                    </option>
                @endfor
            </select>

            {{-- PILIH TEKNISI --}}
            <label class="text-sm text-gray-600 font-semibold mt-4 block">Pilih Teknisi</label>
            <select id="filter_teknisi" name="teknisi"
                    class="mt-1 w-full border rounded-xl px-3 py-2"
                    onchange="this.form.submit()">
                <option value="all">Semua Teknisi</option>
                @foreach ($users as $tk)
                    <option value="{{ $tk->id }}" {{ request('teknisi') == $tk->id ? 'selected' : '' }}>
                        {{ $tk->name }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="px-4 mt-6">
        @php $selectedYear = request('tahun') ?? date('Y'); @endphp

        <p class="text-gray-700 font-bold mb-4 text-base border-l-4 pl-3 border-[var(--primary-color)]">
            Rekap Absensi Tahun {{ $selectedYear }}
        </p>

        {{-- LOOP TEKNISI --}}
        @forelse ($rekap as $id => $data)
            <div class="bg-white rounded-2xl shadow-md border p-4 mb-6">

                {{-- HEADER NAMA --}}
                <div class="flex justify-between items-center mb-3">
                    <div>
                        <p class="font-bold text-lg text-gray-900">{{ $data['nama'] }}</p>
                        <p class="text-xs text-gray-500">{{ $data['role'] }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500">Total Masuk: <strong>{{ $data['total_masuk'] ?? 0 }}</strong></p>
                        <p class="text-xs text-gray-500">Total Pulang: <strong>{{ $data['total_pulang'] ?? 0 }}</strong></p>
                    </div>
                </div>

                {{-- LIST BULAN --}}
                <div class="divide-y">
                    @foreach ($data['bulan'] as $bulan => $detail)
                        @php $modalId = $id.'-'.$bulan.'-'.$selectedYear; @endphp

                        {{-- ROW BULAN --}}
                        <div class="py-3 cursor-pointer flex justify-between items-center flex-wrap"
                             onclick="document.getElementById('modal-{{ $modalId }}').showModal()">
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">
                                    {{ DateTime::createFromFormat('!m', $bulan)->format('F') }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    Masuk: <strong>{{ $detail['total_masuk'] ?? 0 }}</strong>
                                    •
                                    Pulang: <strong>{{ $detail['total_pulang'] ?? 0 }}</strong>
                                </p>
                            </div>

                            {{-- BADGES STATUS --}}
                            <div class="flex gap-2 mt-2 sm:mt-0">
                                <button class="px-3 py-1.5 text-xs rounded-xl bg-yellow-50 text-yellow-700 border"
                                        onclick="showIzinModal('{{ $modalId }}', 'pending', event)">
                                    Pending: {{ $detail['izin_pending'] ?? 0 }}
                                </button>

                                <button class="px-3 py-1.5 text-xs rounded-xl bg-green-50 text-green-700 border"
                                        onclick="showIzinModal('{{ $modalId }}', 'setuju', event)">
                                    Setuju: {{ $detail['izin_disetujui'] ?? 0 }}
                                </button>

                                <button class="px-3 py-1.5 text-xs rounded-xl bg-red-50 text-red-700 border"
                                        onclick="showIzinModal('{{ $modalId }}', 'tolak', event)">
                                    Tolak: {{ $detail['izin_ditolak'] ?? 0 }}
                                </button>
                            </div>
                        </div>

                        {{-- POPUP DETAIL ABSENSI --}}
                        <dialog id="modal-{{ $modalId }}" class="rounded-xl p-0 w-[95%] max-w-lg">
                            <div class="bg-white p-5 rounded-xl shadow-lg">
                                <div class="flex justify-between items-center mb-3">
                                    <p class="font-bold text-gray-700">
                                        Detail Absensi
                                        {{ DateTime::createFromFormat('!m', $bulan)->format('F') }}
                                        {{ $selectedYear }}
                                    </p>
                                    <button onclick="document.getElementById('modal-{{ $modalId }}').close()">✖</button>
                                </div>

                                <table class="w-full text-sm border">
                                    <thead>
                                        <tr class="bg-gray-100">
                                            <th class="p-2 border">Tanggal</th>
                                            <th class="p-2 border">Hari</th>
                                            <th class="p-2 border">Masuk</th>
                                            <th class="p-2 border">Pulang</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($detail['list'] ?? [] as $row)
                                            @php
                                                $y = explode('-', $row['tanggal'])[0];
                                                $m = explode('-', $row['tanggal'])[1];
                                            @endphp
                                            @if($y == $selectedYear && intval($m) == intval($bulan))
                                                <tr>
                                                    <td class="p-1 border text-center">{{ $row['tanggal'] }}</td>
                                                    <td class="p-1 border text-center">{{ $row['hari'] }}</td>
                                                    <td class="p-1 border text-center">{{ $row['jam_masuk'] ?? '-' }}</td>
                                                    <td class="p-1 border text-center">{{ $row['jam_pulang'] ?? '-' }}</td>
                                                </tr>
                                            @endif
                                        @empty
                                            <tr>
                                                <td colspan="4" class="py-2 text-center text-gray-500">Tidak ada data</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </dialog>

                        {{-- POPUP DETAIL IZIN --}}
                        <dialog id="izin-modal-{{ $modalId }}" class="rounded-xl p-0 w-[95%] max-w-lg">
                            <div class="bg-white p-5 rounded-xl shadow-lg">
                                <div class="flex justify-between items-center mb-3">
                                    <p class="font-bold text-gray-700">Detail Izin</p>
                                    <button onclick="document.getElementById('izin-modal-{{ $modalId }}').close()">✖</button>
                                </div>

                                <table class="w-full text-sm border">
                                    <thead>
                                        <tr class="bg-gray-100">
                                            <th class="p-2 border">Tanggal</th>
                                            <th class="p-2 border">Nama</th>
                                            <th class="p-2 border">Status</th>
                                            <th class="p-2 border">Keterangan</th>
                                            <th class="p-2 border">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="izin-modal-body-{{ $modalId }}">
                                        <tr>
                                            <td colspan="5" class="py-2 text-center text-gray-500">Klik badge untuk melihat data</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </dialog>

                    @endforeach
                </div>
            </div>
        @empty
            <div class="text-center text-gray-500 mt-10">Tidak ada data absensi.</div>
        @endforelse

    </div>
</div>
@endsection

@section('bottom-menu')
<div class="flex justify-between gap-2 p-2 bg-white shadow-inner border-t fixed bottom-0 left-0 right-0 z-50">
    @php
        $menu = [
            ['icon'=>'🏠','title'=>'Beranda','link'=>route('admin.dashboard')],
            ['icon'=>'💳','title'=>'Transaksi','link'=>route('admin.pelanggan_lunas')],
            ['icon'=>'💰','title'=>'Bayar','link'=>route('admin.belum_bayar')],
            ['icon'=>'📩','title'=>'Pengaduan','link'=>route('pengaduan.index')],
            ['icon'=>'⚙️','title'=>'Setting','link'=>route('setting')],
        ];
    @endphp

    @foreach($menu as $m)
    <a href="{{ $m['link'] }}"
       class="flex-1 flex flex-col items-center justify-center text-[var(--primary-color)] text-xs bg-white p-2 rounded-lg">
        <div class="text-xl mb-1">{{ $m['icon'] }}</div>
        <span>{{ $m['title'] }}</span>
    </a>
    @endforeach
</div>

{{-- Choices.js --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

<script>
new Choices('#filter_tahun', { searchEnabled: false, shouldSort: false, itemSelectText: '' });
new Choices('#filter_teknisi', { searchEnabled: false, shouldSort: false, itemSelectText: '' });

const izinData = @json($rekap);

function showIzinModal(modalId, status, e) {
    if(e) e.stopPropagation();
    const modal = document.getElementById('izin-modal-' + modalId);
    const tbody = document.getElementById('izin-modal-body-' + modalId);

    const parts = modalId.split('-'); // userId-bulan-tahun
    const userId = parts[0];
    const bulan = parts[1];

    let bulanData = izinData[userId]['bulan'][bulan];
    if(!bulanData || !bulanData.izin_list) {
        tbody.innerHTML = `<tr><td colspan="5" class="py-2 text-center text-gray-500">Tidak ada data</td></tr>`;
        modal.showModal();
        return;
    }

    const filtered = bulanData.izin_list.filter(i => {
        if(status === 'pending') return i.status === 'pending';
        if(status === 'setuju') return i.status === 'disetujui';
        if(status === 'tolak') return i.status === 'ditolak';
        return false;
    });

    if(filtered.length) {
        tbody.innerHTML = filtered.map(i => {
            let actionBtns = '';
            if(i.status === 'pending') {
                actionBtns = `
                    <button class="px-3 py-1 text-xs bg-green-500 text-white rounded hover:opacity-90"
                        onclick="updateIzinStatus(${i.id}, 'disetujui', '${modalId}', event)">
                        Setuju
                    </button>
                    <button class="px-3 py-1 text-xs bg-red-500 text-white rounded hover:opacity-90"
                        onclick="updateIzinStatus(${i.id}, 'ditolak', '${modalId}', event)">
                        Tolak
                    </button>
                `;
            }
            return `
                <tr>
                    <td class="p-1 border text-center">${i.tanggal}</td>
                    <td class="p-1 border text-center">${i.nama}</td>
                    <td class="p-1 border text-center">${i.status}</td>
                    <td class="p-1 border text-center">${i.alasan || '-'}</td>
                    <td class="p-1 border text-center">${actionBtns || '-'}</td>
                </tr>
            `;
        }).join('');
    } else {
        tbody.innerHTML = `<tr><td colspan="5" class="py-2 text-center text-gray-500">Tidak ada data</td></tr>`;
    }

    modal.showModal();
}

function updateIzinStatus(id, status, modalId, e) {
    if(e) e.stopPropagation();
    if(!confirm('Yakin ingin merubah status izin ini?')) return;

    let url = '';
    if(status === 'disetujui') url = '{{ route("admin.izin.setujui", ["id"=>"ID_PLACEHOLDER"]) }}'.replace('ID_PLACEHOLDER', id);
    else if(status === 'ditolak') url = '{{ route("admin.izin.tolak", ["id"=>"ID_PLACEHOLDER"]) }}'.replace('ID_PLACEHOLDER', id);

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/x-www-form-urlencoded',
        },
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            const parts = modalId.split('-');
            const userId = parts[0];
            const bulan = parts[1];

            const izinItem = izinData[userId]['bulan'][bulan].izin_list.find(i => i.id === id);
            if(izinItem) izinItem.status = status === 'disetujui' ? 'disetujui' : 'ditolak';

            refreshBadge(modalId);
            showIzinModal(modalId, 'pending', new Event('click'));
        } else {
            alert('Gagal update status: ' + (data.message ?? 'Terjadi kesalahan'));
        }
    })
    .catch(err => {
        console.error(err);
        alert('Terjadi kesalahan: ' + err.message);
    });
}

function refreshBadge(modalId) {
    const parts = modalId.split('-');
    const userId = parts[0];
    const bulan = parts[1];
    const bulanData = izinData[userId]['bulan'][bulan];
    if(!bulanData) return;

    const row = document.querySelector(`[onclick="document.getElementById('modal-${modalId}').showModal()"]`);

    if(row) {
        const pendingBtn = row.querySelector('button.bg-yellow-50');
        const setujuBtn = row.querySelector('button.bg-green-50');
        const tolakBtn = row.querySelector('button.bg-red-50');

        if(pendingBtn) pendingBtn.innerText = 'Pending: ' + bulanData.izin_list.filter(i => i.status === 'pending').length;
        if(setujuBtn) setujuBtn.innerText = 'Setuju: ' + bulanData.izin_list.filter(i => i.status === 'disetujui').length;
        if(tolakBtn) tolakBtn.innerText = 'Tolak: ' + bulanData.izin_list.filter(i => i.status === 'ditolak').length;
    }
}
</script>
@endsection
