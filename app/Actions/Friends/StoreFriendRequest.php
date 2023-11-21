<?php

namespace App\Actions\Friends;

use App\Models\Friend;
use Illuminate\Contracts\Events\Dispatcher;
use App\Actions\BaseMessengerAction;
use App\Broadcasting\FriendRequestBroadcast;
use App\Contracts\BroadcastDriver;
use App\Contracts\FriendDriver;
use App\Contracts\MessengerProvider;
use App\Events\FriendRequestEvent;
use App\Exceptions\FriendException;
use App\Exceptions\ProviderNotFoundException;
use App\Http\Request\FriendRequest;
use App\Http\Resources\Broadcast\FriendRequestBroadcastResource;
use App\Http\Resources\SentFriendResource;
use App\Messenger;
use App\Models\SentFriend;
use App\Repositories\ProvidersRepository;

class StoreFriendRequest extends BaseMessengerAction
{
    /**
     * @var Messenger
     */
    private Messenger $messenger;

    /**
     * @var ProvidersRepository
     */
    private ProvidersRepository $providersRepository;

    /**
     * @var BroadcastDriver
     */
    private BroadcastDriver $broadcaster;

    /**
     * @var MessengerProvider|null
     */
    private ?MessengerProvider $recipient;

    /**
     * @var Dispatcher
     */
    private Dispatcher $dispatcher;

    /**
     * @var FriendDriver
     */
    private FriendDriver $friends;

    /**
     * @var SentFriend
     */
    private SentFriend $sentFriend;

    /**
     * StoreFriendRequest constructor.
     *
     * @param  Messenger  $messenger
     * @param  ProvidersRepository  $providersRepository
     * @param  BroadcastDriver  $broadcaster
     * @param  Dispatcher  $dispatcher
     * @param  FriendDriver  $friends
     */
    public function __construct(Messenger $messenger,
                                ProvidersRepository $providersRepository,
                                BroadcastDriver $broadcaster,
                                Dispatcher $dispatcher,
                                FriendDriver $friends)
    {
        $this->messenger = $messenger;
        $this->providersRepository = $providersRepository;
        $this->broadcaster = $broadcaster;
        $this->dispatcher = $dispatcher;
        $this->friends = $friends;
    }

    /**
     * Store our new sent friend request and notify the recipient!
     *
     * @param  array  $params
     * @return $this
     *
     * @see FriendRequest
     *
     * @throws FriendException|ProviderNotFoundException
     */
    public function execute(array $params): self
    {
        $this->locateAndSetRecipientProvider(
            $params['recipient_alias'],
            $params['recipient_id']
        );

        $this->bailIfChecksFail();

        $this->storeSentFriendRequest()
            ->generateResource()
            ->fireBroadcast()
            ->fireEvents();

        return $this;
    }

    /**
     * @param  string  $alias
     * @param  string  $id
     */
    private function locateAndSetRecipientProvider(string $alias, string $id): void
    {
        $this->recipient = $this->providersRepository->getProviderUsingAliasAndId($alias, $id);
    }

    /**
     * @throws FriendException|ProviderNotFoundException
     * @noinspection PhpParamsInspection
     */
    private function bailIfChecksFail(): void
    {
        if (is_null($this->recipient)) {
            throw new ProviderNotFoundException;
        }

        if ($this->messenger->getProvider()->is($this->recipient)) {
            throw new FriendException('Cannot friend yourself.');
        }

        if (! $this->messenger->canFriendProvider($this->recipient)) {
            throw new FriendException('Not authorized to add friend.');
        }

        if ($this->friends->friendStatus($this->recipient) !== FriendDriver::NOT_FRIEND) {
            throw new FriendException("You are already friends, or have a pending request with {$this->recipient->getProviderName()}.");
        }
    }

    /**
     * @return $this
     */
    private function storeSentFriendRequest(): self
    {
        $friend = new SentFriend();
        if ($friend->fill([
            'user1_id' => $this->messenger->getProvider()->getKey(),
            'user1_model' => $this->messenger->getProvider()->getMorphClass(),
            'user2_id' => $this->recipient->getKey(),
            'user2_model' => $this->recipient->getMorphClass(),
        ])->save()) {
            $this->sentFriend = $friend->setRelations([
                'recipient' => $this->recipient,
                'sender' => $this->messenger->getProvider(),
            ]);
        }

        return $this;
    }

    /**
     * @return $this
     */
    private function generateResource(): self
    {
        $this->setJsonResource(new SentFriendResource(
            $this->sentFriend
        ));

        return $this;
    }

    /**
     * @return array
     */
    private function generateBroadcastResource(): array
    {
        return (new FriendRequestBroadcastResource(
            $this->sentFriend
        ))->resolve();
    }

    /**
     * @return $this
     */
    private function fireBroadcast(): self
    {
        if ($this->shouldFireBroadcast()) {
            $this->broadcaster
                ->to($this->recipient)
                ->with($this->generateBroadcastResource())
                ->broadcast(FriendRequestBroadcast::class);
        }

        return $this;
    }

    /**
     * @return void
     */
    private function fireEvents(): void
    {
        if ($this->shouldFireEvents()) {
            $this->dispatcher->dispatch(new FriendRequestEvent(
                $this->sentFriend->withoutRelations()
            ));
        }
    }
}
