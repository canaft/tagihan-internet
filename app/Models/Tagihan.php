<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    protected $table = 'tagihan'; // PENTING

protected $fillable = [
    'pelanggan_id', 'sales_id', 'bulan', 'jumlah', 'status', 'tanggal_tagihan','diskon'
];

protected $attributes = [
    'metode_bayar' => 'cash',
];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id');
    }


    public function sales()
    {
        return $this->belongsTo(User::class, 'sales_id');
    }

    
public function transaksi()
{
    return $this->hasOne(Transaksi::class, 'bulan', 'bulan')
                ->where('pelanggan_id', $this->pelanggan_id)
                ->where('status', 'lunas');
}

}
