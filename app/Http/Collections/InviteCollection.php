<?php

namespace App\Http\Collections;

use Illuminate\Http\Request;
use App\Facades\Messenger;
use App\Http\Resources\InviteResource;
use App\Models\Thread;
use Throwable;

class InviteCollection extends MessengerCollection
{
    /**
     * InviteCollection constructor.
     *
     * @param $resource
     * @param  Thread  $thread
     */
    public function __construct($resource, Thread $thread)
    {
        parent::__construct($resource);

        $this->thread = $thread;
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'data' => $this->safeTransformer(),
            'meta' => [
                'total' => $this->thread->invites()->valid()->count(),
                'max_allowed' => Messenger::getThreadMaxInvitesCount() ?: null,
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    protected function makeResource($resource): ?array
    {
        try {
            return (new InviteResource($resource))->resolve();
        } catch (Throwable $t) {
            report($t);
        }

        return null;
    }
}
