<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pelanggan;
use App\Models\Tagihan;
use Carbon\Carbon;

class GenerateMonthlyTagihan extends Command
{
    protected $signature = 'tagihan:generate-bulanan';
    protected $description = 'Generate tagihan bulanan otomatis untuk semua pelanggan aktif';

    public function handle()
    {
        $bulanIni = Carbon::now()->format('Y-m');
        $pelanggans = Pelanggan::with('package')
            ->where('is_active', 1)
            ->get();

        $jumlahTagihanBaru = 0;

        foreach ($pelanggans as $pelanggan) {
            // Cek apakah sudah ada tagihan bulan ini
            $sudahAda = Tagihan::where('pelanggan_id', $pelanggan->id)
                ->where('bulan', $bulanIni)
                ->exists();

            if (!$sudahAda) {
                $total = ($pelanggan->paket->harga ?? 0)
                       + ($pelanggan->biaya_tambahan_1 ?? 0)
                       + ($pelanggan->biaya_tambahan_2 ?? 0);

                Tagihan::create([
                    'pelanggan_id' => $pelanggan->id,
                    'bulan' => $bulanIni,
                    'jumlah' => $total,
                    'status' => 'belum_lunas',
                    'metode_bayar' => null,
                ]);

                $jumlahTagihanBaru++;
            }
        }

        $this->info("✅ {$jumlahTagihanBaru} tagihan baru berhasil dibuat untuk bulan {$bulanIni}.");
    }
}
