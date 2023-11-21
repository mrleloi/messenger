<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use App\Models\MessageReaction;

class ReactionAddedEvent
{
    use SerializesModels;

    /**
     * @var MessageReaction
     */
    public MessageReaction $reaction;

    /**
     * Create a new event instance.
     *
     * @param  MessageReaction  $reaction
     */
    public function __construct(MessageReaction $reaction)
    {
        $this->reaction = $reaction;
    }
}
