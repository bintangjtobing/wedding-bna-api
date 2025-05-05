<?php

namespace App\Events;

use App\Models\InvitationMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewInvitationMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(InvitationMessage $message)
    {
        $this->message = $message->load('contact:id,name,username');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
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
}
