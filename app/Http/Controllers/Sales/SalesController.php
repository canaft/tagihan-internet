<?php

namespace App\Http\Controllers\Seles;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SalesController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        // Pelanggan baru (misal 7 hari terakhir)
        $pelangganBaru = Pelanggan::where('wilayah', $user->wilayah)
                            ->where('created_at', '>=', Carbon::now()->subDays(7))
                            ->get();

        // Tagihan baru bulan ini
        $bulanIni = Carbon::now()->format('Y-m');
        $tagihanBaru = Tagihan::whereHas('pelanggan', function($q) use ($user) {
                                $q->where('wilayah', $user->wilayah);
                            })
                            ->where('bulan', $bulanIni)
                            ->get();

        $notifCount = $pelangganBaru->count() + $tagihanBaru->count();

        return view('sales.dashboard', compact(
            'user', 'pelangganBaru', 'tagihanBaru', 'notifCount'
        ));
    }
}

