<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Package;


class Pelanggan extends Model
{
    use HasFactory;

    protected $table = 'pelanggan';

protected $fillable = [
    'name',
    'phone',
    'paket_id',
    'tanggal_register',
    'tanggal_tagihan',
    'tanggal_isolir',
    'area_id',
    'nama_biaya_1',
    'biaya_tambahan_1',
    'nama_biaya_2',
    'biaya_tambahan_2',
    'diskon', // <- wajib ada
    'device_id',
    'odp_id',
    'latitude',
    'longitude',
    'status',
    'is_active',
    'activated_at',
];



    // protected static function booted()
    // {
    //     // ===============================
    //     // TAGIHAN OTOMATIS UNTUK PELANGGAN BARU
    //     // ===============================
    //     static::created(function ($pelanggan) {
    //         if ($pelanggan->is_active) {
    //             $bulanIni = now()->format('Y-m-01');

    //             Tagihan::create([
    //                 'pelanggan_id' => $pelanggan->id,
    //                 'bulan' => $bulanIni,
    //                 'jumlah' => $pelanggan->package->harga ?? 0,
    //                 'status' => 'belum_lunas',
    //                 'metode_bayar' => 'cash',
    //                 'tanggal_tagihan' => now(),
    //             ]);
    //         }
    //     });
    // }
public function getTotalTagihanAttribute()
{
    $hargaPaket = $this->package->harga ?? 0;
    $biaya1     = $this->biaya_tambahan_1 ?? 0;
    $biaya2     = $this->biaya_tambahan_2 ?? 0;
    $diskon     = $this->diskon ?? 0; // PERSEN

    $subtotal = $hargaPaket + $biaya1 + $biaya2;
    $potongan = $subtotal * $diskon / 100;

    return $subtotal - $potongan;
}




    // Relasi ke paket
public function package()
{
    return $this->belongsTo(Package::class, 'paket_id'); // pastikan kolom foreign key sesuai
}




    // Relasi ke sales (user dengan role sales)
    public function sales()
    {
        return $this->belongsTo(User::class, 'sales_id');
    }

    // Relasi ke tagihan
    public function tagihan()
    {
        return $this->hasMany(Tagihan::class);
    }

    // Relasi ke pengaduan
    public function pengaduan()
    {
        return $this->hasMany(Pengaduan::class);
    }
    public function odp()
{
    return $this->belongsTo(ODP::class);
}
public function devices()
{
    return $this->belongsTo(ODP::class);
}

public function area()
{
    return $this->belongsTo(Area::class); // pastikan foreign key sesuai
}

    public function device()
    {
        return $this->belongsTo(Device::class); // pastikan field foreign key: device_id
    }

    
public function tagihans() {
    return $this->hasMany(Tagihan::class, 'pelanggan_id');
}
public function transaksi()
{
    return $this->hasMany(Transaksi::class, 'pelanggan_id');
}

}
