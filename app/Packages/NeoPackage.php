<?php

namespace App\Packages;

use App\MessengerBots;
use App\Support\PackagedBot;
use App\Bots\CommandsBot;
use App\Bots\DocumentFinderBot;
use App\Bots\GiphyBot;
use App\Bots\InviteBot;
use App\Bots\LocationBot;
use App\Bots\NukeBot;
use App\Bots\RandomImageBot;
use App\Bots\ReactionBot;
use App\Bots\WeatherBot;
use App\Bots\WikiBot;
use App\Bots\YoutubeBot;

class NeoPackage extends PackagedBot
{
    const COOL_TRIGGERS = ['cool', 'nice', 'awesome', 'sweet', '100', ':100:', 'wow'];

    /**
     * The packages settings.
     *
     * @return array
     */
    public static function getSettings(): array
    {
        return [
            'alias' => 'neo_package',
            'description' => 'Bundles internet searching and general help topic actions.',
            'name' => 'Neo',
            'avatar' => __DIR__.'/../../assets/neo_package_avatar.jpg',
        ];
    }

    /**
     * The handlers and their settings to install.
     *
     * @return array
     */
    public static function installs(): array
    {
        return [
            CommandsBot::class => [
                'cooldown' => 120,
            ],
            DocumentFinderBot::class => [
                'cooldown' => 15,
                'limit' => 10,
            ],
            GiphyBot::class => [
                'cooldown' => 15,
            ],
            InviteBot::class => [
                'cooldown' => 120,
                'lifetime_minutes' => 15,
            ],
            LocationBot::class => [
                'cooldown' => 15,
            ],
            NukeBot::class => [
                'admin_only' => true,
                'cooldown' => 0,
            ],
            RandomImageBot::class => [
                'cooldown' => 60,
                'match' => MessengerBots::MATCH_EXACT_CASELESS,
                'triggers' => ['!image', '!picture'],
            ],
            ReactionBot::class => [
                [
                    'match' => MessengerBots::MATCH_CONTAINS_CASELESS,
                    'reaction' => '👍',
                    'triggers' => self::COOL_TRIGGERS,
                ],
                [
                    'match' => MessengerBots::MATCH_CONTAINS_CASELESS,
                    'reaction' => '💯',
                    'triggers' => self::COOL_TRIGGERS,
                ],
            ],
            WeatherBot::class => [
                'cooldown' => 15,
            ],
            WikiBot::class => [
                'cooldown' => 15,
                'limit' => 3,
            ],
            YoutubeBot::class => [
                'cooldown' => 15,
                'limit' => 1,
            ],
        ];
    }
}
