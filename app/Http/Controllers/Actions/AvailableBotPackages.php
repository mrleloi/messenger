<?php

namespace App\Http\Controllers\Actions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use App\DataTransferObjects\PackagedBotDTO;
use App\MessengerBots;
use App\Models\Bot;
use App\Models\Thread;

class AvailableBotPackages
{
    use AuthorizesRequests;

    /**
     * Get all authorized bot packages the current
     * provider is allowed to choose from.
     *
     * @param  MessengerBots  $bots
     * @param  Thread  $thread
     * @return Collection
     *
     * @throws AuthorizationException
     */
    public function __invoke(MessengerBots $bots, Thread $thread): Collection
    {
        $this->authorize('create', [
            Bot::class,
            $thread,
        ]);

        return $bots->getAuthorizedPackagedBots()->each(
            fn (PackagedBotDTO $package) => $package->applyInstallFilters($thread)
        );
    }
}
