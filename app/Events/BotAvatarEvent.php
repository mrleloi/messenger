<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use App\Contracts\MessengerProvider;
use App\Models\Bot;

class BotAvatarEvent
{
    use SerializesModels;

    /**
     * @var MessengerProvider
     */
    public MessengerProvider $provider;

    /**
     * @var Bot
     */
    public Bot $bot;

    /**
     * Create a new event instance.
     *
     * @param  MessengerProvider  $provider
     * @param  Bot  $bot
     */
    public function __construct(MessengerProvider $provider, Bot $bot)
    {
        $this->provider = $provider;
        $this->bot = $bot;
    }
}
