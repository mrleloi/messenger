<?php

namespace App\Actions\Bots;

use App\Exceptions\FeatureDisabledException;
use App\Models\Bot;

class DestroyBotAvatar extends BotAvatarAction
{
    /**
     * @param  Bot  $bot
     * @return $this
     *
     * @throws FeatureDisabledException
     */
    public function execute(Bot $bot): self
    {
        $this->bailIfDisabled();

        $this->setBot($bot)
            ->removeOldIfExist()
            ->updateBotAvatar(null);

        if ($this->getBot()->wasChanged()) {
            $this->clearActionsCache()->fireEvents();
        }

        return $this;
    }
}
