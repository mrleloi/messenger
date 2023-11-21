<?php

namespace App\Broadcasting\ClientEvents;

use App\Broadcasting\MessengerBroadcast;

class StopTyping extends MessengerBroadcast
{
    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'client-stop-typing';
    }
}
