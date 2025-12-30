<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keuangans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('tanggal');
            $table->text('deskripsi');
            $table->decimal('nominal', 15, 2);
            $table->string('tipe', 20); // pemasukan / pengeluaran
            $table->timestamps();

            // Relasi ke tabel users
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keuangans');
    }
};
