<?php

namespace App;

use Illuminate\Support\ServiceProvider;
use App\Facades\MessengerBots;
use App\Bots\FakerBot;
use App\Commands\AudioCommand;
use App\Commands\DocumentCommand;
use App\Commands\ImageCommand;
use App\Commands\KnockCommand;
use App\Commands\MessageCommand;
use App\Commands\RandomCommand;
use App\Commands\ReactCommand;
use App\Commands\ReadCommand;
use App\Commands\SystemCommand;
use App\Commands\TypingCommand;
use App\Commands\UnReadCommand;
use App\Commands\VideoCommand;

class MessengerFakerServiceProvider extends ServiceProvider
{
    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/messenger-faker.php', 'messenger-faker');

        $this->app->singleton(MessengerFaker::class, MessengerFaker::class);
    }

    /**
     * Bootstrap any package services.
     *
     * @return void
     * @TODO v2 remove check for videos path.
     */
    public function boot(): void
    {
        if (config('messenger-faker.enable_bot')) {
            MessengerBots::registerHandlers([
                FakerBot::class,
            ]);
        }

        $videos = config('messenger-faker.paths.videos') ?? false;

        if (! $videos) {
            config([
                'messenger-faker.paths.videos' => storage_path('faker/videos'),
            ]);
        }

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/messenger-faker.php' => config_path('messenger-faker.php'),
            ], 'messenger-faker');

            $this->commands([
                AudioCommand::class,
                DocumentCommand::class,
                ImageCommand::class,
                KnockCommand::class,
                MessageCommand::class,
                RandomCommand::class,
                ReactCommand::class,
                ReadCommand::class,
                SystemCommand::class,
                TypingCommand::class,
                UnReadCommand::class,
                VideoCommand::class,
            ]);
        }
    }
}
