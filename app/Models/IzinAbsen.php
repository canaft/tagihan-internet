<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IzinAbsen extends Model
{
    use HasFactory;

    protected $table = 'izin_absen';
    protected $fillable = ['user_id', 'tanggal', 'alasan', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
