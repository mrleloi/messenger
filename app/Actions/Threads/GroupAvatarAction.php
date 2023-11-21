<?php

namespace App\Actions\Threads;

use Illuminate\Contracts\Events\Dispatcher;
use App\Actions\BaseMessengerAction;
use App\Broadcasting\ThreadAvatarBroadcast;
use App\Contracts\BroadcastDriver;
use App\Events\ThreadAvatarEvent;
use App\Exceptions\FeatureDisabledException;
use App\Http\Resources\Broadcast\ThreadSettingsBroadcastResource;
use App\Http\Resources\ThreadSettingsResource;
use App\Messenger;
use App\Services\FileService;

abstract class GroupAvatarAction extends BaseMessengerAction
{
    /**
     * @var FileService
     */
    protected FileService $fileService;

    /**
     * @var Messenger
     */
    protected Messenger $messenger;

    /**
     * @var BroadcastDriver
     */
    protected BroadcastDriver $broadcaster;

    /**
     * @var Dispatcher
     */
    protected Dispatcher $dispatcher;

    /**
     * GroupAvatarAction constructor.
     *
     * @param  Messenger  $messenger
     * @param  BroadcastDriver  $broadcaster
     * @param  FileService  $fileService
     * @param  Dispatcher  $dispatcher
     */
    public function __construct(Messenger $messenger,
                                BroadcastDriver $broadcaster,
                                FileService $fileService,
                                Dispatcher $dispatcher)
    {
        $this->fileService = $fileService;
        $this->messenger = $messenger;
        $this->dispatcher = $dispatcher;
        $this->broadcaster = $broadcaster;
    }

    /**
     * @return void
     *
     * @throws FeatureDisabledException
     */
    protected function bailIfDisabled(): void
    {
        if (! $this->messenger->isThreadAvatarEnabled()) {
            throw new FeatureDisabledException('Group avatars are currently disabled.');
        }
    }

    /**
     * @return $this
     */
    protected function removeOldIfExist(): self
    {
        if (! is_null($this->getThread()->image)) {
            $this->fileService
                ->setDisk($this->getThread()->getStorageDisk())
                ->destroy($this->getThread()->getAvatarPath());
        }

        return $this;
    }

    /**
     * @param  string|null  $avatar
     * @return $this
     */
    protected function updateGroupAvatar(?string $avatar): self
    {
        $this->getThread()->timestamps = false;

        $this->getThread()->update([
            'image' => $avatar,
        ]);

        return $this;
    }

    /**
     * @return $this
     */
    protected function generateResource(): self
    {
        $this->setJsonResource(new ThreadSettingsResource(
            $this->getThread()
        ));

        return $this;
    }

    /**
     * @return $this
     */
    protected function fireBroadcast(): self
    {
        if ($this->shouldFireBroadcast()) {
            $this->broadcaster
                ->toPresence($this->getThread())
                ->with($this->generateBroadcastResource())
                ->broadcast(ThreadAvatarBroadcast::class);
        }

        return $this;
    }

    /**
     * @return void
     */
    protected function fireEvents(): void
    {
        if ($this->shouldFireEvents()) {
            $this->dispatcher->dispatch(new ThreadAvatarEvent(
                $this->messenger->getProvider(true),
                $this->getThread(true)
            ));
        }
    }

    /**
     * @return array
     */
    private function generateBroadcastResource(): array
    {
        return (new ThreadSettingsBroadcastResource(
            $this->messenger->getProvider(),
            $this->getThread()
        ))->resolve();
    }
}
