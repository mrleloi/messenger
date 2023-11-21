<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use App\Actions\Threads\RemoveParticipant;
use App\Actions\Threads\StoreManyParticipants;
use App\Actions\Threads\UpdateParticipantPermissions;
use App\Http\Collections\ParticipantCollection;
use App\Http\Request\AddParticipantsRequest;
use App\Http\Request\ParticipantPermissionsRequest;
use App\Http\Resources\ParticipantResource;
use App\Models\Participant;
use App\Models\Thread;
use App\Repositories\ParticipantRepository;
use Throwable;

class ParticipantController
{
    use AuthorizesRequests;

    /**
     * Display a listing of the participants, oldest first.
     *
     * @param  ParticipantRepository  $repository
     * @param  Thread  $thread
     * @return ParticipantCollection
     *
     * @throws AuthorizationException
     */
    public function index(ParticipantRepository $repository, Thread $thread): ParticipantCollection
    {
        $this->authorize('viewAny', [
            Participant::class,
            $thread,
        ]);

        return new ParticipantCollection(
            $repository->getThreadParticipantsIndex($thread),
            $thread
        );
    }

    /**
     * Display participant history pagination.
     *
     * @param  ParticipantRepository  $repository
     * @param  Thread  $thread
     * @param  Participant  $participant
     * @return ParticipantCollection
     *
     * @throws AuthorizationException
     */
    public function paginate(ParticipantRepository $repository,
                             Thread $thread,
                             Participant $participant): ParticipantCollection
    {
        $this->authorize('viewAny', [
            Participant::class,
            $thread,
        ]);

        return new ParticipantCollection(
            $repository->getThreadParticipantsPage($thread, $participant),
            $thread,
            true,
            $participant->id
        );
    }

    /**
     * Store one or many new participants.
     *
     * @param  AddParticipantsRequest  $request
     * @param  StoreManyParticipants  $storeManyParticipants
     * @param  Thread  $thread
     * @return Collection
     *
     * @throws AuthorizationException|Throwable
     */
    public function store(AddParticipantsRequest $request,
                          StoreManyParticipants $storeManyParticipants,
                          Thread $thread): Collection
    {
        $this->authorize('create', [
            Participant::class,
            $thread,
        ]);

        return $storeManyParticipants->execute(
            $thread,
            $request->validated()['providers']
        )->getData();
    }

    /**
     * Display the participant.
     *
     * @param  Thread  $thread
     * @param  Participant  $participant
     * @return ParticipantResource
     *
     * @throws AuthorizationException
     */
    public function show(Thread $thread, Participant $participant): ParticipantResource
    {
        $this->authorize('view', [
            Participant::class,
            $thread,
        ]);

        return new ParticipantResource(
            $participant->load('owner'),
            $thread
        );
    }

    /**
     * Update the participants permissions.
     *
     * @param  ParticipantPermissionsRequest  $request
     * @param  UpdateParticipantPermissions  $permissions
     * @param  Thread  $thread
     * @param  Participant  $participant
     * @return ParticipantResource
     *
     * @throws AuthorizationException
     */
    public function update(ParticipantPermissionsRequest $request,
                           UpdateParticipantPermissions $permissions,
                           Thread $thread,
                           Participant $participant): ParticipantResource
    {
        $this->authorize('update', [
            $participant,
            $thread,
        ]);

        return $permissions->execute(
            $thread,
            $participant,
            $request->validated()
        )->getJsonResource();
    }

    /**
     * Remove the participant.
     *
     * @param  RemoveParticipant  $removeParticipant
     * @param  Thread  $thread
     * @param  Participant  $participant
     * @return JsonResponse|mixed|null
     *
     * @throws AuthorizationException|Exception
     */
    public function destroy(RemoveParticipant $removeParticipant,
                            Thread $thread,
                            Participant $participant): ?JsonResponse
    {
        $this->authorize('delete', [
            $participant,
            $thread,
        ]);

        return $removeParticipant->execute(
            $thread,
            $participant
        )->getEmptyResponse();
    }
}
