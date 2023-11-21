<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use App\Models\Bot;

class NewBotEvent
{
    use SerializesModels;

    /**
     * @var Bot
     */
    public Bot $bot;

    /**
     * Create a new event instance.
     *
     * @param  Bot  $bot
     */
    public function __construct(Bot $bot)
    {
        $this->bot = $bot;
    }
}
