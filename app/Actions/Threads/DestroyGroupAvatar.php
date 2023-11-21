<?php

namespace App\Actions\Threads;

use Exception;
use App\Exceptions\FeatureDisabledException;
use App\Exceptions\FileServiceException;
use App\Models\Thread;

class DestroyGroupAvatar extends GroupAvatarAction
{
    /**
     * @param  Thread  $thread
     * @return $this
     *
     * @throws FeatureDisabledException|FileServiceException|Exception
     */
    public function execute(Thread $thread): self
    {
        $this->bailIfDisabled();

        $this->setThread($thread)
            ->removeOldIfExist()
            ->updateGroupAvatar(null);

        if ($this->getThread()->wasChanged()) {
            $this->fireBroadcast()->fireEvents();
        }

        return $this;
    }
}
