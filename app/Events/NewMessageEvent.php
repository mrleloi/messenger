<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use App\Contracts\MessengerProvider;
use App\Models\Message;
use App\Models\Thread;

class NewMessageEvent
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
                                array $params,
                                ?string $senderIp = null,
                                MessengerProvider $provider = null)
    {
        $this->thread = $thread;
        $this->params = $params;
        $this->senderIp = $senderIp;
        $this->provider = $provider;
    }
}
