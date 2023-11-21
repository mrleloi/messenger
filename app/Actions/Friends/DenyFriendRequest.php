<?php

namespace App\Actions\Friends;

use Exception;
use Illuminate\Contracts\Events\Dispatcher;
use App\Actions\BaseMessengerAction;
use App\Broadcasting\FriendDeniedBroadcast;
use App\Contracts\BroadcastDriver;
use App\Events\FriendDeniedEvent;
use App\Models\PendingFriend;

class DenyFriendRequest extends BaseMessengerAction
{
    /**
     * @var Dispatcher
     */
    private Dispatcher $dispatcher;

    /**
     * @var PendingFriend
     */
    private PendingFriend $pendingFriend;

    /**
     * @var BroadcastDriver
     */
    private BroadcastDriver $broadcaster;

    /**
     * DenyFriendRequest constructor.
     *
     * @param  Dispatcher  $dispatcher
     * @param  BroadcastDriver  $broadcaster
     */
    public function __construct(Dispatcher $dispatcher, BroadcastDriver $broadcaster)
    {
        $this->dispatcher = $dispatcher;
        $this->broadcaster = $broadcaster;
    }

    /**
     * Deny and destroy the pending friend request.
     *
     * @param  PendingFriend  $pending
     * @return $this
     *
     * @throws Exception
     */
    public function execute(PendingFriend $pending): self
    {
        $this->pendingFriend = $pending;

        $this->destroyPendingFriend()
            ->fireBroadcast()
            ->fireEvents();

        return $this;
    }

    /**
     * @return $this
     *
     * @throws Exception
     */
    private function destroyPendingFriend(): self
    {
        $this->pendingFriend->delete();

        return $this;
    }

    /**
     * @return array
     */
    private function generateBroadcastResource(): array
    {
        return [
            'sent_friend_id' => $this->pendingFriend->id,
        ];
    }

    /**
     * @return $this
     */
    private function fireBroadcast(): self
    {
        if ($this->shouldFireBroadcast()) {
            $this->broadcaster
                ->to($this->pendingFriend->sender)
                ->with($this->generateBroadcastResource())
                ->broadcast(FriendDeniedBroadcast::class);
        }

        return $this;
    }

    /**
     * @return void
     */
    private function fireEvents(): void
    {
        if ($this->shouldFireEvents()) {
            $this->dispatcher->dispatch(new FriendDeniedEvent(
                $this->pendingFriend->withoutRelations()
            ));
        }
    }
}
