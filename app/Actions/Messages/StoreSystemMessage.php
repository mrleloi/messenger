<?php

namespace App\Actions\Messages;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\DatabaseManager;
use App\Contracts\BroadcastDriver;
use App\Contracts\MessengerProvider;
use App\Messenger;
use App\Models\Thread;
use Throwable;

class StoreSystemMessage extends NewSystemMessageAction
{
    /**
     * @var Messenger
     */
    private Messenger $messenger;

    /**
     * StoreSystemMessage constructor.
     */
    public function __construct(BroadcastDriver $broadcaster,
                                DatabaseManager $database,
                                Dispatcher $dispatcher,
                                Messenger $messenger)
    {
        parent::__construct(
            $broadcaster,
            $database,
            $dispatcher
        );

        $this->messenger = $messenger;
    }

    /**
     * Store new system message, update thread updated_at.
     *
     * @param  Thread  $thread
     * @param  MessengerProvider  $provider
     * @param  string  $body
     * @param  int  $type
     * @return $this
     *
     * @throws Throwable
     */
    public function execute(Thread $thread,
                            MessengerProvider $provider,
                            string $body,
                            int $type): self
    {
        if ($this->messenger->isSystemMessagesEnabled()) {
            $this->fireEvents($thread, $provider, $body, $type);
            $this->setThread($thread)
                ->setMessageType($type)
                ->setMessageBody($body)
                ->setMessageOwner($provider)
                ->storeTempMessage()
                ->generateResource();
        }

        return $this;
    }
}
