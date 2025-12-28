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
Schema::create('tagihan', function (Blueprint $table) {
    $table->id();
    $table->foreignId('pelanggan_id')->constrained('pelanggan')->onDelete('cascade');
    $table->foreignId('sales_id')->constrained('users')->onDelete('cascade');
    $table->string('bulan'); // contoh: Oktober 2025
    $table->decimal('jumlah', 10, 2);
    $table->enum('status', ['belum dibayar', 'lunas'])->default('belum dibayar');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihan');
    }
};
