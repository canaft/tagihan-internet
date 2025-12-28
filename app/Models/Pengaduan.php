<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengaduan extends Model
{
    use HasFactory;

    protected $table = 'pengaduan'; // nama tabel sebenarnya

    public $timestamps = true;

    protected $fillable = [
        'pelanggan_id',
        'id_teknisi',      // pastikan sesuai database
        'jenis_pengaduan',
        'deskripsi',
        'status',
        'bukti_foto',       // kolom baru untuk foto bukti
        'tanggal_update'
    ];

    // Relasi ke pelanggan
    public function pelanggan() 
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id');
    }

    // Relasi ke teknisi (User)
    public function teknisi()
    {
        return $this->belongsTo(User::class, 'id_teknisi');
    }
}
