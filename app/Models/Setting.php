<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key_name', 'value', 'category', 'is_default'];
    public $timestamps = true; // supaya updated_at otomatis ter-update
}
