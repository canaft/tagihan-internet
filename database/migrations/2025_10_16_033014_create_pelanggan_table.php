<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelanggan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->string('status')->nullable();
            $table->string('phone')->nullable();
            $table->date('tanggal_register')->nullable();
            $table->foreignId('paket_id')->nullable()->constrained('packages')->onDelete('set null');
            $table->decimal('diskon', 10, 2)->default(0);
            $table->date('tanggal_tagihan')->nullable();
            $table->date('tanggal_isolir')->nullable();
            $table->foreignId('area_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('odp_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('koordinat')->nullable();
            $table->text('keterangan_odp')->nullable();
            $table->foreignId('device_id')->nullable()->constrained()->onDelete('set null');
            $table->string('nama_biaya_1')->nullable();
            $table->decimal('biaya_tambahan_1', 15, 2)->nullable();
            $table->string('nama_biaya_2')->nullable();
            $table->decimal('biaya_tambahan_2', 15, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('activated_at')->nullable();
            $table->date('tanggal_aktivasi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelanggan');
    }
};
