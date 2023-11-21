<?php

namespace App\Jobs;

use App\Actions\Messages\StoreSystemMessage;
use App\Events\ThreadSettingsEvent;
use App\Support\MessageTransformer;
use Throwable;

class ThreadNameMessage extends BaseMessengerJob
{
    /**
     * @var ThreadSettingsEvent
     */
    public ThreadSettingsEvent $event;

    /**
     * Create a new job instance.
     *
     * @param  ThreadSettingsEvent  $event
     */
    public function __construct(ThreadSettingsEvent $event)
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
        return MessageTransformer::makeGroupRenamed(
            $this->event->thread,
            $this->event->provider,
            $this->event->thread->subject
        );
    }
}
