<?php

namespace App\Actions\Threads;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\RateLimiter;
use App\Actions\BaseMessengerAction;
use App\Broadcasting\KnockBroadcast;
use App\Contracts\BroadcastDriver;
use App\Events\KnockEvent;
use App\Exceptions\FeatureDisabledException;
use App\Exceptions\KnockException;
use App\Http\Resources\Broadcast\KnockBroadcastResource;
use App\Messenger;
use App\Models\Thread;

class SendKnock extends BaseMessengerAction
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
     * SendKnock constructor.
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
     * Send a KNOCK 👊✊ to the thread!
     *
     * @param  Thread  $thread
     * @return $this
     *
     * @throws FeatureDisabledException|KnockException
     */
    public function execute(Thread $thread): self
    {
        $limiter = $thread->getKnockCacheKey(
            $this->messenger->getProvider()
        );

        $this->setThread($thread);

        $this->bailIfChecksFail($limiter);

        $this->hitLimiter($limiter);

        $this->generateResource()
            ->fireBroadcast()
            ->fireEvents();

        return $this;
    }

    /**
     * @throws FeatureDisabledException|KnockException
     */
    private function bailIfChecksFail(string $limiter): void
    {
        if (! $this->messenger->isKnockKnockEnabled()) {
            throw new FeatureDisabledException('Knocking is currently disabled.');
        }

        if ($this->messenger->getKnockTimeout() > 0
            && RateLimiter::tooManyAttempts($limiter, 1)) {
            $seconds = RateLimiter::availableIn($limiter);

            throw new KnockException("You can't knock at {$this->getThread()->name()} for another $seconds seconds.");
        }
    }

    /**
     * @param  string  $limiter
     * @return void
     */
    private function hitLimiter(string $limiter): void
    {
        if ($this->messenger->getKnockTimeout() > 0) {
            RateLimiter::hit($limiter, $this->messenger->getKnockTimeout() * 60);
        }
    }

    /**
     * @return $this
     */
    private function generateResource(): self
    {
        $this->setJsonResource(new KnockBroadcastResource(
            $this->messenger->getProvider(),
            $this->getThread()
        ));

        return $this;
    }

    /**
     * @return $this
     */
    private function fireBroadcast(): self
    {
        if ($this->shouldFireBroadcast()) {
            $this->broadcaster
                ->toOthersInThread($this->getThread())
                ->with($this->getJsonResource()->resolve())
                ->broadcast(KnockBroadcast::class);
        }

        return $this;
    }

    /**
     * @return void
     */
    private function fireEvents(): void
    {
        if ($this->shouldFireEvents()) {
            $this->dispatcher->dispatch(new KnockEvent(
                $this->messenger->getProvider(true),
                $this->getThread(true)
            ));
        }
    }
}
