<?php

namespace App\Actions\Friends;

use Exception;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\DatabaseManager;
use App\Actions\BaseMessengerAction;
use App\Broadcasting\FriendApprovedBroadcast;
use App\Contracts\BroadcastDriver;
use App\Http\Resources\Broadcast\FriendApprovedBroadcastResource;
use App\Http\Resources\FriendResource;
use App\Events\FriendApprovedEvent;
use App\Models\Friend;
use App\Models\PendingFriend;
use Throwable;

class AcceptFriendRequest extends BaseMessengerAction
{
    /**
     * @var PendingFriend
     */
    private PendingFriend $pending;

    /**
     * @var DatabaseManager
     */
    private DatabaseManager $database;

    /**
     * @var BroadcastDriver
     */
    private BroadcastDriver $broadcaster;

    /**
     * @var Dispatcher
     */
    private Dispatcher $dispatcher;

    /**
     * @var Friend
     */
    private Friend $friend;

    /**
     * @var Friend
     */
    private Friend $inverseFriend;

    /**
     * AcceptFriendRequest constructor.
     *
     * @param  DatabaseManager  $database
     * @param  BroadcastDriver  $broadcaster
     * @param  Dispatcher  $dispatcher
     */
    public function __construct(DatabaseManager $database,
                                BroadcastDriver $broadcaster,
                                Dispatcher $dispatcher)
    {
        $this->database = $database;
        $this->broadcaster = $broadcaster;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Accept the pending friend request. We will remove the pending model
     * and create two mirrored friend models to link our friendship!
     *
     * @param  PendingFriend  $pending
     * @return $this
     *
     * @throws Throwable
     */
    public function execute(PendingFriend $pending): self
    {
        $this->pending = $pending;

        $this->process()
            ->generateResource()
            ->fireBroadcast()
            ->fireEvents();

        return $this;
    }

    /**
     * @return $this
     *
     * @throws Throwable
     */
    private function process(): self
    {
        $this->isChained()
            ? $this->handle()
            : $this->database->transaction(fn () => $this->handle());

        return $this;
    }

    /**
     * Execute transactions.
     *
     * @return void
     *
     * @throws Exception
     */
    private function handle(): void
    {
        $this->storeMyFriend();

        $this->storeInverseFriend();

//        $this->destroyPending();
    }

    /**
     * Store friend relationship.
     *
     * @return void
     */
    private function storeMyFriend(): void
    {
        $friend = Friend::query()->where([
            'user1_id' => $this->pending->user1_id,
            'user1_model' => $this->pending->user1_model,
            'user2_id' => $this->pending->user2_id,
            'user2_model' => $this->pending->user2_model,
        ])->first();
        if ($friend->fill([
            'status' => 1
        ])->save()) {
            $this->friend = $friend
                ->setRelations([
                    'recipient' => $this->pending->recipient,
                    'sender' => $this->pending->sender,
                ]);
        }
    }

    /**
     * Store inverse friend relationship.
     *
     * @return void
     */
    private function storeInverseFriend(): void
    {
        $friend = Friend::query()->where([
            'user1_id' => $this->pending->user1_id,
            'user1_model' => $this->pending->user1_model,
            'user2_id' => $this->pending->user2_id,
            'user2_model' => $this->pending->user2_model,
        ])->first();
        if ($friend) {
            $this->inverseFriend = $friend->fill([
                'status' => 1
            ])->setRelations([
                'recipient' => $this->pending->recipient,
                'sender' => $this->pending->sender,
            ]);
        }
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    private function destroyPending(): void
    {
//        $this->pending->delete();
    }

    /**
     * @return $this
     */
    private function generateResource(): self
    {
        $this->setJsonResource(new FriendResource(
            $this->friend
        ));

        return $this;
    }

    /**
     * @return array
     */
    private function generateBroadcastResource(): array
    {
        return (new FriendApprovedBroadcastResource(
            $this->inverseFriend
        ))->resolve();
    }

    /**
     * @return $this
     */
    private function fireBroadcast(): self
    {
        if ($this->shouldFireBroadcast()) {
            $this->broadcaster
                ->to($this->pending->sender)
                ->with($this->generateBroadcastResource())
                ->broadcast(FriendApprovedBroadcast::class);
        }

        return $this;
    }

    /**
     * @return void
     */
    private function fireEvents(): void
    {
        if ($this->shouldFireEvents()) {
            $this->dispatcher->dispatch(new FriendApprovedEvent(
                $this->friend->withoutRelations(),
                $this->inverseFriend->withoutRelations()
            ));
        }
    }
}
