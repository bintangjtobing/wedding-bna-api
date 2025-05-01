<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable, HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'whatsapp_number',
        'whatsapp_api_key',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'whatsapp_api_key',
    ];

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function messageLogs()
    {
        return $this->hasMany(MessageLog::class);
    }
}
