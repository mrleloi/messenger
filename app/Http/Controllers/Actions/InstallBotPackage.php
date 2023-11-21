<?php

namespace App\Http\Controllers\Actions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Actions\Bots\InstallPackagedBot;
use App\DataTransferObjects\PackagedBotDTO;
use App\Exceptions\BotException;
use App\Http\Request\InstallBotRequest;
use App\Http\Resources\BotResource;
use App\Messenger;
use App\MessengerBots;
use App\Models\Bot;
use App\Models\Thread;
use Throwable;

class InstallBotPackage
{
    use AuthorizesRequests;

    /**
     * @var Messenger
     */
    private Messenger $messenger;

    /**
     * @var MessengerBots
     */
    private MessengerBots $bots;

    /**
     * @var InstallPackagedBot
     */
    private InstallPackagedBot $installer;

    /**
     * @param  Messenger  $messenger
     * @param  MessengerBots  $bots
     * @param  InstallPackagedBot  $installer
     */
    public function __construct(Messenger $messenger,
                                MessengerBots $bots,
                                InstallPackagedBot $installer)
    {
        $this->messenger = $messenger;
        $this->bots = $bots;
        $this->installer = $installer;
    }

    /**
     * @param  InstallBotRequest  $request
     * @param  Thread  $thread
     * @return BotResource
     *
     * @throws AuthorizationException|Throwable
     */
    public function __invoke(InstallBotRequest $request, Thread $thread): BotResource
    {
        $this->authorize('create', [
            Bot::class,
            $thread,
        ]);

        $package = $this->bots->getPackagedBot(
            $request->validated()['alias']
        );

        $this->bailIfAuthorizationFails($package);

        return $this->installer->execute(
            $thread,
            $package
        )->getJsonResource();
    }

    /**
     * @param  PackagedBotDTO  $package
     *
     * @throws AuthorizationException
     * @throws BotException
     */
    private function bailIfAuthorizationFails(PackagedBotDTO $package): void
    {
        if (! $this->bots->authorizePackagedBot($package)) {
            throw new AuthorizationException('Not authorized to install that bot package.');
        }
    }
}
