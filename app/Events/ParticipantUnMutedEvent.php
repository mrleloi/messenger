<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use App\Models\Participant;

class ParticipantUnMutedEvent
{
    use SerializesModels;

    /**
     * @var Participant
     */
    public Participant $participant;

    /**
     * Create a new event instance.
     *
     * @param  Participant  $participant
     */
    public function __construct(Participant $participant)
    {
        $this->participant = $participant;
    }
}
