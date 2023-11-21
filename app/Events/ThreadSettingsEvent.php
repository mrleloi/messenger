<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use App\Contracts\MessengerProvider;
use App\Models\Thread;

class ThreadSettingsEvent
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
     * @var bool
     */
    public bool $nameChanged;

    /**
     * Create a new event instance.
     *
     * @param  MessengerProvider  $provider
     * @param  Thread  $thread
     * @param  bool  $nameChanged
     */
    public function __construct(MessengerProvider $provider,
                                Thread $thread,
                                bool $nameChanged)
    {
        $this->provider = $provider;
        $this->thread = $thread;
        $this->nameChanged = $nameChanged;
    }
}
