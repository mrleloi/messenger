<?php

namespace App\Actions\Messages;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\DatabaseManager;
use App\Contracts\BroadcastDriver;
use App\Contracts\EmojiInterface;
use App\Messenger;
use App\Http\Request\MessageRequest;
use App\Models\Message;
use App\Models\Thread;
use Throwable;

class StoreMessage extends NewMessageAction
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

    /**
     * Store new message, update thread updated_at,
     * mark read for participant, broadcast.
     *
     * @param  Thread  $thread
     * @param  array  $params
     * @param  string|null  $senderIp
     * @return $this
     *
     * @see MessageRequest
     *
     * @throws Throwable
     */
    public function execute(Thread $thread,
                            array $params,
                            ?string $senderIp = null): self
    {
        $this->fireEvents($thread, $params, $senderIp, $this->messenger->getProvider());
        $this->setThread($thread)
            ->setMessageType(Message::MESSAGE)
            ->setMessageBody($this->emoji->toShort($params['message']) ?: null)
            ->setMessageOptionalParameters($params)
            ->setMessageOwner($this->messenger->getProvider())
            ->setSenderIp($senderIp)
            ->storeTempMessage()
            ->generateResource();
        return $this;
    }
}
