<?php

namespace App\Broadcasting;

class ReactionAddedBroadcast extends MessengerBroadcast
{
    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'reaction.added';
    }
}
