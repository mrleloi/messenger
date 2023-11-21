<?php

namespace App;

use Illuminate\Broadcasting\BroadcastManager;
use Illuminate\Contracts\Broadcasting\Broadcaster;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Broadcast;
use App\Broadcasting\Channels\CallChannel;
use App\Broadcasting\Channels\ProviderChannel;
use App\Broadcasting\Channels\ThreadChannel;

/**
 * @property-read Application $app
 */
trait ChannelMap
{
    /**
     * Register all broadcast channels used by messenger.
     *
     * @return void
     *
     * @throws BindingResolutionException
     */
    private function registerChannels(): void
    {
        if (config('messenger.routing.channels.enabled')) {
            $this->app->make(BroadcastManager::class)->routes($this->channelRouteConfiguration());

            $broadcaster = $this->app->make(Broadcaster::class);

//            Broadcast::routes();
//            Broadcast::channel('presence-messenger.{id}', true);
//            Broadcast::channel('private-messenger.{id}', true);
//            Broadcast::channel('thread.{id}', true);
//            Broadcast::channel('user.{id}', true);
//            Broadcast::channel('messenger.{id}', true);

            $broadcaster->channel('messenger.thread.{thread}', ThreadChannel::class);
            $broadcaster->channel('messenger.call.{call}.thread.{thread}', CallChannel::class);
            $broadcaster->channel('messenger.{alias}.{id}', ProviderChannel::class);
        }
    }

    /**
     * Get the Broadcasting channel route group configuration array.
     *
     * @return array
     */
    private function channelRouteConfiguration(): array
    {
        return [
            'domain' => config('messenger.routing.channels.domain'),
            'prefix' => config('messenger.routing.channels.prefix'),
            'middleware' => $this->mergeApiMiddleware(config('messenger.routing.channels.middleware')),
        ];
    }
}
