<?php

namespace App\Jobs;

use App\Actions\Messages\StoreSystemMessage;
use App\Events\PackagedBotInstalledEvent;
use App\Support\MessageTransformer;
use Throwable;

class BotInstalledMessage extends BaseMessengerJob
{
    /**
     * @var PackagedBotInstalledEvent
     */
    public PackagedBotInstalledEvent $event;

    /**
     * Create a new job instance.
     *
     * @param  PackagedBotInstalledEvent  $event
     */
    public function __construct(PackagedBotInstalledEvent $event)
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
        return MessageTransformer::makeBotPackageInstalled(
            $this->event->thread,
            $this->event->provider,
            $this->event->packagedBot->name
        );
    }
}
