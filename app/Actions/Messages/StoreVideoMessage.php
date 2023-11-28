<?php

namespace App\Actions\Messages;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\DatabaseManager;
use Illuminate\Http\UploadedFile;
use App\Contracts\BroadcastDriver;
use App\Exceptions\FeatureDisabledException;
use App\Exceptions\FileServiceException;
use App\Http\Request\VideoMessageRequest;
use App\Messenger;
use App\Models\Message;
use App\Models\Thread;
use App\Services\FileService;
use Throwable;

class StoreVideoMessage extends NewMessageAction
{
    /**
     * @var Messenger
     */
    private Messenger $messenger;

    /**
     * @var FileService
     */
    private FileService $fileService;

    /**
     * StoreVideoMessage constructor.
     *
     * @param  BroadcastDriver  $broadcaster
     * @param  DatabaseManager  $database
     * @param  Dispatcher  $dispatcher
     * @param  Messenger  $messenger
     * @param  FileService  $fileService
     */
    public function __construct(BroadcastDriver $broadcaster,
                                DatabaseManager $database,
                                Dispatcher $dispatcher,
                                Messenger $messenger,
                                FileService $fileService)
    {
        parent::__construct(
            $broadcaster,
            $database,
            $dispatcher
        );

        $this->messenger = $messenger;
        $this->fileService = $fileService;
    }

    /**
     * Store / upload new video message, update thread
     * updated_at, mark read for participant, broadcast.
     *
     * @param  Thread  $thread
     * @param  array  $params
     * @param  string|null  $senderIp
     * @return $this
     *
     * @see VideoMessageRequest
     *
     * @throws Throwable|FeatureDisabledException|FileServiceException
     */
    public function execute(Thread $thread,
                            array $params,
                            ?string $senderIp = null): self
    {
        try {
            $this->bailIfDisabled();

            $this->setThread($thread);

            $video = $this->upload($params['video']);
            $params['messageType'] = Message::DOCUMENT_MESSAGE;
            $params['messageBody'] = $video;

            $this->setMessageType(Message::VIDEO_MESSAGE)
                ->setMessageBody($video)
                ->setMessageOptionalParameters($params)
                ->setMessageOwner($this->messenger->getProvider())
                ->setSenderIp($senderIp)
                ->storeTempMessage()
                ->generateResource();

            unset($params['video']);
            $this->fireEvents($thread, $params, $senderIp, $this->messenger->getProvider());
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            var_dump($e);
            $this->fileService
                ->setDisk($this->getThread()->getStorageDisk())
                ->destroy("{$this->getThread()->getImagesDirectory()}/$document");
            throw $e;
        }

        return $this;
    }

    /**
     * The video file has been uploaded at this point, so if
     * our database actions fail, we want to remove the file
     * from storage and rethrow the exception.
     *
     * @param  string  $fileName
     * @return void
     *
     * @throws Throwable
     */
    private function handleOrRollback(string $fileName): void
    {
        try {
            $this->process();
        } catch (Throwable $e) {
            $this->fileService
                ->setDisk($this->getThread()->getStorageDisk())
                ->destroy("{$this->getThread()->getVideoDirectory()}/$fileName");

            throw $e;
        }
    }

    /**
     * @throws FeatureDisabledException
     */
    private function bailIfDisabled(): void
    {
        if (! $this->messenger->isMessageVideoUploadEnabled()) {
            throw new FeatureDisabledException('Video messages are currently disabled.');
        }
    }

    /**
     * @param  UploadedFile  $file
     * @return string
     *
     * @throws FileServiceException
     */
    private function upload(UploadedFile $file): string
    {
        return $this->fileService
            ->setDisk($this->getThread()->getStorageDisk())
            ->setDirectory($this->getThread()->getVideoDirectory())
            ->upload($file);
    }
}
