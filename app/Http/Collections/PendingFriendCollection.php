<?php

namespace App\Http\Collections;

use Illuminate\Http\Request;
use App\Contracts\MessengerProvider;
use App\Http\Resources\PendingFriendResource;
use App\Models\PendingFriend;
use Throwable;

class PendingFriendCollection extends MessengerCollection
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
        /** @var PendingFriend $friend */
        $friend = $resource;

        try {
            return $friend->sender instanceof MessengerProvider
                ? (new PendingFriendResource($friend))->resolve()
                : null;
        } catch (Throwable $t) {
            report($t);
        }

        return null;
    }
}
