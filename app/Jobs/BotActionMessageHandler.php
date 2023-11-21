<?php

namespace App\Jobs;

use App\Actions\Bots\ProcessMessageTriggers;
use App\Events\NewMessageEvent;
use App\Exceptions\FeatureDisabledException;

class BotActionMessageHandler extends BaseMessengerJob
{
    /**
     * @var NewMessageEvent
     */
    public NewMessageEvent $event;

    /**
     * Create a new job instance.
     */
    public function __construct(NewMessageEvent $event)
    {
        $this->event = $event;
    }

    /**
     * Execute the job.
     *
     * @param  ProcessMessageTriggers  $process
     * @return void
     *
     * @throws FeatureDisabledException
     */
    public function handle(ProcessMessageTriggers $process): void
    {
        $process->execute(
            $this->event->thread,
            $this->event->message,
            $this->event->isGroupAdmin,
            $this->event->senderIp
        );
    }
}
