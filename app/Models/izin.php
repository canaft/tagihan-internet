<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class izin extends Model
{
    protected $table = 'izin_absen';

    protected $fillable = [
        'user_id',
        'tanggal',
        'alasan',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
