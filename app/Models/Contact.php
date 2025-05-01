<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;
    protected $fillable = [
        'admin_id',
        'name',
        'phone_number',
        'invitation_status',
        'sent_at',
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
