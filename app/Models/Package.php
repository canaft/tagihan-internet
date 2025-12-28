<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;
    protected $table = 'packages'; // ← tambahkan baris ini

    protected $fillable = ['nama_paket', 'kecepatan', 'harga', 'kuota'];

    // Relasi ke pelanggan
    public function pelanggans()
    {
        return $this->hasMany(Pelanggan::class);
    }

    
}
