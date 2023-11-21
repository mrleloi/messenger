<?php

namespace App\Providers;

use App\Bots\RecursionBot;
use App\Brokers\FriendBroker;
use App\Brokers\JanusBroker;
use App\Models\Admin;
use App\Models\Employee;
use App\Models\Message;
use App\Models\MessageReaction;
use App\Models\Participant;
use App\Models\Thread;
use App\Models\User;
use App\Policies\MessagePolicy;
use App\Policies\MessageReactionPolicy;
use App\Policies\ParticipantPolicy;
use App\Policies\ThreadPolicy;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use App\Facades\Messenger;
use App\Facades\MessengerBots;
use App\Models\Friend;
use App\Models\PendingFriend;
use App\Models\SentFriend;
use App\Policies\FriendPolicy;
use App\Policies\PendingFriendPolicy;
use App\Policies\SentFriendPolicy;

/**
 * Laravel Messenger System.
 * Created by: Richard Tippin.
 *
 * @link https://github.com/RTippin/messenger
 * @link https://github.com/RTippin/messenger-bots
 * @link https://github.com/RTippin/messenger-faker
 * @link https://github.com/RTippin/messenger-ui
 */
class MessengerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        Relation::morphMap([
            'users' => User::class,
            'employee' => Employee::class,
            'admin' => Admin::class
        ]);

        Messenger::registerProviders([
            Employee::class,
            Admin::class,
        ]);

        // Set the video driver of your choosing.
        Messenger::setVideoDriver(JanusBroker::class);

        // Set the friend driver.
        Messenger::setFriendDriver(FriendBroker::class);

        // Register the bot handlers you wish to use.
        MessengerBots::registerHandlers([
            RecursionBot::class,
        ]);

        $this->registerCustomPolicies();
    }

    private array $customPolicies = [
//        Bot::class => BotPolicy::class,
//        BotAction::class => BotActionPolicy::class,
//        Call::class => CallPolicy::class,
//        CallParticipant::class => CallParticipantPolicy::class,
        Thread::class => ThreadPolicy::class,
        Participant::class => ParticipantPolicy::class,
        Message::class => MessagePolicy::class,
        MessageReaction::class => MessageReactionPolicy::class,
//        Invite::class => InvitePolicy::class,
        Friend::class => FriendPolicy::class,
        PendingFriend::class => PendingFriendPolicy::class,
        SentFriend::class => SentFriendPolicy::class,
    ];

    private function registerCustomPolicies(): void
    {
        $gate = $this->app->make(Gate::class);

        foreach ($this->customPolicies as $key => $value) {
            $gate->policy($key, $value);
        }
    }
}
