<?php

namespace App\Jobs;

use App\Actions\Calls\CallBrokerSetup;
use App\Actions\Messages\SetupNewSystemMessageAction;
use App\Jobs\BaseMessengerJob;
use App\Actions\Messages\SetupNewMessageAction;
use App\Events\NewSystemMessageEvent;

class SetupNewSystemMessage extends BaseMessengerJob
{
    /**
     * @var NewSystemMessageEvent
     */
    public NewSystemMessageEvent $event;

    /**
     * Create a new job instance.
     *
     * @param  NewSystemMessageEvent  $event
     */
    public function __construct(NewSystemMessageEvent $event)
    {
        $this->event = $event;
    }

    /**
     * Execute the job.
     *
     * @param  SetupNewSystemMessageAction  $broker
     * @return void
     *
     */
    public function handle(SetupNewSystemMessageAction $broker): void
    {
        $broker->execute($this->event);
    }
}
