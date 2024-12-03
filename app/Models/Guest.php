<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug_name', 'phone_number', 'email', 'profile_picture', 'comment'];

    // Ensure the slug_name is always lowercase and hyphenated
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->slug_name = strtolower(str_replace(' ', '-', $model->name));
        });
    }
}