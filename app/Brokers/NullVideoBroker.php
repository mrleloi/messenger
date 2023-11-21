<?php

namespace App\Brokers;

use App\Contracts\VideoDriver;
use App\Models\Call;
use App\Models\Thread;

class NullVideoBroker implements VideoDriver
{
    /**
     * @inheritDoc
     */
    public function create(Thread $thread, Call $call): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function destroy(Call $call): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getRoomId(): ?string
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getRoomPin(): ?string
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getRoomSecret(): ?string
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getExtraPayload(): ?string
    {
        return null;
    }
}
