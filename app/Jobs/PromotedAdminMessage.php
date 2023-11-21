<?php

namespace App\Jobs;

use App\Actions\Messages\StoreSystemMessage;
use App\Events\PromotedAdminEvent;
use App\Support\MessageTransformer;
use Throwable;

class PromotedAdminMessage extends BaseMessengerJob
{
    /**
     * @var PromotedAdminEvent
     */
    public PromotedAdminEvent $event;

    /**
     * Create a new job instance.
     *
     * @param  PromotedAdminEvent  $event
     */
    public function __construct(PromotedAdminEvent $event)
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
        return MessageTransformer::makeParticipantPromoted(
            $this->event->thread,
            $this->event->provider,
            $this->event->participant
        );
    }
}
