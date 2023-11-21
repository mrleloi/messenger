<?php

namespace App\Actions\Bots;

use Illuminate\Http\UploadedFile;
use App\Exceptions\FeatureDisabledException;
use App\Exceptions\FileServiceException;
use App\Models\Bot;
use Throwable;

class StoreBotAvatar extends BotAvatarAction
{
    /**
     * @param  Bot  $bot
     * @param  UploadedFile  $image
     * @return $this
     *
     * @throws FeatureDisabledException|FileServiceException|Throwable
     */
    public function execute(Bot $bot, UploadedFile $image): self
    {
        $this->bailIfDisabled();

        $this->setBot($bot);

        $this->handleOrRollback($this->upload($image));

        $this->clearActionsCache()
            ->generateResource()
            ->fireEvents();

        return $this;
    }

    /**
     * The avatar has been uploaded at this point, so if our
     * database actions fail, we want to remove the avatar
     * from storage and rethrow the exception.
     *
     * @param  string  $fileName
     *
     * @throws Throwable
     */
    private function handleOrRollback(string $fileName): void
    {
        try {
            $this->removeOldIfExist()->updateBotAvatar($fileName);
        } catch (Throwable $e) {
            $this->fileService
                ->setDisk($this->getBot()->getStorageDisk())
                ->destroy("{$this->getBot()->getAvatarDirectory()}/$fileName");

            throw $e;
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
            ->setDisk($this->getBot()->getStorageDisk())
            ->setDirectory($this->getBot()->getAvatarDirectory())
            ->upload($file);
    }
}
