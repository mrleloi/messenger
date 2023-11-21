<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use App\Contracts\MessengerProvider;
use App\Models\Thread;

class KnockEvent
{
    use SerializesModels;

    /**
     * @var MessengerProvider
     */
    public MessengerProvider $provider;

    /**
     * @var Thread
     */
    public Thread $thread;

    /**
     * Create a new event instance.
     *
     * @param  MessengerProvider  $provider
     * @param  Thread  $thread
     */
    public function __construct(MessengerProvider $provider, Thread $thread)
    {
        $this->provider = $provider;
        $this->thread = $thread;
    }
}
