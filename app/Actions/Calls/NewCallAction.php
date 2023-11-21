<?php

namespace App\Actions\Calls;

use Illuminate\Contracts\Cache\Lock;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Cache;
use App\Actions\BaseMessengerAction;
use App\Broadcasting\CallStartedBroadcast;
use App\Contracts\BroadcastDriver;
use App\Events\CallStartedEvent;
use App\Exceptions\FeatureDisabledException;
use App\Exceptions\NewCallException;
use App\Http\Resources\Broadcast\NewCallBroadcastResource;
use App\Http\Resources\CallResource;
use App\Messenger;

abstract class NewCallAction extends BaseMessengerAction
{
    /**
     * @var Messenger
     */
    protected Messenger $messenger;

    /**
     * @var BroadcastDriver
     */
    protected BroadcastDriver $broadcaster;

    /**
     * @var Dispatcher
     */
    protected Dispatcher $dispatcher;

    /**
     * @var Lock
     */
    protected Lock $atomicLock;

    /**
     * NewCallAction constructor.
     *
     * @param  Messenger  $messenger
     * @param  BroadcastDriver  $broadcaster
     * @param  Dispatcher  $dispatcher
     */
    public function __construct(Messenger $messenger,
                                BroadcastDriver $broadcaster,
                                Dispatcher $dispatcher)
    {
        $this->messenger = $messenger;
        $this->broadcaster = $broadcaster;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return $this
     */
    protected function generateResource(): self
    {
        $this->setJsonResource(new CallResource(
            $this->getCall(),
            $this->getThread()
        ));

        return $this;
    }

    /**
     * @return array
     */
    protected function generateBroadcastResource(): array
    {
        return (new NewCallBroadcastResource(
            $this->messenger->getProvider(),
            $this->getCall()
        ))->resolve();
    }

    /**
     * @return $this
     */
    protected function fireBroadcast(): self
    {
        if ($this->shouldFireBroadcast()) {
            $this->broadcaster
                ->toOthersInThread($this->getThread())
                ->with($this->generateBroadcastResource())
                ->broadcast(CallStartedBroadcast::class);
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function fireEvents(): self
    {
//        \Illuminate\Support\Facades\Log::channel('dev')->info($this->getCall(true));
        if ($this->shouldFireEvents()) {
            $this->dispatcher->dispatch(new CallStartedEvent(
                $this->getCall(true),
                $this->getThread(true)
            ));
        }

        return $this;
    }

    /**
     * @throws FeatureDisabledException|NewCallException
     */
    protected function bailIfChecksFail(): void
    {
        if (! $this->acquireLock()) {
            throw new NewCallException("{$this->getThread()->name()} has a call awaiting creation.");
        }

        if (! $this->messenger->isCallingEnabled()
            || $this->messenger->isCallingTemporarilyDisabled()) {
            $this->atomicLock->release();

            throw new FeatureDisabledException('Calling is currently disabled.');
        }

        if ($this->getThread()->hasActiveCall()) {
            $this->atomicLock->release();

            throw new NewCallException("{$this->getThread()->name()} already has an active call.");
        }
    }

    /**
     * @deprecated removing in v2.
     *
     * @return $this
     */
    protected function setCallLockout(): self
    {
        return $this;
    }

    /**
     * @param  int  $type
     * @param  bool  $isSetupComplete
     * @return $this
     */
    protected function storeCall(int $type, bool $isSetupComplete): self
    {
        $this->setCall(
            $this->getThread()
                ->calls()
                ->create([
                    'type' => $type,
                    'owner_id' => $this->messenger->getProvider()->getKey(),
                    'owner_type' => $this->messenger->getProvider()->getMorphClass(),
                    'setup_complete' => $isSetupComplete,
                    'teardown_complete' => false,
                ])
                ->setRelations([
                    'owner' => $this->messenger->getProvider(),
                    'thread' => $this->getThread(),
                ])
        );

        return $this;
    }

    /**
     * @return bool
     */
    private function acquireLock(): bool
    {
        $this->atomicLock = Cache::lock(
            "call:{$this->getThread()->id}:starting",
            10
        );

        return $this->atomicLock->acquire();
    }
}
