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
        try {
            $thread = $event->thread;
            $params = $event->params;
            $senderIp = $event->senderIp;
            $provider = $event->provider;

            $messageType = Message::MESSAGE;
            if (isset($params['message'])) {
                $messageBody = $this->emoji->toShort($params['message']) ?: null;
            }

            if (isset($params['messageType']))  {
                if ($params['messageType'] == Message::IMAGE_MESSAGE) {
                    $messageType = Message::IMAGE_MESSAGE;
                    $messageBody = $params['messageBody'];
                } else if ($params['messageType'] == Message::DOCUMENT_MESSAGE) {
                    $messageType = Message::DOCUMENT_MESSAGE;
                    $messageBody = $params['messageBody'];
                } else if ($params['messageType'] == Message::AUDIO_MESSAGE) {
                    $messageType = Message::AUDIO_MESSAGE;
                    $messageBody = $params['messageBody'];
                } else if ($params['messageType'] == Message::VIDEO_MESSAGE) {
                    $messageType = Message::VIDEO_MESSAGE;
                    $messageBody = $params['messageBody'];
                }
            }

            $this->setThread($thread)
                ->setMessageType($messageType)
                ->setMessageBody($messageBody)
                ->setMessageOptionalParameters($params)
                ->setMessageOwner($provider)
                ->setSenderIp($senderIp)
                ->process()
                ->generateResource()
                ->fireBroadcast();
        } catch (Exception $e) {
            var_dump($e->getMessage());
            var_dump($e);
            $this->fileService
                ->setDisk($this->getThread()->getStorageDisk())
                ->destroy("{$this->getThread()->getImagesDirectory()}/$image");
            throw $e;
        }

        return $this;
    }
}
