<?php

namespace App\Actions\Messages;

use App\Events\NewSystemMessageEvent;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\Log;
use App\Actions\Messages\NewMessageAction;
use App\Contracts\BroadcastDriver;
use App\Contracts\EmojiInterface;
use App\Events\NewMessageEvent;
use App\Http\Request\MessageRequest;
use App\Messenger;
use App\Models\Message;
use App\Models\Thread;
use Exception;

class SetupNewSystemMessageAction extends NewSystemMessageAction
{
    /**
     * @var Messenger
     */
    private Messenger $messenger;

    /**
     * @var EmojiInterface
     */
    private EmojiInterface $emoji;

    /**
     * StoreMessage constructor.
     *
     * @param  BroadcastDriver  $broadcaster
     * @param  DatabaseManager  $database
     * @param  Dispatcher  $dispatcher
     * @param  Messenger  $messenger
     * @param  EmojiInterface  $emoji
     */
    public function __construct(BroadcastDriver $broadcaster,
                                DatabaseManager $database,
                                Dispatcher $dispatcher,
                                Messenger $messenger,
                                EmojiInterface $emoji)
    {
        parent::__construct(
            $broadcaster,
            $database,
            $dispatcher
        );

        $this->messenger = $messenger;
        $this->emoji = $emoji;
    }

    public function execute(NewSystemMessageEvent $event): self
    {
        $thread = $event->thread;
        $type = $event->type;
        $body = $event->body;
        $provider = $event->provider;

        $this->setThread($thread)
            ->setMessageType($type)
            ->setMessageBody($body)
            ->setMessageOwner($provider)
            ->process()
            ->generateResource()
            ->fireBroadcast();

        return $this;
    }
}
