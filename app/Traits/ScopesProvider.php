<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Contracts\MessengerProvider;
use App\Models\Call;
use App\Models\CallParticipant;
use App\Models\Friend;
use App\Models\Invite;
use App\Models\Message;
use App\Models\MessageReaction;
use App\Models\Messenger;
use App\Models\Participant;
use App\Models\PendingFriend;
use App\Models\SentFriend;
use App\Models\Thread;

/**
 * @method static Builder|Call|CallParticipant|Friend|Invite|Message|MessageReaction|Messenger|Participant|PendingFriend|SentFriend|Thread forProvider(MessengerProvider $provider, string $morph = 'owner')
 * @method static Builder|Call|CallParticipant|Friend|Invite|Message|MessageReaction|Messenger|Participant|PendingFriend|SentFriend|Thread forProviderWithModel(Model $model, string $modelKey = 'owner', string $morphKey = 'owner')
 * @method static Builder|Call|CallParticipant|Friend|Invite|Message|MessageReaction|Messenger|Participant|PendingFriend|SentFriend|Thread notProvider(MessengerProvider $provider, string $morph = 'owner')
 * @method static Builder|Call|CallParticipant|Friend|Invite|Message|MessageReaction|Messenger|Participant|PendingFriend|SentFriend|Thread notProviderWithModel(Model $model, string $modelKey = 'owner', string $morphKey = 'owner')
 */
trait ScopesProvider
{
    /**
     * Scope a query for belonging to the given provider.
     *
     * @param  Builder  $query
     * @param  MessengerProvider  $provider
     * @param  string  $morph
     * @return Builder
     */
    public function scopeForProvider(Builder $query,
                                     MessengerProvider $provider,
                                     string $morph = 'owner'): Builder
    {
        return $query->where("{$morph}_id", '=', $provider->getKey())
            ->where("{$morph}_type", '=', $provider->getMorphClass());
    }

    /**
     * Scope a query not belonging to the given provider.
     *
     * @param  Builder  $query
     * @param  MessengerProvider  $provider
     * @param  string  $morph
     * @return Builder
     */
    public function scopeNotProvider(Builder $query,
                                     MessengerProvider $provider,
                                     string $morph = 'owner'): Builder
    {
        return $query->whereRaw("NOT ({$morph}_id=? AND {$morph}_type=?)", [$provider->getKey(), $provider->getMorphClass()]);
    }

    /**
     * Scope a query for belonging to the given model using relation keys present.
     *
     * @param  Builder  $query
     * @param  Model  $model
     * @param  string  $modelKey
     * @param  string  $morphKey
     * @return Builder
     */
    public function scopeForProviderWithModel(Builder $query,
                                              Model $model,
                                              string $modelKey = 'owner',
                                              string $morphKey = 'owner'): Builder
    {
        return $query->where("{$morphKey}_id", '=', $model->{"{$modelKey}_id"})
            ->where("{$morphKey}_type", '=', $model->{"{$modelKey}_type"});
    }

    /**
     * Scope a query not belonging to the given model using relation keys present.
     *
     * @param  Builder  $query
     * @param  Model  $model
     * @param  string  $modelKey
     * @param  string  $morphKey
     * @return Builder
     */
    public function scopeNotProviderWithModel(Builder $query,
                                              Model $model,
                                              string $modelKey = 'owner',
                                              string $morphKey = 'owner'): Builder
    {
        return $query->whereRaw("NOT ({$morphKey}_id=? AND {$morphKey}_type=?)", [$model->{$modelKey.'_id'}, $model->{$modelKey.'_type'}]);
    }
}
