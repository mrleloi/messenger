<?php

namespace App\Actions\Threads;

use Illuminate\Contracts\Events\Dispatcher;
use App\Actions\BaseMessengerAction;
use App\Broadcasting\ThreadSettingsBroadcast;
use App\Contracts\BroadcastDriver;
use App\Events\ThreadSettingsEvent;
use App\Http\Request\ThreadSettingsRequest;
use App\Http\Resources\Broadcast\ThreadSettingsBroadcastResource;
use App\Http\Resources\ThreadSettingsResource;
use App\Messenger;
use App\Models\Thread;

class UpdateGroupSettings extends BaseMessengerAction
{
    /**
     * @var Messenger
     */
    private Messenger $messenger;

    /**
     * @var BroadcastDriver
     */
    private BroadcastDriver $broadcaster;

    /**
     * @var Dispatcher
     */
    private Dispatcher $dispatcher;

    /**
     * UpdateGroupSettings constructor.
     *
     * @param  Messenger  $messenger
     * @param  BroadcastDriver  $broadcaster
     * @param  Dispatcher  $dispatcher
     */
    public function __construct(Messenger $messenger,
                                BroadcastDriver $broadcaster,
                                Dispatcher $dispatcher)
    {
        $this->broadcaster = $broadcaster;
        $this->dispatcher = $dispatcher;
        $this->messenger = $messenger;
    }

    /**
     * Update the group settings if anything changed. We push the
     * changes over the presence channel and an event for when
     * the group name changes.
     *
     * @param  Thread  $thread
     * @param  array  $params
     * @return $this
     *
     * @see ThreadSettingsRequest
     */
    public function execute(Thread $thread, array $params): self
    {
        $this->setThread($thread)
            ->updateThread($params)
            ->generateResource()
            ->fireBroadcast()
            ->fireEvents();

        return $this;
    }

    /**
     * @param  array  $attributes
     * @return $this
     */
    private function updateThread(array $attributes): self
    {
        $this->getThread()->timestamps = false;

        $this->getThread()->update($attributes);

        if (! $this->getThread()->wasChanged()) {
            $this->withoutDispatches();
        }

        return $this;
    }

    /**
     * @return $this
     */
    private function generateResource(): self
    {
        $this->setJsonResource(new ThreadSettingsResource(
            $this->getThread()
        ));

        return $this;
    }

    /**
     * @return array
     */
    private function generateBroadcastResource(): array
    {
        return (new ThreadSettingsBroadcastResource(
            $this->messenger->getProvider(),
            $this->getThread()
        ))->resolve();
    }

    /**
     * @return $this
     */
    private function fireBroadcast(): self
    {
        if ($this->shouldFireBroadcast()) {
            $this->broadcaster
                ->toPresence($this->getThread())
                ->with($this->generateBroadcastResource())
                ->broadcast(ThreadSettingsBroadcast::class);
        }

        return $this;
    }

    /**
     * @return void
     */
    private function fireEvents(): void
    {
        if ($this->shouldFireEvents()) {
            $this->dispatcher->dispatch(new ThreadSettingsEvent(
                $this->messenger->getProvider(true),
                $this->getThread(true),
                $this->getThread()->wasChanged('subject')
            ));
        }
    }
}
