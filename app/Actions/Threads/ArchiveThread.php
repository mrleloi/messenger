<?php

namespace App\Actions\Threads;

use Exception;
use Illuminate\Contracts\Events\Dispatcher;
use App\Actions\BaseMessengerAction;
use App\Broadcasting\ThreadArchivedBroadcast;
use App\Contracts\BroadcastDriver;
use App\Events\ThreadArchivedEvent;
use App\Messenger;
use App\Models\Thread;

class ArchiveThread extends BaseMessengerAction
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
     * ArchiveThread constructor.
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
     * Archive the thread.
     *
     * @param  Thread  $thread
     * @return $this
     *
     * @throws Exception
     */
    public function execute(Thread $thread): self
    {
        $this->setThread($thread)
            ->archiveThread()
            ->fireBroadcast()
            ->fireEvents();

        return $this;
    }

    /**
     * @return $this
     *
     * @throws Exception
     */
    private function archiveThread(): self
    {
        $this->getThread()->delete();

        return $this;
    }

    /**
     * @return $this
     */
    private function fireBroadcast(): self
    {
        if ($this->shouldFireBroadcast()) {
            $this->broadcaster
                ->toAllInThread($this->getThread())
                ->with($this->generateBroadcastResource())
                ->broadcast(ThreadArchivedBroadcast::class);
        }

        return $this;
    }

    /**
     * @return void
     */
    private function fireEvents(): void
    {
        if ($this->shouldFireEvents()) {
            $this->dispatcher->dispatch(new ThreadArchivedEvent(
                $this->messenger->getProvider(true),
                $this->getThread(true)
            ));
        }
    }

    /**
     * @return array
     */
    private function generateBroadcastResource(): array
    {
        return [
            'thread_id' => $this->getThread()->id,
        ];
    }
}
