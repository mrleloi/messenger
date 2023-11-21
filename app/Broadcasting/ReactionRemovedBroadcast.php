<?php

namespace App\Broadcasting;

class ReactionRemovedBroadcast extends MessengerBroadcast
{
    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'reaction.removed';
    }
}
