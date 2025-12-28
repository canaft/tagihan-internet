<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ODP extends Model
{
    use HasFactory;
    protected $table = 'odp'; // pastikan sesuai tabel

    protected $fillable = ['user_id','odc_id','kode','nama','lat','lng','info'];

    // Relasi ke ODC
    public function odc()
    {
        return $this->belongsTo(ODC::class, 'odc_id'); // pastikan 'odc_id' sesuai kolom di DB
    }
}
