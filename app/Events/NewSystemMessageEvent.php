<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use App\Contracts\MessengerProvider;
use App\Models\Message;
use App\Models\Thread;

class NewSystemMessageEvent
{
    use SerializesModels;

    /**
     * @var Message
     */
    public Message $message;

    /**
     * @var Thread
     */
    public Thread $thread;

    /**
     * @var bool
     */
    public array $params;

    public string $body;

    public int $type;

    /**
     * @var string|null
     */
    public ?string $senderIp;

    public MessengerProvider $provider;

    /**
     * Create a new event instance.
     *
     * @param  Thread  $thread
     * @param  array  $params
     * @param  string|null  $senderIp
     */
    public function __construct(Thread $thread,
                                MessengerProvider $provider,
                                string $body,
                                int $type)
    {
        $this->thread = $thread;
        $this->provider = $provider;
        $this->body = $body;
        $this->type = $type;
    }
}
