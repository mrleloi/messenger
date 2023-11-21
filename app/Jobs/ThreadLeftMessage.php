<?php

namespace App\Jobs;

use App\Actions\Messages\StoreSystemMessage;
use App\Events\ThreadLeftEvent;
use App\Support\MessageTransformer;
use Throwable;

class ThreadLeftMessage extends BaseMessengerJob
{
    /**
     * @var ThreadLeftEvent
     */
    public ThreadLeftEvent $event;

    /**
     * Create a new job instance.
     *
     * @param  ThreadLeftEvent  $event
     */
    public function __construct(ThreadLeftEvent $event)
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
        return MessageTransformer::makeGroupLeft(
            $this->event->thread,
            $this->event->provider
        );
    }
}
