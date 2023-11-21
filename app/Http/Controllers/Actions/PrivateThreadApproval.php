<?php

namespace App\Http\Controllers\Actions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use App\Actions\Threads\ThreadApproval;
use App\Http\Request\ThreadApprovalRequest;
use App\Models\Thread;

class PrivateThreadApproval
{
    use AuthorizesRequests;

    /**
     * @param  ThreadApprovalRequest  $request
     * @param  ThreadApproval  $threadApproval
     * @param  Thread  $thread
     * @return JsonResponse
     *
     * @throws AuthorizationException
     */
    public function __invoke(ThreadApprovalRequest $request,
                             ThreadApproval $threadApproval,
                             Thread $thread): JsonResponse
    {
        $this->authorize('approval', $thread);

        return $threadApproval->execute(
            $thread,
            $request->validated()['approve']
        )->getSuccessResponse();
    }
}
