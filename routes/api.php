<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Actions\AvailableBotHandlers;
use App\Http\Controllers\Actions\AvailableBotPackages;
use App\Http\Controllers\Actions\CallHeartbeat;
use App\Http\Controllers\Actions\DemoteAdmin;
use App\Http\Controllers\Actions\EndCall;
use App\Http\Controllers\Actions\FilterAddParticipants;
use App\Http\Controllers\Actions\FindRecipientThread;
use App\Http\Controllers\Actions\IgnoreCall;
use App\Http\Controllers\Actions\InstallBotPackage;
use App\Http\Controllers\Actions\IsThreadUnread;
use App\Http\Controllers\Actions\JoinCall;
use App\Http\Controllers\Actions\JoinGroupInvite;
use App\Http\Controllers\Actions\KnockKnock;
use App\Http\Controllers\Actions\LeaveCall;
use App\Http\Controllers\Actions\MarkThreadRead;
use App\Http\Controllers\Actions\MuteThread;
use App\Http\Controllers\Actions\PrivateThreadApproval;
use App\Http\Controllers\Actions\PromoteAdmin;
use App\Http\Controllers\Actions\Search;
use App\Http\Controllers\Actions\StatusHeartbeat;
use App\Http\Controllers\Actions\ThreadArchiveState;
use App\Http\Controllers\Actions\ThreadLoader;
use App\Http\Controllers\Actions\UnmuteThread;
use App\Http\Controllers\Actions\UnreadThreadsCount;
use App\Http\Controllers\AudioMessageController;
use App\Http\Controllers\BotActionController;
use App\Http\Controllers\BotController;
use App\Http\Controllers\CallController;
use App\Http\Controllers\CallParticipantController;
use App\Http\Controllers\DocumentMessageController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\GroupThreadController;
use App\Http\Controllers\ImageMessageController;
use App\Http\Controllers\InviteController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\MessageReactionController;
use App\Http\Controllers\MessengerController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\PendingFriendController;
use App\Http\Controllers\PrivateThreadController;
use App\Http\Controllers\SentFriendController;
use App\Http\Controllers\SystemMessageController;
use App\Http\Controllers\ThreadController;
use App\Http\Controllers\VideoMessageController;

/*
|--------------------------------------------------------------------------
| Messenger API Routes
|--------------------------------------------------------------------------
*/

