<?php

namespace App\Brokers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Contracts\FriendDriver;
use App\Messenger;
use App\Models\Friend;
use App\Models\PendingFriend;
use App\Models\SentFriend;
use App\Models\Thread;
use App\Support\Helpers;

class FriendBroker implements FriendDriver
{
    /**
     * @var Messenger
     */
    protected Messenger $messenger;

    /**
     * @var Collection|null
     */
    protected ?Collection $friends = null;

    /**
     * @var Collection|null
     */
    protected ?Collection $pendingFriends = null;

    /**
     * @var Collection|null
     */
    protected ?Collection $sentFriends = null;

    /**
     * FriendBroker constructor.
     *
     * @param  Messenger  $messenger
     */
    public function __construct(Messenger $messenger)
    {
        $this->messenger = $messenger;
    }

    /**
     * @inheritDoc
     */
    public function getProviderFriends(bool $withRelations = false)
    {
        if (! $this->messenger->providerHasFriends()) {
            return $this->sendEmptyCollection();
        }

        return is_null($this->friends)
            ? $this->friends = $this->getProviderFriendsBuilder()
//                ->with($withRelations ? 'party' : [])
                ->get()
            : $this->friends;
    }

    /**
     * @inheritDoc
     */
    public function getProviderPendingFriends(bool $withRelations = false)
    {
        if (! $this->messenger->providerHasFriends()) {
            return $this->sendEmptyCollection();
        }

        return is_null($this->pendingFriends)
            ? $this->pendingFriends = $this->getProviderPendingFriendsBuilder()
                ->latest()
                ->get()
            : $this->pendingFriends;
    }

    /**
     * @inheritDoc
     */
    public function getProviderSentFriends(bool $withRelations = false)
    {
        if (! $this->messenger->providerHasFriends()) {
            return $this->sendEmptyCollection();
        }

        return is_null($this->sentFriends)
            ? $this->sentFriends = $this->getProviderSentFriendsBuilder()
                ->latest()
                ->get()
            : $this->sentFriends;
    }

    /**
     * @inheritDoc
     */
    public function isFriend($provider = null): bool
    {
        $providerCurrentUser = $this->messenger->getProvider();
        return $this->messenger->isValidMessengerProvider($provider)
            && Friend::query()
            ->where("status", Friend::$STATUS_ACCEPTED)
            ->where(function (Builder $query) use ($provider, $providerCurrentUser) {
                $query
                    ->where("user1_id", '=', $provider->getKey())
                    ->where("user1_model", '=', $provider->getMorphClass())
                    ->whereIn('user2_model', $this->messenger->getAllFriendableProviders());
                $provider = $providerCurrentUser;
                $query
                    ->where("user2_id", '=', $provider->getKey())
                    ->where("user2_model", '=', $provider->getMorphClass());
            })
            ->orWhere(function (Builder $query) use ($provider, $providerCurrentUser) {
                $query
                    ->where("user2_id", '=', $provider->getKey())
                    ->where("user2_model", '=', $provider->getMorphClass())
                    ->whereIn('user1_model', $this->messenger->getAllFriendableProviders());
                $provider = $providerCurrentUser;
                $query
                    ->where("user1_id", '=', $provider->getKey())
                    ->where("user1_model", '=', $provider->getMorphClass());
            })->first();
    }

    /**
     * @inheritDoc
     */
    public function isSentFriendRequest($provider = null): bool
    {
        $morph = 'user2';
        return $this->messenger->isValidMessengerProvider($provider)
            && $this->getProviderSentFriends()
                ->where("{$morph}_id", '=', $provider->getKey())
                ->where("{$morph}_model", '=', $provider->getMorphClass())
                ->first();
    }

    /**
     * @inheritDoc
     */
    public function isPendingFriendRequest($provider = null): bool
    {
        return $this->messenger->isValidMessengerProvider($provider)
            && Helpers::forProviderInCollection(
                $this->getProviderPendingFriends(),
                $provider,
                'sender'
            )->first();
    }

