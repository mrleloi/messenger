<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Actions\Threads\StorePrivateThread;
use App\Http\Collections\PrivateThreadCollection;
use App\Http\Request\PrivateThreadRequest;
use App\Http\Resources\ThreadResource;
use App\Models\Thread;
use App\Repositories\PrivateThreadRepository;
use Throwable;

class PrivateThreadController
{
    use AuthorizesRequests;

    /**
     * Display a listing of the most recent private threads.
     *
     * @param  PrivateThreadRepository  $repository
     * @return PrivateThreadCollection
     *
     * @throws AuthorizationException
     */
    public function index(PrivateThreadRepository $repository): PrivateThreadCollection
    {
        $this->authorize('viewAny', Thread::class);

        return new PrivateThreadCollection(
            $repository->getProviderPrivateThreadsIndex()
        );
    }

    /**
     * Display private threads history pagination.
     *
     * @param  PrivateThreadRepository  $repository
     * @param  Thread  $private
     * @return PrivateThreadCollection
     *
     * @throws AuthorizationException
     */
    public function paginate(PrivateThreadRepository $repository, Thread $private): PrivateThreadCollection
    {
        $this->authorize('privateMethod', $private);

        return new PrivateThreadCollection(
            $repository->getProviderPrivateThreadsPage($private),
            true,
            $private->id
        );
    }

    /**
     * Store a new private thread.
     *
     * @param  PrivateThreadRequest  $request
     * @param  StorePrivateThread  $storePrivateThread
     * @return ThreadResource
     *
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function store(PrivateThreadRequest $request, StorePrivateThread $storePrivateThread): ThreadResource
    {
        $this->authorize('create', Thread::class);

        return $storePrivateThread->execute(
            $request->validated()
        )->getJsonResource();
    }
}
