<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Actions\Messages\StoreImageMessage;
use App\Exceptions\FileServiceException;
use App\Http\Collections\ImageMessageCollection;
use App\Http\Request\ImageMessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use App\Models\Thread;
use App\Repositories\ImageMessageRepository;
use Throwable;

class ImageMessageController
{
    use AuthorizesRequests;

    /**
     * Display a listing of the most recent image files.
     *
     * @param  ImageMessageRepository  $repository
     * @param  Thread  $thread
     * @return ImageMessageCollection
     *
     * @throws AuthorizationException
     */
    public function index(ImageMessageRepository $repository, Thread $thread): ImageMessageCollection
    {
        $this->authorize('viewAny', [
            Message::class,
            $thread,
        ]);

        return new ImageMessageCollection(
            $repository->getThreadImagesIndex($thread),
            $thread
        );
    }

    /**
     * Display image history pagination.
     *
     * @param  ImageMessageRepository  $repository
     * @param  Thread  $thread
     * @param  Message  $image
     * @return ImageMessageCollection
     *
     * @throws AuthorizationException
     */
    public function paginate(ImageMessageRepository $repository,
                             Thread $thread,
                             Message $image): ImageMessageCollection
    {
        $this->authorize('view', [
            $image,
            $thread,
        ]);

        return new ImageMessageCollection(
            $repository->getThreadImagesPage($thread, $image),
            $thread,
            true,
            $image->id
        );
    }

    /**
     * Upload a new image message.
     *
     * @param  ImageMessageRequest  $request
     * @param  StoreImageMessage  $storeImageMessage
     * @param  Thread  $thread
     * @return MessageResource
     *
     * @throws AuthorizationException|Throwable|FileServiceException
     */
    public function store(ImageMessageRequest $request,
                          StoreImageMessage $storeImageMessage,
                          Thread $thread): MessageResource
    {
        $this->authorize('createImage', [
            Message::class,
            $thread,
        ]);

        return $storeImageMessage->execute(
            $thread,
            $request->validated(),
            $request->ip()
        )->getJsonResource();
    }
}
