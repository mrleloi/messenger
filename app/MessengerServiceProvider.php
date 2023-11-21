<?php

namespace App;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use App\Brokers\BroadcastBroker;
use App\Brokers\FriendBroker;
use App\Brokers\NullVideoBroker;
use App\Commands\AttachMessengersCommand;
use App\Commands\CallsActivityCheckCommand;
use App\Commands\CallsDownCommand;
use App\Commands\CallsUpCommand;
use App\Commands\InstallCommand;
use App\Commands\InvitesCheckCommand;
use App\Commands\MakeBotCommand;
use App\Commands\MakePackagedBotCommand;
use App\Commands\PurgeAudioCommand;
use App\Commands\PurgeBotsCommand;
use App\Commands\PurgeDocumentsCommand;
use App\Commands\PurgeImagesCommand;
use App\Commands\PurgeMessagesCommand;
use App\Commands\PurgeThreadsCommand;
use App\Commands\PurgeVideosCommand;
use App\Contracts\BroadcastDriver;
use App\Contracts\EmojiInterface;
use App\Contracts\FriendDriver;
use App\Contracts\VideoDriver;
use RTippin\Messenger\Exceptions\Handler;
use App\Http\Middleware\MessengerApi;
use App\Listeners\BotSubscriber;
use App\Listeners\CallSubscriber;
use App\Listeners\SystemMessageSubscriber;
use App\Models\Bot;
use App\Services\EmojiService;
use App\Support\MessengerComposer;

class MessengerServiceProvider extends ServiceProvider
{
    use ChannelMap,
        PolicyMap,
        RouteMap;

    /**
     * Register Messenger's services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/messenger.php', 'messenger');

        $this->app->singleton(Messenger::class, Messenger::class);
        $this->app->singleton(MessengerBots::class, MessengerBots::class);
        $this->app->singleton(FriendDriver::class, FriendBroker::class);
        $this->app->singleton(EmojiInterface::class, EmojiService::class);
        $this->app->bind(MessengerComposer::class, MessengerComposer::class);
        $this->app->bind(BroadcastDriver::class, BroadcastBroker::class);
        $this->app->bind(VideoDriver::class, NullVideoBroker::class);
        $this->app->extend(ExceptionHandler::class, fn (ExceptionHandler $handler) => new Handler($handler));
        $this->app->alias(Messenger::class, 'messenger');
        $this->app->alias(MessengerBots::class, 'messenger-bots');
        $this->app->alias(MessengerComposer::class, 'messenger-composer');
    }

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     *
     * @throws BindingResolutionException
     */
    public function boot(): void
    {
        Messenger::shouldUseUuids(config('messenger.provider_uuids'));

        Messenger::shouldUseAbsoluteRoutes(config('messenger.use_absolute_routes'));

        Relation::morphMap([
            'bots' => Bot::class,
        ]);

        $this->registerRouterServices();
        $this->registerPolicies();
        $this->registerSubscribers();
        $this->registerChannels();

        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    private function bootForConsole(): void
    {
        $this->commands([
            AttachMessengersCommand::class,
            CallsActivityCheckCommand::class,
            CallsDownCommand::class,
            CallsUpCommand::class,
            InstallCommand::class,
            InvitesCheckCommand::class,
            MakeBotCommand::class,
            MakePackagedBotCommand::class,
            PurgeAudioCommand::class,
            PurgeBotsCommand::class,
            PurgeDocumentsCommand::class,
            PurgeImagesCommand::class,
            PurgeMessagesCommand::class,
            PurgeThreadsCommand::class,
            PurgeVideosCommand::class,
        ]);

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->publishes([
            __DIR__.'/../config/messenger.php' => config_path('messenger.php'),
        ], 'messenger.config');

        $this->publishes([
            __DIR__.'/../stubs/MessengerServiceProvider.stub' => app_path('Providers/MessengerServiceProvider.php'),
        ], 'messenger.provider');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'messenger.migrations');
    }

    /**
     * Register the Event Subscribers.
     *
     * @return void
     *
     * @throws BindingResolutionException
     */
    private function registerSubscribers(): void
    {
        $events = $this->app->make(Dispatcher::class);

        $events->subscribe(CallSubscriber::class);
        $events->subscribe(SystemMessageSubscriber::class);
        $events->subscribe(BotSubscriber::class);
    }

    /**
     * Prepend our API middleware, merge additional
     * middleware, append throttle middleware.
     *
     * @param $middleware
     * @return array
     */
    private function mergeApiMiddleware($middleware): array
    {
        $merged = array_merge([MessengerApi::class], is_array($middleware) ? $middleware : [$middleware]);

        $merged[] = 'throttle:messenger-api';

        return $merged;
    }
}
