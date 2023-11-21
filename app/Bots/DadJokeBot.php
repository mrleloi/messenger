<?php

namespace App\Bots;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use App\Support\BotActionHandler;
use Throwable;

class DadJokeBot extends BotActionHandler
{
    /**
     * Endpoint we gather data from.
     */
    const API_ENDPOINT = 'https://icanhazdadjoke.com/';

    /**
     * The bots settings.
     *
     * @return array
     */
    public static function getSettings(): array
    {
        return [
            'alias' => 'dad_joke',
            'description' => 'Get a random dad joke.',
            'name' => 'Dad Joke',
            'unique' => true,
        ];
    }

    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        $joke = $this->getDadJoke();

        if ($joke->failed()) {
            $this->releaseCooldown();

            return;
        }

        $this->composer()->emitTyping()->message("👨 {$joke->json('joke')}");
    }

    /**
     * @return Response
     */
    private function getDadJoke(): Response
    {
        return Http::acceptJson()->timeout(5)->get(self::API_ENDPOINT);
    }
}
