<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Actions\Messages\StoreVideoMessage;
use App\Http\Collections\VideoMessageCollection;
use App\Http\Request\VideoMessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use App\Models\Thread;
use App\Repositories\VideoMessageRepository;
use Throwable;

class VideoMessageController
{
    use AuthorizesRequests;

    /**
     * Display a listing of the most recent video files.
     *
     * @param  VideoMessageRepository  $repository
     * @param  Thread  $thread
     * @return VideoMessageCollection
     *
     * @throws AuthorizationException
     */
    public function index(VideoMessageRepository $repository, Thread $thread): VideoMessageCollection
    {
        $this->authorize('viewAny', [
            Message::class,
            $thread,
        ]);

        return new VideoMessageCollection(
            $repository->getThreadVideosIndex($thread),
            $thread
        );
    }

    /**
     * Display video history pagination.
     *
     * @param  VideoMessageRepository  $repository
     * @param  Thread  $thread
     * @param  Message  $video
     * @return VideoMessageCollection
     *
     * @throws AuthorizationException
     */
    public function paginate(VideoMessageRepository $repository,
                             Thread $thread,
                             Message $video): VideoMessageCollection
    {
        $this->authorize('view', [
            $video,
            $thread,
        ]);

        return new VideoMessageCollection(
            $repository->getThreadVideosPage($thread, $video),
            $thread,
            true,
            $video->id
        );
    }

    /**
     * Upload a new video message.
     *
     * @param  VideoMessageRequest  $request
     * @param  StoreVideoMessage  $storeVideoMessage
     * @param  Thread  $thread
     * @return MessageResource
     *
     * @throws AuthorizationException|Throwable
     */
    public function store(VideoMessageRequest $request,
                          StoreVideoMessage $storeVideoMessage,
                          Thread $thread): MessageResource
    {
        $this->authorize('createVideo', [
            Message::class,
            $thread,
        ]);

        return $storeVideoMessage->execute(
            $thread,
            $request->validated(),
            $request->ip()
        )->getJsonResource();
    }
}
