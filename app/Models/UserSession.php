<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'refresh_token_hash',
        'device_id',
        'device_name',
        'ip_address',
        'user_agent',
        'last_activity_at'
    ];
}
