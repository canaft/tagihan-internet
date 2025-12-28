<?php

namespace App\Observers;

use App\Models\Tagihan;
use App\Models\Pelanggan;

class TagihanObserver
{
    /**
     * Handle the Tagihan "updated" event.
     */
    public function updated(Tagihan $tagihan)
    {
        // Jika status tagihan berubah menjadi 'lunas'
        if ($tagihan->status === 'lunas') {
            $pelanggan = $tagihan->pelanggan;

            if ($pelanggan) {
                // Cek apakah semua tagihan pelanggan sudah lunas
                $totalTagihan = $pelanggan->tagihan()->count();
                $lunasTagihan = $pelanggan->tagihan()->where('status', 'lunas')->count();

                // Jika semua lunas, update status pelanggan menjadi 'lunas'
                if ($totalTagihan > 0 && $totalTagihan === $lunasTagihan) {
                    $pelanggan->status = 'lunas';
                    $pelanggan->save();
                }
            }
        }
    }

    /**
     * Handle the Tagihan "created" event.
     * Misal ada tagihan baru, pelanggan otomatis non-lunas
     */
    public function created(Tagihan $tagihan)
    {
        $pelanggan = $tagihan->pelanggan;
        if ($pelanggan) {
            $pelanggan->status = 'belum_lunas';
            $pelanggan->save();
        }
    }
}
