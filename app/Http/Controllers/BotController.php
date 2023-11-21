<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use App\Actions\Bots\ArchiveBot;
use App\Actions\Bots\DestroyBotAvatar;
use App\Actions\Bots\StoreBot;
use App\Actions\Bots\StoreBotAvatar;
use App\Actions\Bots\UpdateBot;
use App\Exceptions\FeatureDisabledException;
use App\Http\Collections\BotCollection;
use App\Http\Request\BotAvatarRequest;
use App\Http\Request\BotRequest;
use App\Http\Resources\BotResource;
use App\Models\Bot;
use App\Models\Thread;
use Throwable;

class BotController
{
    use AuthorizesRequests;

    /**
     * Display a listing of bots.
     *
     * @param  Thread  $thread
     * @return BotCollection
     *
     * @throws AuthorizationException
     */
    public function index(Thread $thread): BotCollection
    {
        $this->authorize('viewAny', [
            Bot::class,
            $thread,
        ]);

        return new BotCollection(
            $thread->bots()
                ->with('owner')
                ->withCount('validActions')
                ->get()
        );
    }

    /**
     * Display the bot.
     *
     * @param  Thread  $thread
     * @param  Bot  $bot
     * @return BotResource
     *
     * @throws AuthorizationException
     */
    public function show(Thread $thread, Bot $bot): BotResource
    {
        $this->authorize('view', [
            $bot,
            $thread,
        ]);

        return new BotResource(
            $bot,
            $bot->isActionsVisible($thread)
        );
    }

    /**
     * Store a bot.
     *
     * @param  BotRequest  $request
     * @param  StoreBot  $storeBot
     * @param  Thread  $thread
     * @return BotResource
     *
     * @throws AuthorizationException|FeatureDisabledException
     */
    public function store(BotRequest $request,
                          StoreBot $storeBot,
                          Thread $thread): BotResource
    {
        $this->authorize('create', [
            Bot::class,
            $thread,
        ]);

        return $storeBot->execute(
            $thread,
            $request->validated()
        )->getJsonResource();
    }

    /**
     * Update the bot.
     *
     * @param  BotRequest  $request
     * @param  UpdateBot  $updateBot
     * @param  Thread  $thread
     * @param  Bot  $bot
     * @return BotResource
     *
     * @throws AuthorizationException|FeatureDisabledException
     */
    public function update(BotRequest $request,
                           UpdateBot $updateBot,
                           Thread $thread,
                           Bot $bot): BotResource
    {
        $this->authorize('update', [
            $bot,
            $thread,
        ]);

        return $updateBot->execute(
            $bot,
            $request->validated()
        )->getJsonResource();
    }

    /**
     * Store the bots avatar.
     *
     * @param  BotAvatarRequest  $request
     * @param  StoreBotAvatar  $storeBotAvatar
     * @param  Thread  $thread
     * @param  Bot  $bot
     * @return BotResource
     *
     * @throws AuthorizationException|FeatureDisabledException|Throwable
     */
    public function storeAvatar(BotAvatarRequest $request,
                                StoreBotAvatar $storeBotAvatar,
                                Thread $thread,
                                Bot $bot): BotResource
    {
        $this->authorize('update', [
            $bot,
            $thread,
        ]);

        return $storeBotAvatar->execute(
            $bot,
            $request->validated()['image']
        )->getJsonResource();
    }

    /**
     * Remove the bots avatar.
     *
     * @param  DestroyBotAvatar  $destroyBotAvatar
     * @param  Thread  $thread
     * @param  Bot  $bot
     * @return JsonResponse
     *
     * @throws AuthorizationException|FeatureDisabledException
     */
    public function destroyAvatar(DestroyBotAvatar $destroyBotAvatar,
                                  Thread $thread,
                                  Bot $bot): JsonResponse
    {
        $this->authorize('update', [
            $bot,
            $thread,
        ]);

        return $destroyBotAvatar->execute($bot)->getEmptyResponse();
    }

    /**
     * Remove the bot.
     *
     * @param  ArchiveBot  $archiveBot
     * @param  Thread  $thread
     * @param  Bot  $bot
     * @return JsonResponse
     *
     * @throws AuthorizationException|FeatureDisabledException
     */
    public function destroy(ArchiveBot $archiveBot,
                            Thread $thread,
                            Bot $bot): JsonResponse
    {
        $this->authorize('delete', [
            $bot,
            $thread,
        ]);

        return $archiveBot->execute($bot)->getEmptyResponse();
    }
}
