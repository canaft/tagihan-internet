<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi'; // sesuaikan dengan nama tabel kamu
    protected $fillable = [
        'pelanggan_id',
        'jumlah',
        'diskon',
        'metode',
        'dibayar_oleh',
        'tanggal_bayar',
        'bulan',
        'kode_transaksi',
        'ip_address',
    ];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function tagihan()
{
    return $this->belongsTo(\App\Models\Tagihan::class, 'tagihan_id');
}
public function teknisi()
{
    return $this->belongsTo(User::class, 'teknisi_id');
}

}
