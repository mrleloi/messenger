<?php

namespace App\Listeners;

use App\Events\NewMessageEvent;
use App\Jobs\SetupNewMessage;

class MessageSubscriber
{
    public static array $subscribers;

    public function __construct()
    {
        self::$subscribers = config('messenger.messages.subscriber');
    }

    public function newMessage(NewMessageEvent $event): void
    {
        if ($this->shouldDispatchMessageHandler($event)) {
            self::getSubscriber('queued')
                ?
                SetupNewMessage::dispatch($event)->onQueue(self::getSubscriber('channel'))
                : SetupNewMessage::dispatchSync($event);
        }
    }

    private function shouldDispatchMessageHandler(NewMessageEvent $event): bool
    {
        return self::getSubscriber('enabled');
    }

    public function getSubscriber(string $option)
    {
        return self::$subscribers[$option];
    }

    public function setSubscriber(string $option, $value): self
    {
        self::$subscribers[$option] = $value;

        return $this;
    }
}
