<?php

namespace App\Events;

use App\Models\InvitationMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NewInvitationMessage implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $message;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(InvitationMessage $message)
    {
        // Log untuk memverifikasi bahwa event diterima dengan benar
        Log::info('NewInvitationMessage Event triggered', [
            'invitationMessageId' => $message->id,
            'contact' => $message->contact->name ?? 'No Contact Found'
        ]);

        // Memuat relasi yang dibutuhkan
        $this->message = $message->load('contact:id,name,username');

        // Log untuk memastikan relasi dimuat
        Log::info('Invitation Message Loaded', [
            'message' => $this->message->message,
            'contact' => $this->message->contact->name ?? 'No Contact Found'
        ]);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        Log::info('Broadcasting event on channel "messages"');
        // Broadcast ke channel publik
        return new Channel('messages');
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'new-message';
    }

    /**
     * Menentukan data yang dibroadcast (opsional)
     *
     * @return array
     */
    public function broadcastWith()
    {
        Log::info('Broadcasting with data', ['message' => $this->message->message]);
        return [
            'message' => $this->message,
        ];
    }
}