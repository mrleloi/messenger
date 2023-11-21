<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use App\Contracts\MessengerProvider;
use App\Models\Call;

class CallEndedEvent
{
    use SerializesModels;

    /**
     * @var null|MessengerProvider
     */
    public ?MessengerProvider $provider;

    /**
     * @var Call
     */
    public Call $call;

    /**
     * Create a new event instance.
     *
     * @param  MessengerProvider|null  $provider
     * @param  Call  $call
     */
    public function __construct(?MessengerProvider $provider, Call $call)
    {
        $this->call = $call;
        $this->provider = $provider;
    }
}
