<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $table = 'devices';
    protected $fillable = [
        'id',
        'name',
        'ip_address',
        'api_port',
        'dns_name',
        'username',
        'password',
    ];
    public $timestamps = false;

    // Relasi ke ODP
    public function odp()
    {
        return $this->belongsTo(ODP::class);
    }
}

