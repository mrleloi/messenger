<?php

namespace App\Broadcasting;

class DemotedAdminBroadcast extends MessengerBroadcast
{
    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'demoted.admin';
    }
}
