<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use App\Contracts\MessengerProvider;

class BotActionRemovedEvent
{
    use SerializesModels;

    /**
     * @var null|MessengerProvider
     */
    public ?MessengerProvider $provider;

    /**
     * @var array
     */
    public array $action;

    /**
     * Create a new event instance.
     *
     * @param  MessengerProvider|null  $provider
     * @param  array  $action
     */
    public function __construct(?MessengerProvider $provider, array $action)
    {
        $this->provider = $provider;
        $this->action = $action;
    }
}
