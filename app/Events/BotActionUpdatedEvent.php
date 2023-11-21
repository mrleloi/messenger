<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use App\Contracts\MessengerProvider;
use App\Models\BotAction;

class BotActionUpdatedEvent
{
    use SerializesModels;

    /**
     * @var MessengerProvider
     */
    public MessengerProvider $provider;

    /**
     * @var BotAction
     */
    public BotAction $action;

    /**
     * Create a new event instance.
     *
     * @param  MessengerProvider  $provider
     * @param  BotAction  $action
     */
    public function __construct(MessengerProvider $provider, BotAction $action)
    {
        $this->action = $action;
        $this->provider = $provider;
    }
}
