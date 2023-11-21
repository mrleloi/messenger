<?php

namespace App\Listeners;

use App\Events\BroadcastFailedEvent;

class BroadcastError
{
    /**
     * Handle the event.
     *
     * @param  BroadcastFailedEvent  $event
     * @return void
     */
    public function handle(BroadcastFailedEvent $event): void
    {
        report($event->exception);
    }
}
