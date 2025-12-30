<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tagihan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pelanggan_id');
            $table->unsignedBigInteger('sales_id')->nullable();

            $table->string('bulan'); // contoh: 2025-01
            $table->date('tanggal_tagihan')->nullable();
            $table->date('tanggal_bayar')->nullable();

            $table->decimal('jumlah', 15, 2);
            $table->string('status')->default('belum_bayar');

            $table->string('metode_bayar')->nullable();
            $table->decimal('diskon', 15, 2)->nullable()
                  ->comment('Diskon saat tagihan dibuat');

            $table->timestamps();

            // Relasi
            $table->foreign('pelanggan_id')
                  ->references('id')
                  ->on('pelanggan')
                  ->onDelete('cascade');

            $table->foreign('sales_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tagihan');
    }
};
