<?php

namespace App\Jobs;

use App\Actions\Calls\CallBrokerSetup;
use App\Events\CallStartedEvent;
use App\Exceptions\CallBrokerException;

class SetupCall extends BaseMessengerJob
{
    /**
     * @var CallStartedEvent
     */
    public CallStartedEvent $event;

    /**
     * Create a new job instance.
     *
     * @param  CallStartedEvent  $event
     */
    public function __construct(CallStartedEvent $event)
    {
        $this->event = $event;
    }

    /**
     * Execute the job.
     *
     * @param  CallBrokerSetup  $broker
     * @return void
     *
     * @throws CallBrokerException
     */
    public function handle(CallBrokerSetup $broker): void
    {
        $broker->execute(
            $this->event->thread,
            $this->event->call
        );
    }
}
