<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::create('transaksi', function (Blueprint $table) {
        $table->id();
        $table->foreignId('pelanggan_id')->constrained('pelanggan')->onDelete('cascade');
        $table->string('kode_transaksi')->unique();
        $table->integer('jumlah')->default(0);
        $table->integer('diskon')->default(0);
        $table->enum('metode', ['cash', 'online'])->default('cash');
        $table->string('dibayar_oleh')->nullable();
        $table->string('bulan')->nullable();
        $table->datetime('tanggal_bayar')->nullable();
        $table->string('ip_address')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
