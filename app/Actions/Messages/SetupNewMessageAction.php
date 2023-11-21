<?php

namespace App\Actions\Messages;

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

class SetupNewMessageAction extends NewMessageAction
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

    public function execute(NewMessageEvent $event): self
    {
        $thread = $event->thread;
        $params = $event->params;
        $senderIp = $event->senderIp;
        $provider = $event->provider;

        $this->setThread($thread)
            ->setMessageType(Message::MESSAGE)
            ->setMessageBody($this->emoji->toShort($params['message']) ?: null)
            ->setMessageOptionalParameters($params)
            ->setMessageOwner($provider)
            ->setSenderIp($senderIp)
            ->process()
            ->generateResource()
            ->fireBroadcast();

        return $this;
    }
}
