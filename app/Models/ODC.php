<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ODC extends Model
{
    use HasFactory;
    protected $table = 'odc';

    protected $fillable = ['nama','kode','lat','lng','info'];

    // Relasi ke ODP
    public function odps()
    {
        return $this->hasMany(ODP::class, 'odc_id'); // pastikan sesuai kolom foreign key
    }
}
