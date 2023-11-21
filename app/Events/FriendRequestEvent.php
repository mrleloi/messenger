<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use App\Models\SentFriend;

class FriendRequestEvent
{
    use SerializesModels;

    /**
     * @var SentFriend
     */
    public SentFriend $friend;

    /**
     * Create a new event instance.
     *
     * @param  SentFriend  $friend
     */
    public function __construct(SentFriend $friend)
    {
        $this->friend = $friend;
    }
}
