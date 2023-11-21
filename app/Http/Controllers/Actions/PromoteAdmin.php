<?php

namespace App\Http\Controllers\Actions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Actions\Threads\PromoteAdmin as PromoteAdminAction;
use App\Http\Resources\ParticipantResource;
use App\Models\Participant;
use App\Models\Thread;

class PromoteAdmin
{
    use AuthorizesRequests;

    /**
     * Promote participant to admin.
     *
     * @param  PromoteAdminAction  $promoteAdmin
     * @param  Thread  $thread
     * @param  Participant  $participant
     * @return ParticipantResource
     *
     * @throws AuthorizationException
     */
    public function __invoke(PromoteAdminAction $promoteAdmin,
                             Thread $thread,
                             Participant $participant): ParticipantResource
    {
        $this->authorize('promote', [
            $participant,
            $thread,
        ]);

        return $promoteAdmin->execute($thread, $participant)->getJsonResource();
    }
}
