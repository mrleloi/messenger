<?php

namespace App\Broadcasting\Channels;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Resources\ProviderResource;
use App\Messenger;
use App\Models\Thread;

class ThreadChannel
{
    use AuthorizesRequests;

    /**
     * @var Messenger
     */
    public Messenger $messenger;

    /**
     * Create a new channel instance.
     *
     * @param  Messenger  $messenger
     */
    public function __construct(Messenger $messenger)
    {
        $this->messenger = $messenger;
    }

    /**
     * Authenticate the provider's access to the channel.
     *
     * @param $user
     * @param  Thread  $thread
     * @return ProviderResource
     *
     * @throws AuthorizationException
     */
    public function join($user, Thread $thread): ProviderResource
    {
        $this->authorize('socket', $thread);

        return new ProviderResource(
            $this->messenger->getProvider(),
            false,
            null,
            false
        );
    }
}
