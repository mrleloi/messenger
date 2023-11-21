<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Actions\Bots\RemoveBotAction;
use App\Actions\Bots\StoreBotAction;
use App\Actions\Bots\UpdateBotAction;
use App\Exceptions\BotException;
use App\Exceptions\FeatureDisabledException;
use App\Http\Collections\BotActionCollection;
use App\Http\Resources\BotActionResource;
use App\Models\Bot;
use App\Models\BotAction;
use App\Models\Thread;
use App\Services\BotHandlerResolverService;

class BotActionController
{
    use AuthorizesRequests;

    /**
     * Display a listing of bot actions.
     *
     * @param  Thread  $thread
     * @param  Bot  $bot
     * @return BotActionCollection
     *
     * @throws AuthorizationException
     */
    public function index(Thread $thread, Bot $bot): BotActionCollection
    {
        $this->authorize('viewAny', [
            BotAction::class,
            $thread,
            $bot,
        ]);

        return new BotActionCollection(
            $bot->validActions()
                ->with('owner')
                ->get()
        );
    }

    /**
     * Display the bot action.
     *
     * @param  Thread  $thread
     * @param  Bot  $bot
     * @param  BotAction  $action
     * @return BotActionResource
     *
     * @throws AuthorizationException
     */
    public function show(Thread $thread,
                         Bot $bot,
                         BotAction $action): BotActionResource
    {
        $this->authorize('view', [
            $action,
            $thread,
            $bot,
        ]);

        return new BotActionResource(
            $action->load('owner')
        );
    }

    /**
     * @param  Request  $request
     * @param  BotHandlerResolverService  $resolver
     * @param  StoreBotAction  $storeBotAction
     * @param  Thread  $thread
     * @param  Bot  $bot
     * @return BotActionResource
     *
     * @throws FeatureDisabledException|ValidationException
     * @throws BotException|AuthorizationException
     */
    public function store(Request $request,
                          BotHandlerResolverService $resolver,
                          StoreBotAction $storeBotAction,
                          Thread $thread,
                          Bot $bot): BotActionResource
    {
        $this->authorize('create', [
            BotAction::class,
            $thread,
            $bot,
        ]);

        $resolved = $resolver->resolve($request->all());

        return $storeBotAction->execute(
            $thread,
            $bot,
            $resolved
        )->getJsonResource();
    }

    /**
     * @param  Request  $request
     * @param  BotHandlerResolverService  $resolver
     * @param  UpdateBotAction  $updateBotAction
     * @param  Thread  $thread
     * @param  Bot  $bot
     * @param  BotAction  $action
     * @return BotActionResource
     *
     * @throws FeatureDisabledException|ValidationException
     * @throws BotException|AuthorizationException
     */
    public function update(Request $request,
                           BotHandlerResolverService $resolver,
                           UpdateBotAction $updateBotAction,
                           Thread $thread,
                           Bot $bot,
                           BotAction $action): BotActionResource
    {
        $this->authorize('update', [
            $action,
            $thread,
            $bot,
        ]);

        $resolved = $resolver->resolve($request->all(), $action->handler);

        return $updateBotAction->execute(
            $action,
            $resolved
        )->getJsonResource();
    }

    /**
     * Remove the bot action.
     *
     * @param  RemoveBotAction  $removeBotAction
     * @param  Thread  $thread
     * @param  Bot  $bot
     * @param  BotAction  $action
     * @return JsonResponse
     *
     * @throws AuthorizationException|FeatureDisabledException
     */
    public function destroy(RemoveBotAction $removeBotAction,
                            Thread $thread,
                            Bot $bot,
                            BotAction $action): JsonResponse
    {
        $this->authorize('delete', [
            $action,
            $thread,
            $bot,
        ]);

        return $removeBotAction->execute($action)->getEmptyResponse();
    }
}
