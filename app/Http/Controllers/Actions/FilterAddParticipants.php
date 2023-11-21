<?php

namespace App\Http\Controllers\Actions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Contracts\FriendDriver;
use App\Http\Collections\FriendCollection;
use App\Models\Thread;

class FilterAddParticipants
{
    use AuthorizesRequests;

    /**
     * @param  FriendDriver  $repository
     * @param  Thread  $thread
     * @return FriendCollection
     *
     * @throws AuthorizationException
     */
    public function __invoke(FriendDriver $repository, Thread $thread): FriendCollection
    {
        $this->authorize('addParticipants', $thread);

        return new FriendCollection(
            $repository->getProviderFriendsNotInThread($thread)
        );
    }
}
