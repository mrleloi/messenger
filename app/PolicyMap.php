<?php

namespace App;

use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;
use App\Models\Bot;
use App\Models\BotAction;
use App\Models\Call;
use App\Models\CallParticipant;
use App\Models\Friend;
use App\Models\Invite;
use App\Models\Message;
use App\Models\MessageReaction;
use App\Models\Participant;
use App\Models\PendingFriend;
use App\Models\SentFriend;
use App\Models\Thread;
use App\Policies\BotActionPolicy;
use App\Policies\BotPolicy;
use App\Policies\CallParticipantPolicy;
use App\Policies\CallPolicy;
use App\Policies\FriendPolicy;
use App\Policies\InvitePolicy;
use App\Policies\MessagePolicy;
use App\Policies\MessageReactionPolicy;
use App\Policies\ParticipantPolicy;
use App\Policies\PendingFriendPolicy;
use App\Policies\SentFriendPolicy;
use App\Policies\ThreadPolicy;

/**
 * @property-read Application $app
 */
trait PolicyMap
{
    /**
     * The policy mappings for messenger models.
     *
     * @var array
     */
    private array $policies = [
        Bot::class => BotPolicy::class,
        BotAction::class => BotActionPolicy::class,
        Call::class => CallPolicy::class,
        CallParticipant::class => CallParticipantPolicy::class,
        Thread::class => ThreadPolicy::class,
        Participant::class => ParticipantPolicy::class,
        Message::class => MessagePolicy::class,
        MessageReaction::class => MessageReactionPolicy::class,
        Invite::class => InvitePolicy::class,
        Friend::class => FriendPolicy::class,
        PendingFriend::class => PendingFriendPolicy::class,
        SentFriend::class => SentFriendPolicy::class,
    ];

    /**
     * Register the application's policies.
     *
     * @return void
     *
     * @throws BindingResolutionException
     */
    private function registerPolicies(): void
    {
        $gate = $this->app->make(Gate::class);

        foreach ($this->policies as $key => $value) {
            $gate->policy($key, $value);
        }
    }
}
