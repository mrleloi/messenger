<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Actions\Messages\StoreAudioMessage;
use App\Exceptions\FileServiceException;
use App\Http\Collections\AudioMessageCollection;
use App\Http\Request\AudioMessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use App\Models\Thread;
use App\Repositories\AudioMessageRepository;
use Throwable;

class AudioMessageController
{
    use AuthorizesRequests;

    /**
     * Display a listing of the most recent audio files.
     *
     * @param  AudioMessageRepository  $repository
     * @param  Thread  $thread
     * @return AudioMessageCollection
     *
     * @throws AuthorizationException
     */
    public function index(AudioMessageRepository $repository, Thread $thread): AudioMessageCollection
    {
        $this->authorize('viewAny', [
            Message::class,
            $thread,
        ]);

        return new AudioMessageCollection(
            $repository->getThreadAudioIndex($thread),
            $thread
        );
    }

    /**
     * Display audio history pagination.
     *
     * @param  AudioMessageRepository  $repository
     * @param  Thread  $thread
     * @param  Message  $audio
     * @return AudioMessageCollection
     *
     * @throws AuthorizationException
     */
    public function paginate(AudioMessageRepository $repository,
                             Thread $thread,
                             Message $audio): AudioMessageCollection
    {
        $this->authorize('view', [
            $audio,
            $thread,
        ]);

        return new AudioMessageCollection(
            $repository->getThreadAudioPage($thread, $audio),
            $thread,
            true,
            $audio->id
        );
    }

    /**
     * Store a new audio message.
     *
     * @param  AudioMessageRequest  $request
     * @param  StoreAudioMessage  $storeAudioMessage
     * @param  Thread  $thread
     * @return MessageResource
     *
     * @throws AuthorizationException|Throwable|FileServiceException
     */
    public function store(AudioMessageRequest $request,
                          StoreAudioMessage $storeAudioMessage,
                          Thread $thread): MessageResource
    {
        $this->authorize('createAudio', [
            Message::class,
            $thread,
        ]);

        return $storeAudioMessage->execute(
            $thread,
            $request->validated(),
            $request->ip()
        )->getJsonResource();
    }
}
