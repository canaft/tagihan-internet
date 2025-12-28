<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Admin\TagihanController;

class AutoGenerateTagihan extends Command
{
    protected $signature = 'tagihan:auto-generate';
    protected $description = 'Generate tagihan otomatis berdasarkan tanggal register';

    public function handle()
    {
        // Panggil function dari Controller
        $controller = new TagihanController;
        $controller->autoGenerateTagihan();

        $this->info('Tagihan otomatis berhasil digenerate.');
    }
}
