<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Actions\Messenger\DestroyMessengerAvatar;
use App\Actions\Messenger\StoreMessengerAvatar;
use App\Actions\Messenger\UpdateMessengerSettings;
use App\Exceptions\FeatureDisabledException;
use App\Exceptions\FileServiceException;
use App\Http\Collections\ActiveCallCollection;
use App\Http\Request\MessengerAvatarRequest;
use App\Http\Request\MessengerSettingsRequest;
use App\Http\Resources\MessengerResource;
use App\Messenger;
use App\Repositories\ThreadRepository;
use Throwable;

class MessengerController
{
    /**
     * @var Messenger
     */
    private Messenger $messenger;

    /**
     * MessengerController constructor.
     *
     * @param  Messenger  $messenger
     */
    public function __construct(Messenger $messenger)
    {
        $this->messenger = $messenger;
    }

    /**
     * Display the messenger system settings.
     *
     * @return array
     */
    public function index(): array
    {
        return $this->messenger->getConfig();
    }

    /**
     * Display the provider's messenger.
     *
     * @return MessengerResource
     */
    public function settings(): MessengerResource
    {
        return new MessengerResource(
            $this->messenger->getProviderMessenger()
        );
    }

    /**
     * Display a listing of the providers active calls.
     *
     * @param  ThreadRepository  $threadRepository
     * @return ActiveCallCollection
     */
    public function activeCalls(ThreadRepository $threadRepository): ActiveCallCollection
    {
        return new ActiveCallCollection(
            $threadRepository->getProviderThreadsWithActiveCalls()
        );
    }

    /**
     * Update the providers messenger settings.
     *
     * @param  MessengerSettingsRequest  $request
     * @param  UpdateMessengerSettings  $settings
     * @return MessengerResource
     */
    public function updateSettings(MessengerSettingsRequest $request, UpdateMessengerSettings $settings): MessengerResource
    {
        $settings->execute($request->validated());

        return new MessengerResource(
            $this->messenger->getProviderMessenger()
        );
    }

    /**
     * Update the providers avatar.
     *
     * @param  MessengerAvatarRequest  $request
     * @param  StoreMessengerAvatar  $storeAvatar
     * @return MessengerResource
     *
     * @throws FeatureDisabledException|FileServiceException|Throwable
     */
    public function updateAvatar(MessengerAvatarRequest $request, StoreMessengerAvatar $storeAvatar): MessengerResource
    {
        $storeAvatar->execute($request->validated()['image']);

        return new MessengerResource(
            $this->messenger->getProviderMessenger()
        );
    }

    /**
     * Remove the providers avatar.
     *
     * @param  DestroyMessengerAvatar  $destroyMessengerAvatar
     * @return JsonResponse
     *
     * @throws FeatureDisabledException
     */
    public function destroyAvatar(DestroyMessengerAvatar $destroyMessengerAvatar): JsonResponse
    {
        return $destroyMessengerAvatar->execute()->getEmptyResponse();
    }
}
