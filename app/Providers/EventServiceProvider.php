<?php

namespace App\Providers;

use App\Listeners\BotHandlerError;
use App\Listeners\BroadcastError;
use App\Listeners\MessageSubscriber;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\BotActionFailedEvent;
use App\Events\BroadcastFailedEvent;
use App\Events\NewMessageEvent;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        BotActionFailedEvent::class => [
            BotHandlerError::class,
        ],
        BroadcastFailedEvent::class => [
            BroadcastError::class,
        ],
    ];

    public function boot(): void
    {
        Event::listen(NewMessageEvent::class, [MessageSubscriber::class, 'newMessage']);
    }
}
