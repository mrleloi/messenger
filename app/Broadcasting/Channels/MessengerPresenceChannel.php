<?php

namespace App\Broadcasting\Channels;

use Illuminate\Broadcasting\Channel;
use App\Contracts\HasPresenceChannel;

class MessengerPresenceChannel extends Channel
{
    /**
     * Create a new presence channel instance.
     *
     * @param  HasPresenceChannel  $model
     */
    public function __construct(HasPresenceChannel $model)
    {
        parent::__construct('presence-messenger.'.$model->getPresenceChannel());
    }
}
