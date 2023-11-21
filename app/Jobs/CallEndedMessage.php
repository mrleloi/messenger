<?php

namespace App\Jobs;

use App\Actions\Messages\StoreSystemMessage;
use App\Events\CallEndedEvent;
use App\Support\MessageTransformer;
use Throwable;

class CallEndedMessage extends BaseMessengerJob
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
     * @param  StoreSystemMessage  $message
     * @return void
     *
     * @throws Throwable
     */
    public function handle(StoreSystemMessage $message): void
    {
        $message->execute(...$this->systemMessage());
    }

    /**
     * @return array
     */
    private function systemMessage(): array
    {
        return MessageTransformer::makeVideoCall(
            $this->event->call->thread,
            $this->event->call->owner,
            $this->event->call
        );
    }
}
