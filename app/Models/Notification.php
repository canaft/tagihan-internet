<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
protected $fillable = [
    'user_id',
    'admin_id',
    'type',
    'message',
    'data',
    'is_read',
];



    /**
     * User yang membuat notif
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Admin yang menerima notif
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}