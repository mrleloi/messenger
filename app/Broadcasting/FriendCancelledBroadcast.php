<?php

namespace App\Broadcasting;

class FriendCancelledBroadcast extends MessengerBroadcast
{
    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'friend.cancelled';
    }
}
