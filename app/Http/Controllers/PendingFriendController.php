<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use App\Actions\Friends\AcceptFriendRequest;
use App\Actions\Friends\DenyFriendRequest;
use App\Contracts\FriendDriver;
use App\Http\Collections\PendingFriendCollection;
use App\Http\Resources\FriendResource;
use App\Http\Resources\PendingFriendResource;
use App\Models\PendingFriend;
use Throwable;

class PendingFriendController
{
    use AuthorizesRequests;

    /**
     * Display a listing of the providers pending friends.
     *
     * @param  FriendDriver  $repository
     * @return PendingFriendCollection
     *
     * @throws AuthorizationException
     */
    public function index(FriendDriver $repository): PendingFriendCollection
    {
        $this->authorize('viewAny', PendingFriend::class);

        $res=$repository->getProviderPendingFriends(true);
        return new PendingFriendCollection(
            $repository->getProviderPendingFriends(true)
        );
    }

    /**
     * Display the pending friend.
     *
     * @param  PendingFriend  $pending
     * @return PendingFriendResource
     *
     * @throws AuthorizationException
     */
    public function show(PendingFriend $pending): PendingFriendResource
    {
        $this->authorize('view', $pending);

        return new PendingFriendResource($pending);
    }

    /**
     * Accept the pending friend request.
     *
     * @param  AcceptFriendRequest  $acceptFriendRequest
     * @param  PendingFriend  $pending
     * @return FriendResource
     *
     * @throws Throwable|AuthorizationException
     */
    public function update(AcceptFriendRequest $acceptFriendRequest, PendingFriend $pending): FriendResource
    {
        $this->authorize('update', $pending);

        return $acceptFriendRequest->execute($pending)
            ->getJsonResource();
    }

    /**
     * Deny the pending friend request.
     *
     * @param  DenyFriendRequest  $denyFriendRequest
     * @param  PendingFriend  $pending
     * @return JsonResponse
     *
     * @throws Exception|AuthorizationException
     */
    public function destroy(DenyFriendRequest $denyFriendRequest, PendingFriend $pending): JsonResponse
    {
        $this->authorize('delete', $pending);

        return $denyFriendRequest->execute($pending)->getEmptyResponse();
    }
}
