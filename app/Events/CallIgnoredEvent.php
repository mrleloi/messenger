<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use App\Contracts\MessengerProvider;
use App\Models\Call;

class CallIgnoredEvent
{
    use SerializesModels;

    /**
     * @var Call
     */
    public Call $call;

    /**
     * @var MessengerProvider
     */
    public MessengerProvider $provider;

    /**
     * Create a new event instance.
     *
     * @param  Call  $call
     * @param  MessengerProvider  $provider
     */
    public function __construct(Call $call, MessengerProvider $provider)
    {
        $this->call = $call;
        $this->provider = $provider;
    }
}
