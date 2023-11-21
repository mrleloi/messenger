<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use App\Models\BotAction;

class NewBotActionEvent
{
    use SerializesModels;

    /**
     * @var BotAction
     */
    public BotAction $botAction;

    /**
     * Create a new event instance.
     *
     * @param  BotAction  $botAction
     */
    public function __construct(BotAction $botAction)
    {
        $this->botAction = $botAction;
    }
}
