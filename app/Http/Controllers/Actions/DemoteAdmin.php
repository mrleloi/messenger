<?php

namespace App\Http\Controllers\Actions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Actions\Threads\DemoteAdmin as DemoteAdminAction;
use App\Http\Resources\ParticipantResource;
use App\Models\Participant;
use App\Models\Thread;

class DemoteAdmin
{
    use AuthorizesRequests;

    /**
     * Demote participant from admin.
     *
     * @param  DemoteAdminAction  $demoteAdmin
     * @param  Thread  $thread
     * @param  Participant  $participant
     * @return ParticipantResource
     *
     * @throws AuthorizationException
     */
    public function __invoke(DemoteAdminAction $demoteAdmin,
                             Thread $thread,
                             Participant $participant): ParticipantResource
    {
        $this->authorize('demote', [
            $participant,
            $thread,
        ]);

        return $demoteAdmin->execute($thread, $participant)->getJsonResource();
    }
}
