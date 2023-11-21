<?php

namespace App\Http\Collections;

use Illuminate\Http\Request;
use App\Http\Resources\CallResource;
use App\Models\Thread;
use Throwable;

class ActiveCallCollection extends MessengerCollection
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
            /** @var Thread $resource */
            return (new CallResource($resource->activeCall, $resource))->resolve();
        } catch (Throwable $t) {
            report($t);
        }

        return null;
    }
}
