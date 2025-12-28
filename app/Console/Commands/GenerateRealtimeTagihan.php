<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pelanggan;
use App\Models\Tagihan;
use Carbon\Carbon;

class GenerateRealtimeTagihan extends Command
{
    protected $signature = 'tagihan:generate-realtime';
    protected $description = 'Generate tagihan realtime berdasarkan tanggal register pelanggan';

    public function handle()
    {
        $today = Carbon::now()->day;
        $bulanIni = Carbon::now()->format('Y-m');

        $pelanggans = Pelanggan::with(['package', 'area.sales'])->get();

        foreach ($pelanggans as $pelanggan) {

            if (!$pelanggan->tanggal_register) {
                continue;
            }

            // Cek hanya pelanggan yang tanggal_register = tanggal hari ini
            if (Carbon::parse($pelanggan->tanggal_register)->day != $today) {
                continue;
            }

            // Cegah duplikasi tagihan bulan berjalan
            $exists = Tagihan::where('pelanggan_id', $pelanggan->id)
                ->where('bulan', $bulanIni)
                ->exists();

            if ($exists) {
                continue;
            }

            // Hitung total
            $harga_paket = $pelanggan->package->harga ?? 0;
            $biaya1 = $pelanggan->biaya_tambahan_1 ?? 0;
            $biaya2 = $pelanggan->biaya_tambahan_2 ?? 0;
            $diskon = $pelanggan->diskon ?? 0;

            $total = $harga_paket + $biaya1 + $biaya2 - $diskon;

            Tagihan::create([
                'pelanggan_id' => $pelanggan->id,
                'sales_id'     => $pelanggan->area->sales->id ?? null,
                'bulan'        => $bulanIni,
                'tanggal_tagihan' => now(),
                'jumlah'       => $total,
                'status'       => 'BELUM BAYAR',
            ]);
        }

        return Command::SUCCESS;
    }
}
