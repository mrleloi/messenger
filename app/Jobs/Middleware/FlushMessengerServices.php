<?php

namespace App\Jobs\Middleware;

use App\Facades\Messenger;
use App\Facades\MessengerBots;

class FlushMessengerServices
{
    /**
     * Process the queued job.
     *
     * @param  mixed  $job
     * @param  callable  $next
     * @return mixed
     */
    public function handle($job, callable $next)
    {
        Messenger::flush();
        MessengerBots::flush();

        return $next($job);
    }
}
