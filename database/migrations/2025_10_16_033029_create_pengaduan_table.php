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
Schema::create('pengaduan', function (Blueprint $table) {
    $table->id();
    $table->foreignId('pelanggan_id')->constrained('pelanggan')->onDelete('cascade');
    $table->text('keluhan');
    $table->foreignId('teknisi_id')->nullable()->constrained('users')->onDelete('set null');
    $table->enum('status', ['baru', 'dikirim ke teknisi', 'selesai'])->default('baru');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaduan');
    }
};
