<?php

namespace App\Broadcasting;

class CallEndedBroadcast extends MessengerBroadcast
{
    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'call.ended';
    }
}
