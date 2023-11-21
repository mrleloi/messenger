<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use App\Actions\Friends\RemoveFriend;
use App\Contracts\FriendDriver;
use App\Http\Collections\FriendCollection;
use App\Http\Resources\FriendResource;
use App\Models\Friend;
use Throwable;

class FriendController
{
    use AuthorizesRequests;

    /**
     * Display a listing of the providers friends.
     *
     * @param  FriendDriver  $repository
     * @return FriendCollection
     *
     * @throws AuthorizationException
     */
    public function index(FriendDriver $repository): FriendCollection
    {
        $this->authorize('viewAny', Friend::class);

        return new FriendCollection(
            $repository->getProviderFriends(true)
        );
    }

    /**
     * Display the friend.
     *
     * @param  Friend  $friend
     * @return FriendResource
     *
     * @throws AuthorizationException
     */
    public function show(Friend $friend): FriendResource
    {
        $this->authorize('view', $friend);

        return new FriendResource($friend);
    }

    /**
     * Remove the friend.
     *
     * @param  RemoveFriend  $removeFriend
     * @param  Friend  $friend
     * @return JsonResponse
     *
     * @throws Throwable|AuthorizationException
     */
    public function destroy(RemoveFriend $removeFriend, Friend $friend): JsonResponse
    {
        $this->authorize('delete', $friend);

        return $removeFriend->execute($friend)->getEmptyResponse();
    }
}
