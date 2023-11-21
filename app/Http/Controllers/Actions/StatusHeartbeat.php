<?php

namespace App\Http\Controllers\Actions;

use App\Actions\Messenger\OnlineStatus;
use App\Http\Request\StatusHeartbeatRequest;
use App\Http\Resources\ProviderStatusResource;
use App\Messenger;

class StatusHeartbeat
{
    /**
     * Update providers online cache state.
     *
     * @param  StatusHeartbeatRequest  $request
     * @param  Messenger  $messenger
     * @param  OnlineStatus  $onlineStatus
     * @return ProviderStatusResource
     */
    public function __invoke(StatusHeartbeatRequest $request,
                             Messenger $messenger,
                             OnlineStatus $onlineStatus): ProviderStatusResource
    {
        $onlineStatus->execute($request->input('away') ?? false);

        return new ProviderStatusResource(
            $messenger->getProvider()
        );
    }
}
