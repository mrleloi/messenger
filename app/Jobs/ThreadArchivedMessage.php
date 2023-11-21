<?php

namespace App\Jobs;

use App\Actions\Messages\StoreSystemMessage;
use App\Events\ThreadArchivedEvent;
use App\Support\MessageTransformer;
use Throwable;

class ThreadArchivedMessage extends BaseMessengerJob
{
    /**
     * @var ThreadArchivedEvent
     */
    public ThreadArchivedEvent $event;

    /**
     * Create a new job instance.
     *
     * @param  ThreadArchivedEvent  $event
     */
    public function __construct(ThreadArchivedEvent $event)
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
        $message->withoutBroadcast()->execute(...$this->systemMessage());
    }

    /**
     * @return array
     */
    private function systemMessage(): array
    {
        return MessageTransformer::makeThreadArchived(
            $this->event->thread,
            $this->event->provider
        );
    }
}
