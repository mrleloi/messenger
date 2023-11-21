<?php

namespace App\Jobs;

use App\Actions\Calls\CallBrokerSetup;
use App\Jobs\BaseMessengerJob;
use App\Actions\Messages\SetupNewMessageAction;
use App\Events\NewMessageEvent;

class SetupNewMessage extends BaseMessengerJob
{
    /**
     * @var NewMessageEvent
     */
    public NewMessageEvent $event;

    /**
     * Create a new job instance.
     *
     * @param  NewMessageEvent  $event
     */
    public function __construct(NewMessageEvent $event)
    {
        $this->event = $event;
    }

    /**
     * Execute the job.
     *
     * @param  SetupNewMessageAction  $broker
     * @return void
     *
     */
    public function handle(SetupNewMessageAction $broker): void
    {
        $broker->execute($this->event);
    }
}
