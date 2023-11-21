<?php

namespace App;

use Illuminate\Support\ServiceProvider;
use App\Facades\MessengerBots;
use App\Bots\ChuckNorrisBot;
use App\Bots\CoinTossBot;
use App\Bots\CommandsBot;
use App\Bots\DadJokeBot;
use App\Bots\DocumentFinderBot;
use App\Bots\GiphyBot;
use App\Bots\InsultBot;
use App\Bots\InviteBot;
use App\Bots\JokeBot;
use App\Bots\KanyeBot;
use App\Bots\KnockBot;
use App\Bots\LocationBot;
use App\Bots\NukeBot;
use App\Bots\RandomImageBot;
use App\Bots\ReactionBombBot;
use App\Bots\ReactionBot;
use App\Bots\ReplyBot;
use App\Bots\RockPaperScissorsBot;
use App\Bots\RollBot;
use App\Bots\WeatherBot;
use App\Bots\WikiBot;
use App\Bots\YoMommaBot;
use App\Bots\YoutubeBot;
use App\Packages\GamesPackage;
use App\Packages\JokesterPackage;
use App\Packages\NeoPackage;

class MessengerBotsServiceProvider extends ServiceProvider
{
    /**
     * All bot handlers provided by this package.
     */
    const HANDLERS = [
        ChuckNorrisBot::class,
        CoinTossBot::class,
        CommandsBot::class,
        DadJokeBot::class,
        DocumentFinderBot::class,
        GiphyBot::class,
        InsultBot::class,
        InviteBot::class,
        JokeBot::class,
        KanyeBot::class,
        KnockBot::class,
        LocationBot::class,
        NukeBot::class,
        RandomImageBot::class,
        ReactionBombBot::class,
        ReactionBot::class,
        ReplyBot::class,
        RockPaperScissorsBot::class,
        RollBot::class,
        WeatherBot::class,
        WikiBot::class,
        YoMommaBot::class,
        YoutubeBot::class,
    ];

    /**
     * All bot packages provided by this package.
     */
    const PACKAGES = [
        GamesPackage::class,
        JokesterPackage::class,
        NeoPackage::class,
    ];

    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot(): void
    {
        if (config('messenger-bots.auto_register_all')) {
            MessengerBots::registerHandlers(self::HANDLERS);
            MessengerBots::registerPackagedBots(self::PACKAGES);
        }

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/messenger-bots.php' => config_path('messenger-bots.php'),
            ], 'messenger-bots');
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/messenger-bots.php', 'messenger-bots');
    }
}
