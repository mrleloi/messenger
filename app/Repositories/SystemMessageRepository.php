<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use App\Messenger;
use App\Models\Message;
use App\Models\Thread;
use App\Support\Helpers;

class SystemMessageRepository
{
    /**
     * @var Messenger
     */
    private Messenger $messenger;

    /**
     * SystemMessageRepository constructor.
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
    public function getThreadSystemMessagesIndex(Thread $thread): Collection
    {
        return $thread->logs()
            ->latest()
            ->with('owner')
            ->limit($this->messenger->getMessagesIndexCount())
            ->get();
    }

    /**
     * @param  Thread  $thread
     * @param  Message  $message
     * @return Collection
     */
    public function getThreadSystemMessagesPage(Thread $thread, Message $message): Collection
    {
        return $thread->logs()
            ->latest()
            ->with('owner')
            ->where('created_at', '<=', Helpers::precisionTime($message->created_at))
            ->where('id', '!=', $message->id)
            ->limit($this->messenger->getMessagesPageCount())
            ->get();
    }
}
