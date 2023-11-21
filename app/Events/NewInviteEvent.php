<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use App\Models\Invite;

class NewInviteEvent
{
    use SerializesModels;

    /**
     * @var Invite
     */
    public Invite $invite;

    /**
     * Create a new event instance.
     *
     * @param  Invite  $invite
     */
    public function __construct(Invite $invite)
    {
        $this->invite = $invite;
    }
}
