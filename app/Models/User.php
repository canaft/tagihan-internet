<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'username', 'email', 'password',
        'role', 'nomor_hp', 'wilayah'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Relasi ke pelanggan jika role = sales
    public function pelanggans()
    {
        return $this->hasMany(Pelanggan::class, 'sales_id');
    }

    // Relasi ke pengaduan jika role = teknisi
    public function pengaduans()
    {
        return $this->hasMany(Pengaduan::class, 'teknisi_id');
    }

    // Relasi ke tagihan jika role = sales
    public function tagihans()
    {
        return $this->hasMany(Tagihan::class, 'sales_id');
    }

    public function absensi()
{
    return $this->hasMany(Absensi::class);
}

}