    /**
     * @inheritDoc
     */
    public function friendStatus($provider = null): int
    {
        if ($this->isFriend($provider)) {
            return self::FRIEND;
        }

        if ($this->isSentFriendRequest($provider)) {
            return self::SENT_FRIEND_REQUEST;
        }

        if ($this->isPendingFriendRequest($provider)) {
            return self::PENDING_FRIEND_REQUEST;
        }

        return self::NOT_FRIEND;
    }

    /**
     * @inheritDoc
     */
    public function getFriendResource(int $friendStatus, $provider = null)
    {
        switch ($friendStatus) {
            case self::FRIEND:
                return $this->getFriend($provider);
            case self::SENT_FRIEND_REQUEST:
                return $this->getSentFriend($provider);
            case self::PENDING_FRIEND_REQUEST:
                return $this->getPendingFriend($provider);
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getProviderFriendsNotInThread(Thread $thread)
    {
        if (! $this->messenger->providerHasFriends()) {
            return $this->sendEmptyCollection();
        }

        $participants = $thread->participants()->get();

        return $this->getProviderFriendsBuilder()
            ->get()
            ->reject(function (Friend $friend) use ($participants) {
                return $participants->where('owner_id', '=', $friend->party_id)
                    ->where('owner_type', '=', $friend->party_type)
                    ->first();
            })
            ->load('party');
    }

    /**
     * @return Friend|Builder
     */
    private function getProviderFriendsBuilder(): Builder
    {
        $provider = $this->messenger->getProvider();
        return Friend::query()
            ->where("status", Friend::$STATUS_ACCEPTED)
            ->where(function (Builder $query) use ($provider) {
                $query
                    ->where("user1_id", '=', $provider->getKey())
                    ->where("user1_model", '=', $provider->getMorphClass())
                    ->whereIn('user2_model', $this->messenger->getAllFriendableProviders());
            })
            ->orWhere(function (Builder $query) use ($provider) {
                $query
                    ->where("user2_id", '=', $provider->getKey())
                    ->where("user2_model", '=', $provider->getMorphClass())
                    ->whereIn('user1_model', $this->messenger->getAllFriendableProviders());
            });
    }

    /**
     * @return PendingFriend|Builder
     */
    private function getProviderPendingFriendsBuilder(): Builder
    {
        $provider = $this->messenger->getProvider();
        return PendingFriend::query()
            ->where('status', '=', Friend::$STATUS_PENDING)
            ->where("user2_id", '=', $provider->getKey())
            ->where("user2_model", '=', $provider->getMorphClass())
            ->whereIn('user1_model', $this->messenger->getAllFriendableProviders());
    }

    /**
     * @return SentFriend|Builder
     */
    private function getProviderSentFriendsBuilder(): Builder
    {
        $provider = $this->messenger->getProvider();
        return SentFriend::query()
            ->where('status', '=', Friend::$STATUS_PENDING)
            ->where("user1_id", '=', $provider->getKey())
            ->where("user1_model", '=', $provider->getMorphClass())
            ->whereIn('user2_model', $this->messenger->getAllFriendableProviders());
    }

    /**
     * @param $model
     * @return Friend|null
     */
    private function getFriend($model): ?Friend
    {
        $provider = $this->messenger->getProvider();
        return $this->getProviderFriends()
            ->where("user1_id", '=', $provider->getKey())
            ->where("user1_model", '=', $provider->getMorphClass())
            ->first();
    }

    /**
     * @param $model
     * @return SentFriend|null
     */
    private function getSentFriend($model): ?SentFriend
    {
        $provider = $this->messenger->getProvider();
        return $this->getProviderSentFriends()
            ->where("user1_id", '=', $provider->getKey())
            ->where("user1_model", '=', $provider->getMorphClass())
            ->first();
    }

    /**
     * @param $model
     * @return PendingFriend|null
     */
    private function getPendingFriend($model): ?PendingFriend
    {
        $provider = $this->messenger->getProvider();
        return $this->getProviderPendingFriends()
            ->where("user1_id", '=', $provider->getKey())
            ->where("user1_model", '=', $provider->getMorphClass())
            ->first();
    }

    /**
     * @return Collection
     */
    private function sendEmptyCollection(): Collection
    {
        return Collection::make();
    }
}
