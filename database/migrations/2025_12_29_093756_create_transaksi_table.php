<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pelanggan_id');

            $table->string('kode_transaksi')->unique();
            $table->decimal('jumlah', 15, 2);
            $table->decimal('diskon', 15, 2)->nullable();

            $table->string('metode')->nullable(); // cash, transfer, dll
            $table->string('status')->default('pending');

            $table->string('dibayar_oleh')->nullable();
            $table->string('bulan')->nullable(); // contoh: 2025-01

            $table->date('tanggal_bayar')->nullable();
            $table->string('ip_address')->nullable();

            $table->timestamps();

            // Relasi
            $table->foreign('pelanggan_id')
                  ->references('id')
                  ->on('pelanggan')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
