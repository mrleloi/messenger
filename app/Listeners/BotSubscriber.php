<?php

namespace App\Listeners;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Log;
use App\Events\NewMessageEvent;
use App\Facades\Messenger;
use App\Jobs\BotActionMessageHandler;

class BotSubscriber
{
    /**
     * Register the listeners for the subscriber.
     *
     * @param  Dispatcher  $events
     * @return void
     */
    public function subscribe(Dispatcher $events): void
    {
//        $events->listen(NewMessageEvent::class, [BotSubscriber::class, 'newMessage']);
    }

    /**
     * @param  NewMessageEvent  $event
     * @return void
     */
    public function newMessage(NewMessageEvent $event): void
    {
        if ($this->shouldDispatchMessageHandler($event)) {
            Messenger::getBotSubscriber('queued')
                ? BotActionMessageHandler::dispatch($event)->onQueue(Messenger::getBotSubscriber('channel'))
                : BotActionMessageHandler::dispatchSync($event);
        }
    }

    /**
     * @param  NewMessageEvent  $event
     * @return bool
     */
    private function shouldDispatchMessageHandler(NewMessageEvent $event): bool
    {
        return Messenger::getBotSubscriber('enabled')
            && $event->message->isText()
            && $event->message->notFromBot()
            && $event->thread->hasBotsFeature();
    }
}
