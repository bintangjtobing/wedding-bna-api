<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'is_template',
        'template_name',
    ];

    public function logs()
    {
        return $this->hasMany(MessageLog::class);
    }
}