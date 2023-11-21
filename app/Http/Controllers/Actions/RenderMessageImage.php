<?php

namespace App\Http\Controllers\Actions;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use App\Models\Message;
use App\Models\Thread;
use App\Services\ImageRenderService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RenderMessageImage
{
    /**
     * Render message image.
     *
     * @param  ImageRenderService  $service
     * @param  Thread  $thread
     * @param  Message  $message
     * @param  string  $size
     * @param  string  $image
     * @return StreamedResponse|BinaryFileResponse
     *
     * @throws FileNotFoundException
     */
    public function __invoke(ImageRenderService $service,
                             Thread $thread,
                             Message $message,
                             string $size,
                             string $image)
    {
        return $service->renderMessageImage($message, $size, $image);
    }
}
