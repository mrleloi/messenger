<?php

namespace App\Http\Controllers;

use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use App\Contracts\FriendDriver;
use App\Exceptions\FriendException;
use App\Http\Collections\SentFriendCollection;
use App\Http\Request\FriendRequest;
use App\Http\Resources\SentFriendResource;
use App\Actions\Friends\CancelFriendRequest;
use App\Actions\Friends\StoreFriendRequest;
use App\Models\SentFriend;

class SentFriendController
{
    use AuthorizesRequests;

    /**
     * Display a listing of the providers sent friend request.
     *
     * @param  FriendDriver  $repository
     * @return SentFriendCollection
     *
     * @throws AuthorizationException
     */
    public function index(FriendDriver $repository): SentFriendCollection
    {
        $this->authorize('viewAny', SentFriend::class);

        return new SentFriendCollection(
            $repository->getProviderSentFriends(true)
        );
    }

    /**
     * Store a new friend request.
     *
     * @param  FriendRequest  $request
     * @param  StoreFriendRequest  $storeFriendRequest
     * @return SentFriendResource
     *
     * @throws AuthorizationException|NotFoundHttpException|FriendException
     */
    public function store(FriendRequest $request, StoreFriendRequest $storeFriendRequest): SentFriendResource
    {
        $this->authorize('create', SentFriend::class);

        return $storeFriendRequest->execute(
            $request->validated()
        )->getJsonResource();
    }

    /**
     * Display the sent friend request.
     *
     * @param  SentFriend  $sent
     * @return SentFriendResource
     *
     * @throws AuthorizationException
     */
    public function show(SentFriend $sent): SentFriendResource
    {
        $this->authorize('view', $sent);

        return new SentFriendResource($sent);
    }

    /**
     * Cancel the sent friend request.
     *
     * @param  CancelFriendRequest  $cancelFriendRequest
     * @param  SentFriend  $sent
     * @return JsonResponse
     *
     * @throws Exception|AuthorizationException
     */
    public function destroy(CancelFriendRequest $cancelFriendRequest, SentFriend $sent): JsonResponse
    {
        $this->authorize('delete', $sent);

        return $cancelFriendRequest->execute($sent)->getEmptyResponse();
    }
}
