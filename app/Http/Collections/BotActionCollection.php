<?php

namespace App\Http\Collections;

use Illuminate\Http\Request;
use App\Http\Resources\BotActionResource;
use Throwable;

class BotActionCollection extends MessengerCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return $this->safeTransformer();
    }

    /**
     * @inheritDoc
     */
    protected function makeResource($resource): ?array
    {
        try {
            return (new BotActionResource($resource))->resolve();
        } catch (Throwable $t) {
            report($t);
        }

        return null;
    }
}
