<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use App\Models\PendingFriend;

class FriendDeniedEvent
{
    use SerializesModels;

    /**
     * @var PendingFriend
     */
    public PendingFriend $friend;

    /**
     * Create a new event instance.
     *
     * @param  PendingFriend  $friend
     */
    public function __construct(PendingFriend $friend)
    {
        $this->friend = $friend;
    }
}
