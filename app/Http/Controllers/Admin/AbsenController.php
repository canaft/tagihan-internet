<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Absensi;
use App\Models\IzinAbsen;
use Carbon\Carbon;

class AbsenController extends Controller
{
    public function history(Request $request)
    {
        $tahun = $request->tahun ?? date('Y');
        $selectedTeknisi = $request->teknisi ?? 'all';

        $users = User::where('role', 'teknisi')->get();
        $rekap = [];

        foreach ($users as $user) {

            if ($selectedTeknisi != 'all' && $user->id != $selectedTeknisi) {
                continue;
            }

            $rekap[$user->id]['nama'] = $user->name;
            $rekap[$user->id]['role'] = $user->role;
            $rekap[$user->id]['total_masuk'] = 0;
            $rekap[$user->id]['total_pulang'] = 0;
            $rekap[$user->id]['bulan'] = [];

            for ($m = 1; $m <= 12; $m++) {

                $absenRecords = Absensi::where('user_id', $user->id)
                    ->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', $m)
                    ->orderBy('tanggal', 'asc')
                    ->get();

                $izinRecords = IzinAbsen::where('user_id', $user->id)
                    ->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', $m)
                    ->get();

                $totalMasuk = $absenRecords->whereNotNull('jam_masuk')->count();
                $totalPulang = $absenRecords->whereNotNull('jam_pulang')->count();

                $izinPending = $izinRecords->where('status', 'pending')->count();
                $izinDisetujui = $izinRecords->where('status', 'disetujui')->count();
                $izinDitolak = $izinRecords->where('status', 'ditolak')->count();

                $listAbsen = [];
                foreach ($absenRecords as $row) {
                    $listAbsen[] = [
                        'tanggal' => $row->tanggal,
                        'hari' => Carbon::parse($row->tanggal)->locale('id')->translatedFormat('l'),
                        'jam_masuk' => $row->jam_masuk,
                        'jam_pulang' => $row->jam_pulang,
                    ];
                }

                // **List izin untuk modal**
$listIzin = $izinRecords->map(function($row) use ($user){
    return [
        'id' => $row->id,           // penting: tambahkan id
        'tanggal' => $row->tanggal,
        'status' => $row->status,
        'alasan' => $row->alasan,
        'nama' => $user->name,
    ];
})->toArray();


                $rekap[$user->id]['bulan'][$m] = [
                    'total_masuk' => $totalMasuk,
                    'total_pulang' => $totalPulang,
                    'izin_pending' => $izinPending,
                    'izin_disetujui' => $izinDisetujui,
                    'izin_ditolak' => $izinDitolak,
                    'list' => $listAbsen,
                    'izin_list' => $listIzin
                ];

                $rekap[$user->id]['total_masuk'] += $totalMasuk;
                $rekap[$user->id]['total_pulang'] += $totalPulang;
            }
        }

        return view('admin.absen.history', [
            'rekap' => $rekap,
            'tahun' => $tahun,
            'users' => $users,
        ]);

    }

public function updateIzinStatus(Request $request, $id)
{
    try {
        $izin = IzinAbsen::findOrFail($id);

        $status = $request->input('status');

        if (!in_array($status, ['pending','disetujui','ditolak'])) {
            return response()->json(['success'=>false, 'message'=>'Status tidak valid']);
        }

        $izin->status = $status;
        $izin->save();

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        return response()->json(['success'=>false, 'message' => $e->getMessage()]);
    }
}


}
