<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClickLog extends Model
{
    protected $fillable = [
        'contact_id',
        'username',
        'name',
        'ip_address',
        'country',
        'city',
        'region',
        'continent',
        'latitude',
        'longitude',
        'zipcode',
        'country_emoji',
        'device_name',
        'device_type',
        'device_brand',
        'os_name',
        'browser_name',
        'clicked_at',
    ];

    protected $casts = [
        'clicked_at' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Get the contact that owns the click log.
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Scope untuk mendapatkan click logs berdasarkan username
     */
    public function scopeByUsername($query, $username)
    {
        return $query->where('username', $username);
    }

    /**
     * Scope untuk mendapatkan click logs berdasarkan tanggal
     */
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('clicked_at', $date);
    }

    /**
     * Scope untuk mendapatkan click logs terbaru
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('clicked_at', 'desc');
    }
}
