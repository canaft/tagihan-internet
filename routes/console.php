<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| Di sini kita mendefinisikan semua scheduler & command artisan
|
*/

// Contoh bawaan Laravel
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ===============================
// Scheduler harian (realtime)
// Bisa dipakai untuk tugas harian lain, misal tagihan trial
// ===============================
Schedule::command('tagihan:generate-realtime')
    ->daily()
    ->withoutOverlapping()
    ->runInBackground();

// ===============================
// Scheduler bulanan (otomatis generate tagihan semua pelanggan)
// ===============================

// Tes dulu tiap menit:
Schedule::command('tagihan:auto-generate')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();

// Setelah produksi, ganti menjadi:
// Schedule::command('tagihan:auto-generate')
//     ->monthlyOn(1, '00:00')
//     ->withoutOverlapping()
//     ->runInBackground();
