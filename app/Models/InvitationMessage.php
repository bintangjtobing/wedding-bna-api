<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvitationMessage extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'contact_id',
        'name',
        'message',
        'attendance',
        'is_approved',
    ];

    /**
     * Get the contact associated with the invitation message.
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
}
