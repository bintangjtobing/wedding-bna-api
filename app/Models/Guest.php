<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug_name', 'phone_number', 'email', 'profile_picture', 'comment', 'attendance_name', // Kolom baru
        'attendance_message', // Kolom baru
        'attend'];
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    // Relasi ke komentar tamu
    public function comment()
    {
        return $this->belongsTo(Guest::class, 'comment_id');
    }
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->slug_name = strtolower(str_replace(' ', '-', $model->name));
        });
    }
}