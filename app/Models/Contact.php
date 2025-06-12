<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Contact extends Model
{
    protected $fillable = [
        'admin_id',
        'name',
        'username',
        'phone_number',
        'invitation_status',
        'sent_at',
        'country',
        'country_code',
        'greeting',
    ];

    /**
     * Atribut yang harus diubah tipenya.
     *
     * @var array
     */
    protected $casts = [
        'sent_at' => 'datetime',
    ];

    /**
     * Boot model dan set event listeners
     */
    protected static function boot()
    {
        parent::boot();

        // Generate username otomatis sebelum disimpan
        static::creating(function ($contact) {
            if (empty($contact->username)) {
                $contact->username = self::generateUniqueUsername($contact->name);
            }

            // Set default negara dan kode negara jika tidak ada
            if (empty($contact->country)) {
                $contact->country = 'ID'; // Default Indonesia
            }

            if (empty($contact->country_code)) {
                $contact->country_code = '62'; // Default Indonesia
            }
        });

        static::updating(function ($contact) {
            // Jika nama berubah dan username masih sama dengan username yang dihasilkan dari nama sebelumnya,
            // update username agar tetap konsisten dengan nama baru
            if ($contact->isDirty('name')) {
                $oldName = $contact->getOriginal('name');
                $oldUsername = self::generateUsername($oldName);
                if ($contact->username === $oldUsername) {
                    $contact->username = self::generateUniqueUsername($contact->name);
                }
            }
        });
    }

    /**
     * Generate username dari nama
     */
    public static function generateUsername($name)
    {
        return Str::slug($name);
    }

    /**
     * Generate username unik dari nama
     */
    public static function generateUniqueUsername($name)
    {
        $baseUsername = self::generateUsername($name);
        $username = $baseUsername;
        $count = 1;

        // Cek apakah username sudah ada, jika ada tambahkan angka
        while (self::where('username', $username)->exists()) {
            $username = $baseUsername . '-' . $count++;
        }

        return $username;
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function messageLogs()
    {
        return $this->hasMany(MessageLog::class);
    }

    /**
     * Method untuk mengupdate status undangan
     */
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

    /**
     * Scope untuk mendapatkan kontak berdasarkan status undangan
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('invitation_status', $status);
    }

    /**
     * Personalisasi pesan dengan data kontak
     */
    public function personalizeMessage($message)
    {
        $replacements = [
            '[NAMA]' => $this->name,
            '[USERNAME]' => $this->username,
            '[PANGGILAN]' => $this->greeting ?: $this->name,
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $message);
    }
    /**
     * Get the click logs for this contact
     */
    public function clickLogs()
    {
        return $this->hasMany(\App\Models\ClickLog::class);
    }

    /**
     * Get click statistics for this contact
     */
    public function getClickStatsAttribute()
    {
        $clickLogService = app(\App\Services\ClickLogService::class);
        return $clickLogService->getClickStats($this);
    }
    /**
     * Get latest click log
     */
    public function getLatestClickAttribute()
    {
        return $this->clickLogs()->latest('clicked_at')->first();
    }

    /**
     * Get unique visitors count
     */
    public function getUniqueVisitorsCountAttribute()
    {
        return $this->clickLogs()->distinct('ip_address')->count();
    }
}
