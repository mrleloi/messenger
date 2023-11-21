<?php

namespace App\Jobs;

use App\Actions\Calls\CallBrokerTeardown;
use App\Events\CallEndedEvent;
use App\Exceptions\CallBrokerException;

class TeardownCall extends BaseMessengerJob
{
    /**
     * @var CallEndedEvent
     */
    public CallEndedEvent $event;

    /**
     * Create a new job instance.
     *
     * @param  CallEndedEvent  $event
     */
    public function __construct(CallEndedEvent $event)
    {
        $this->event = $event;
    }

    /**
     * Execute the job.
     *
     * @param  CallBrokerTeardown  $broker
     * @return void
     *
     * @throws CallBrokerException
     */
    public function handle(CallBrokerTeardown $broker): void
    {
        $broker->execute($this->event->call);
    }
}
