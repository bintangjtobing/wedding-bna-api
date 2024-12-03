<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use HasFactory;

    protected $fillable = ['guest_id', 'commented_by_id', 'reply'];

    // Relasi ke Guest (komentar yang dibalas)
    public function guest()
    {
        return $this->belongsTo(Guest::class, 'guest_id');
    }

    // Relasi ke Admin atau User yang memberikan balasan
    public function commentedBy()
    {
        return $this->belongsTo(User::class, 'commented_by_id');
    }
}