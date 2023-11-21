<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use App\Models\Message;

class MessageEditedEvent
{
    use SerializesModels;

    /**
     * @var Message
     */
    public Message $message;

    /**
     * @var string|null
     */
    public ?string $originalBody;

    /**
     * Create a new event instance.
     *
     * @param  Message  $message
     * @param  string|null  $originalBody
     */
    public function __construct(Message $message, ?string $originalBody)
    {
        $this->message = $message;
        $this->originalBody = $originalBody;
    }
}
