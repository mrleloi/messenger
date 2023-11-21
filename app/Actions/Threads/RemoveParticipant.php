<?php

namespace App\Actions\Threads;

use Exception;
use Illuminate\Contracts\Events\Dispatcher;
use App\Actions\BaseMessengerAction;
use App\Broadcasting\ThreadLeftBroadcast;
use App\Contracts\BroadcastDriver;
use App\Events\RemovedFromThreadEvent;
use App\Messenger;
use App\Models\Participant;
use App\Models\Thread;

class RemoveParticipant extends BaseMessengerAction
{
    /**
     * @var BroadcastDriver
     */
    private BroadcastDriver $broadcaster;

    /**
     * @var Dispatcher
     */
    private Dispatcher $dispatcher;

    /**
     * @var Messenger
     */
    private Messenger $messenger;

    /**
     * RemoveParticipant constructor.
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
     * Remove the participant from the group.
     *
     * @param  Thread  $thread
     * @param  Participant  $participant
     * @return $this
     *
     * @throws Exception
     */
    public function execute(Thread $thread, Participant $participant): self
    {
        $this->setThread($thread)
            ->setParticipant($participant)
            ->removeParticipant()
            ->fireBroadcast()
            ->fireEvents();

        return $this;
    }

    /**
     * @return $this
     *
     * @throws Exception
     */
    private function removeParticipant(): self
    {
        $this->getParticipant()->delete();

        return $this;
    }

    /**
     * @return $this
     */
    private function fireBroadcast(): self
    {
        if ($this->shouldFireBroadcast()) {
            $this->broadcaster
                ->to($this->getParticipant())
                ->with($this->generateBroadcastResource())
                ->broadcast(ThreadLeftBroadcast::class);
        }

        return $this;
    }

    /**
     * @return void
     */
    private function fireEvents(): void
    {
        if ($this->shouldFireEvents()) {
            $this->dispatcher->dispatch(new RemovedFromThreadEvent(
                $this->messenger->getProvider(true),
                $this->getThread(true),
                $this->getParticipant(true)
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
