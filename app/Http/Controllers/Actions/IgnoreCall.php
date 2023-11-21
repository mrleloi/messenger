<?php

namespace App\Http\Controllers\Actions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use App\Actions\Calls\IgnoreCall as IgnoreCallAction;
use App\Models\Call;
use App\Models\Thread;
use Throwable;

class IgnoreCall
{
    use AuthorizesRequests;

    /**
     * Leave the call.
     *
     * @param  IgnoreCallAction  $ignoreCall
     * @param  Thread  $thread
     * @param  Call  $call
     * @return JsonResponse
     *
     * @throws AuthorizationException|Throwable
     */
    public function __invoke(IgnoreCallAction $ignoreCall,
                             Thread $thread,
                             Call $call): JsonResponse
    {
        $this->authorize('ignore', [
            $call,
            $thread,
        ]);

        return $ignoreCall->execute($thread, $call)->getSuccessResponse();
    }
}
