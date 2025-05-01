<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_id',
        'contact_id',
        'admin_id',
        'status',
        'response',
    ];

    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
