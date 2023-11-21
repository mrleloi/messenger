<?php

namespace App\Actions\Calls;

use App\Actions\BaseMessengerAction;
use App\Contracts\MessengerProvider;
use App\Models\CallParticipant;

abstract class CallParticipantAction extends BaseMessengerAction
{
    /**
     * Store a fresh new participant.
     *
     * @param  MessengerProvider  $provider
     * @return $this
     */
    protected function storeParticipant(MessengerProvider $provider): self
    {
        $this->setCallParticipant(
            $this->getCall()
                ->participants()
                ->create([
                    'owner_id' => $provider->getKey(),
                    'owner_type' => $provider->getMorphClass(),
                ])
                ->setRelation('owner', $provider)
        );

        return $this;
    }

    /**
     * @param  CallParticipant  $participant
     * @param  array  $attributes
     * @return $this
     */
    protected function updateParticipant(CallParticipant $participant, array $attributes): self
    {
        $participant->update($attributes);

        $this->setCallParticipant($participant);

        return $this;
    }

    /**
     * Put the participant in cache so that we may tell if a participant
     * left without a proper post to the backend (left_call null).
     *
     * @param  CallParticipant  $participant
     * @return $this
     */
    protected function setParticipantInCallCache(CallParticipant $participant): self
    {
        $participant->setParticipantInCallCache();

        return $this;
    }

    /**
     * Remove the participant from cache.
     *
     * @param  CallParticipant  $participant
     * @return $this
     */
    protected function removeParticipantInCallCache(CallParticipant $participant): self
    {
        $participant->removeParticipantInCallCache();

        return $this;
    }
}
