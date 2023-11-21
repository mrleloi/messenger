<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use App\Contracts\MessengerProvider;
use App\Messenger;
use App\Models\Thread;
use App\Support\Helpers;

class PrivateThreadRepository
{
    /**
     * @var Messenger
     */
    private Messenger $messenger;

    /**
     * PrivateThreadRepository constructor.
     *
     * @param  Messenger  $messenger
     */
    public function __construct(Messenger $messenger)
    {
        $this->messenger = $messenger;
    }

    /**
     * @param  MessengerProvider|null  $recipient
     * @return Thread|null
     */
    public function getProviderPrivateThreadWithRecipient(MessengerProvider $recipient = null): ?Thread
    {
        if ($this->messenger->isValidMessengerProvider($recipient)) {
            return Thread::hasProvider($this->messenger->getProvider())
                ->join('participants as recipients', 'recipients.thread_id', '=', 'threads.id')
                ->where('recipients.owner_id', '=', $recipient->getKey())
                ->where('recipients.owner_type', '=', $recipient->getMorphClass())
                ->whereNull('recipients.deleted_at')
                ->private()
                ->first();
        }

        return null;
    }

    /**
     * @return Collection
     */
    public function getProviderPrivateThreadsIndex(): Collection
    {
        return Thread::hasProvider($this->messenger->getProvider())
            ->private()
            ->latest('updated_at')
            ->with([
                'participants.owner',
                'latestMessage.owner',
                'activeCall.participants.owner',
            ])
            ->limit($this->messenger->getThreadsIndexCount())
            ->get();
    }

    /**
     * @param  Thread  $thread
     * @return Collection
     */
    public function getProviderPrivateThreadsPage(Thread $thread): Collection
    {
        return Thread::hasProvider($this->messenger->getProvider())
            ->private()
            ->latest('updated_at')
            ->with([
                'participants.owner',
                'latestMessage.owner',
                'activeCall.participants.owner',
            ])
            ->where('threads.updated_at', '<=', Helpers::precisionTime($thread->updated_at))
            ->where('threads.id', '!=', $thread->id)
            ->limit($this->messenger->getThreadsPageCount())
            ->get();
    }

    /**
     * @return Thread|null
     */
    public function getProviderOldestPrivateThread(): ?Thread
    {
        return Thread::hasProvider($this->messenger->getProvider())
            ->private()
            ->oldest('updated_at')
            ->first();
    }
}
