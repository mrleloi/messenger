<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use App\Actions\Threads\ArchiveThread;
use App\Http\Collections\ThreadCollection;
use App\Http\Resources\ThreadResource;
use App\Models\Thread;
use App\Repositories\ThreadRepository;

class ThreadController
{
    use AuthorizesRequests;

    /**
     * Display a listing of the most recent updated threads.
     *
     * @param  ThreadRepository  $repository
     * @return ThreadCollection
     *
     * @throws AuthorizationException
     */
    public function index(ThreadRepository $repository): ThreadCollection
    {
        $this->authorize('viewAny', Thread::class);

        return new ThreadCollection(
            $repository->getProviderThreadsIndex()
        );
    }

    /**
     * Display threads history pagination.
     *
     * @param  ThreadRepository  $repository
     * @param  Thread  $thread
     * @return ThreadCollection
     *
     * @throws AuthorizationException
     */
    public function paginate(ThreadRepository $repository, Thread $thread): ThreadCollection
    {
        $this->authorize('view', $thread);

        return new ThreadCollection(
            $repository->getProviderThreadsPage($thread),
            true,
            $thread->id
        );
    }

    /**
     * Display the thread.
     *
     * @param  Thread  $thread
     * @return ThreadResource
     *
     * @throws AuthorizationException
     */
    public function show(Thread $thread): ThreadResource
    {
        $this->authorize('view', $thread);

        return new ThreadResource($thread->load([
            'participants.owner',
            'latestMessage.owner',
            'activeCall.participants.owner',
        ]), true);
    }

    /**
     * Remove the thread.
     *
     * @param  ArchiveThread  $archiveThread
     * @param  Thread  $thread
     * @return JsonResponse
     *
     * @throws Exception|AuthorizationException
     */
    public function destroy(ArchiveThread $archiveThread, Thread $thread): JsonResponse
    {
        $this->authorize('delete', $thread);

        return $archiveThread->execute($thread)->getEmptyResponse();
    }
}
