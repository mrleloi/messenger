<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use App\Messenger;
use App\Models\Message;
use App\Models\Thread;
use App\Support\Helpers;

class AudioMessageRepository
{
    /**
     * @var Messenger
     */
    private Messenger $messenger;

    /**
     * AudioMessageRepository constructor.
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
    public function getThreadAudioIndex(Thread $thread): Collection
    {
        return $thread->audio()
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
    public function getThreadAudioPage(Thread $thread, Message $message): Collection
    {
        return $thread->audio()
            ->latest()
            ->with('owner')
            ->where('created_at', '<=', Helpers::precisionTime($message->created_at))
            ->where('id', '!=', $message->id)
            ->limit($this->messenger->getMessagesPageCount())
            ->get();
    }
}
