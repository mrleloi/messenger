<?php

namespace App\Http\Controllers\Actions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use App\Actions\Threads\UnmuteThread as UnmuteThreadAction;
use App\Models\Thread;

class UnmuteThread
{
    use AuthorizesRequests;

    /**
     * Un-Mute the thread.
     *
     * @param  UnmuteThreadAction  $unmuteThread
     * @param  Thread  $thread
     * @return JsonResponse
     *
     * @throws AuthorizationException
     */
    public function __invoke(UnmuteThreadAction $unmuteThread, Thread $thread): JsonResponse
    {
        $this->authorize('mutes', $thread);

        return $unmuteThread->execute($thread)->getSuccessResponse();
    }
}
