<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Contact extends Model
{
    protected $fillable = [
        'admin_id',
        'name',
        'phone_number',
        'invitation_status',
        'sent_at',
    ];

    /**
     * Atribut yang harus diubah tipenya.
     *
     * @var array
     */
    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function messageLogs()
    {
        return $this->hasMany(MessageLog::class);
    }

    // Method untuk mengupdate status undangan
    public function updateInvitationStatus($status, $timestamp = null)
    {
        $this->invitation_status = $status;

        if ($status == 'terkirim' && $timestamp === null) {
            $this->sent_at = now();
        } elseif ($timestamp !== null) {
            $this->sent_at = $timestamp;
        }

        return $this->save();
    }

    // Scope untuk mendapatkan kontak berdasarkan status undangan
    public function scopeWithStatus($query, $status)
    {
        return $query->where('invitation_status', $status);
    }
}