Route::name('api.messenger.')->scopeBindings()->group(function () {
    //Messenger view service settings
    Route::get('/', [MessengerController::class, 'index'])->name('info');
    //Invitation join
    Route::post('join/{invite:code}', JoinGroupInvite::class)->name('invites.join.store');
    //Search
    Route::get('search/{query?}', Search::class)->middleware('throttle:messenger-search')->name('search');
    //Base messenger routes
    Route::post('heartbeat', StatusHeartbeat::class)->name('heartbeat');
    Route::get('active-calls', [MessengerController::class, 'activeCalls'])->name('active.calls');
    Route::get('settings', [MessengerController::class, 'settings'])->name('settings');
    Route::put('settings', [MessengerController::class, 'updateSettings'])->name('settings.update');
    Route::post('avatar', [MessengerController::class, 'updateAvatar'])->name('avatar.update');
    Route::delete('avatar', [MessengerController::class, 'destroyAvatar'])->name('avatar.destroy');
    Route::get('unread-threads-count', UnreadThreadsCount::class)->name('unread.threads.count');
    Route::get('groups/page/{group}', [GroupThreadController::class, 'paginate'])->name('groups.page');
    Route::get('privates/page/{private}', [PrivateThreadController::class, 'paginate'])->name('privates.page');
    Route::get('privates/recipient/{alias}/{id}', FindRecipientThread::class)->name('privates.locate');
    //Thread resources
    Route::get('threads/page/{thread}', [ThreadController::class, 'paginate'])->name('threads.page');
    Route::prefix('threads/{thread}')->name('threads.')->group(function () {
        //Common
        Route::get('load/{relations?}', ThreadLoader::class)->name('loader'); //TODO v2 remove {relations?}
        Route::get('logs', [SystemMessageController::class, 'index'])->name('logs');
        Route::get('mark-read', MarkThreadRead::class)->name('mark.read');
        Route::get('is-unread', IsThreadUnread::class)->name('is.unread');
        Route::post('knock-knock', KnockKnock::class)->name('knock');
        Route::get('check-archive', ThreadArchiveState::class)->name('archive.check');
        Route::post('mute', MuteThread::class)->name('mute');
        Route::post('unmute', UnmuteThread::class)->name('unmute');
        Route::get('logs/page/{log}', [SystemMessageController::class, 'paginate'])->name('logs.page');
        //Groups
        Route::post('leave', [GroupThreadController::class, 'leave'])->name('leave');
        Route::get('settings', [GroupThreadController::class, 'settings'])->name('settings');
        Route::put('settings', [GroupThreadController::class, 'updateSettings'])->name('settings.update');
        Route::post('avatar', [GroupThreadController::class, 'storeAvatar'])->name('avatar.store');
        Route::delete('avatar', [GroupThreadController::class, 'destroyAvatar'])->name('avatar.destroy');
        Route::get('add-participants', FilterAddParticipants::class)->name('add.participants');
        //Privates
        Route::post('approval', PrivateThreadApproval::class)->name('approval');
        //Messages
        Route::prefix('messages')->name('messages.')->group(function () {
            Route::post('/', [MessageController::class, 'store'])->middleware('throttle:messenger-message')->name('store');
            Route::get('page/{message}', [MessageController::class, 'paginate'])->name('page');
            Route::get('{message}/history', [MessageController::class, 'showEdits'])->name('history');
            Route::delete('{message}/embeds', [MessageController::class, 'removeEmbeds'])->name('embeds.destroy');
        });
        //Image Messages
        Route::prefix('images')->name('images.')->group(function () {
            Route::get('page/{image}', [ImageMessageController::class, 'paginate'])->name('page');
            Route::get('/', [ImageMessageController::class, 'index'])->name('index');
            Route::post('/', [ImageMessageController::class, 'store'])->middleware('throttle:messenger-attachment')->name('store');
        });
        //Document Messages
        Route::prefix('documents')->name('documents.')->group(function () {
            Route::get('page/{document}', [DocumentMessageController::class, 'paginate'])->name('page');
            Route::get('/', [DocumentMessageController::class, 'index'])->name('index');
            Route::post('/', [DocumentMessageController::class, 'store'])->middleware('throttle:messenger-attachment')->name('store');
        });
        //Audio Messages
        Route::prefix('audio')->name('audio.')->group(function () {
            Route::get('page/{audio}', [AudioMessageController::class, 'paginate'])->name('page');
            Route::get('/', [AudioMessageController::class, 'index'])->name('index');
            Route::post('/', [AudioMessageController::class, 'store'])->middleware('throttle:messenger-attachment')->name('store');
        });
        //Video Messages
        Route::prefix('videos')->name('videos.')->group(function () {
            Route::get('videos/page/{video}', [VideoMessageController::class, 'paginate'])->name('page');
            Route::get('/', [VideoMessageController::class, 'index'])->name('index');
            Route::post('/', [VideoMessageController::class, 'store'])->middleware('throttle:messenger-attachment')->name('store');
        });
        //Participants
        Route::prefix('participants')->name('participants.')->group(function () {
            Route::get('page/{participant}', [ParticipantController::class, 'paginate'])->name('page');
            Route::post('{participant}/promote', PromoteAdmin::class)->name('promote');
            Route::post('{participant}/demote', DemoteAdmin::class)->name('demote');
        });
        //Bot Packages
        Route::prefix('bots/packages')->name('bots.packages.')->group(function () {
            Route::get('/', AvailableBotPackages::class)->name('index');
            Route::post('/', InstallBotPackage::class)->name('store');
        });
        //Bots
        Route::prefix('bots/{bot}')->name('bots.')->group(function () {
            Route::get('add-handlers', AvailableBotHandlers::class)->name('handlers');
            Route::post('avatar', [BotController::class, 'storeAvatar'])->name('avatar.store');
            Route::delete('avatar', [BotController::class, 'destroyAvatar'])->name('avatar.destroy');
        });
        //Calls
        Route::prefix('calls')->name('calls.')->group(function () {
            Route::get('page/{call}', [CallController::class, 'paginate'])->name('page');
            Route::post('{call}/join', JoinCall::class)->name('join');
            Route::post('{call}/leave', LeaveCall::class)->name('leave');
            Route::post('{call}/end', EndCall::class)->name('end');
            Route::post('{call}/ignore', IgnoreCall::class)->name('ignore');
            Route::get('{call}/heartbeat', CallHeartbeat::class)->name('heartbeat');
        });
    });
    //Core API resources
    Route::apiResource('groups', GroupThreadController::class)->only(['index', 'store']);
    Route::apiResource('privates', PrivateThreadController::class)->only(['index', 'store']);
    Route::apiResource('threads', ThreadController::class)->except(['store', 'update']);
    Route::apiResource('threads.participants', ParticipantController::class);
    Route::apiResource('threads.bots', BotController::class);
    Route::apiResource('threads.bots.actions', BotActionController::class);
    Route::apiResource('threads.messages', MessageController::class)->except('store');
    Route::apiResource('threads.messages.reactions', MessageReactionController::class)->except(['show', 'update']);
    Route::apiResource('threads.invites', InviteController::class)->except(['show', 'update']);
    Route::apiResource('threads.calls', CallController::class)->except(['update', 'destroy']);
    Route::apiResource('threads.calls.participants', CallParticipantController::class)->except(['store', 'destroy']);
    Route::prefix('friends')->name('friends.')->group(function () {
        Route::apiResource('pending', PendingFriendController::class)->except('store');
        Route::apiResource('sent', SentFriendController::class)->except('update');
    });
    Route::apiResource('friends', FriendController::class)->except(['update', 'store']);
});
