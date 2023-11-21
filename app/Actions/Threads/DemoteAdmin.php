<?php

namespace App\Actions\Threads;

use Illuminate\Contracts\Events\Dispatcher;
use App\Broadcasting\DemotedAdminBroadcast;
use App\Contracts\BroadcastDriver;
use App\Events\DemotedAdminEvent;
use App\Http\Resources\ParticipantResource;
use App\Messenger;
use App\Models\Participant;
use App\Models\Thread;

class DemoteAdmin extends ThreadParticipantAction
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
     * DemoteAdmin constructor.
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
     * Demote the admin back to a default participant.
     *
     * @param  Thread  $thread
     * @param  Participant  $participant
     * @return $this
     */
    public function execute(Thread $thread, Participant $participant): self
    {
        $this->setThread($thread)
            ->updateParticipant(
                $participant,
                Participant::DefaultPermissions
            )
            ->generateResource()
            ->fireBroadcast()
            ->fireEvents();

        return $this;
    }

    /**
     * @return $this
     */
    private function generateResource(): self
    {
        $this->setJsonResource(new ParticipantResource(
            $this->getParticipant(),
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
                ->to($this->getParticipant())
                ->with($this->generateBroadcastResource())
                ->broadcast(DemotedAdminBroadcast::class);
        }

        return $this;
    }

    /**
     * @return void
     */
    private function fireEvents(): void
    {
        if ($this->shouldFireEvents()) {
            $this->dispatcher->dispatch(new DemotedAdminEvent(
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
