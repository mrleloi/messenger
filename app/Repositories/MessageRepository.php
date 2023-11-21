<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use App\Messenger;
use App\Models\Message;
use App\Models\Thread;
use App\Support\Helpers;

class MessageRepository
{
    /**
     * @var Messenger
     */
    private Messenger $messenger;

    /**
     * MessageRepository constructor.
     *
     * @param  Messenger  $messenger
     */
    public function __construct(Messenger $messenger)
    {
        $this->messenger = $messenger;
    }

    /**
     * @param  Thread  $thread
     * @return Collection
     */
    public function getThreadMessagesIndex(Thread $thread): Collection
    {
        return $thread->messages()
            ->latest()
            ->limit($this->messenger->getMessagesIndexCount())
            ->with([
                'owner',
                'reactions.owner',
            ])
            ->get();
    }

    /**
     * @param  Thread  $thread
     * @param  Message  $message
     * @return Collection
     */
    public function getThreadMessagesPage(Thread $thread, Message $message): Collection
    {
        return $thread->messages()
            ->latest()
            ->with([
                'owner',
                'reactions.owner',
            ])
            ->where('created_at', '<=', Helpers::precisionTime($message->created_at))
            ->where('id', '!=', $message->id)
            ->limit($this->messenger->getMessagesPageCount())
            ->get();
    }
}
