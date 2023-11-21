<?php

namespace App\Http\Controllers\Actions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use App\Actions\Threads\MarkParticipantRead;
use App\Models\Thread;

class MarkThreadRead
{
    use AuthorizesRequests;

    /**
     * Mark thread read for current participant.
     *
     * @param  MarkParticipantRead  $markParticipantRead
     * @param  Thread  $thread
     * @return JsonResponse
     *
     * @throws AuthorizationException
     */
    public function __invoke(MarkParticipantRead $markParticipantRead, Thread $thread): JsonResponse
    {
        $this->authorize('view', $thread);

        return $markParticipantRead->execute(
            $thread->currentParticipant(),
            $thread
        )->getSuccessResponse();
    }
}
