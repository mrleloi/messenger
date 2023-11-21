<?php

namespace App\Broadcasting;

class ThreadSettingsBroadcast extends MessengerBroadcast
{
    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'thread.settings';
    }
}
