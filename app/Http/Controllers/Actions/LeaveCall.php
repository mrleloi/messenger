<?php

namespace App\Http\Controllers\Actions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use App\Actions\Calls\LeaveCall as LeaveCallAction;
use App\Models\Call;
use App\Models\Thread;

class LeaveCall
{
    use AuthorizesRequests;

    /**
     * Leave the call.
     *
     * @param  LeaveCallAction  $leaveCall
     * @param  Thread  $thread
     * @param  Call  $call
     * @return JsonResponse
     *
     * @throws AuthorizationException
     */
    public function __invoke(LeaveCallAction $leaveCall,
                             Thread $thread,
                             Call $call): JsonResponse
    {
        $this->authorize('leave', [
            $call,
            $thread,
        ]);

        return $leaveCall->execute(
            $call,
            $call->currentCallParticipant()
        )->getSuccessResponse();
    }
}
