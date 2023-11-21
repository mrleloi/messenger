<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Actions\DownloadMessageAudio;
use App\Http\Controllers\Actions\DownloadMessageFile;
use App\Http\Controllers\Actions\DownloadMessageVideo;
use App\Http\Controllers\Actions\RenderBotAvatar;
use App\Http\Controllers\Actions\RenderGroupAvatar;
use App\Http\Controllers\Actions\RenderMessageImage;
use App\Http\Controllers\Actions\RenderPackagedBotAvatar;
use App\Http\Controllers\Actions\RenderProviderAvatar;
use App\Http\Controllers\InviteController;

/*
|--------------------------------------------------------------------------
| Messenger Asset Routes
|--------------------------------------------------------------------------
*/

Route::name('assets.messenger.')->scopeBindings()->group(function () {
    Route::prefix('threads/{thread}')->name('threads.')->group(function () {
        Route::get('avatar/{size}/{image}', RenderGroupAvatar::class)->name('avatar.render');
        Route::get('bots/{bot}/avatar/{size}/{image}', RenderBotAvatar::class)->name('bots.avatar.render');
        Route::get('gallery/{message}/{size}/{image}', RenderMessageImage::class)->name('gallery.render');
        Route::get('files/{message}/{file}', DownloadMessageFile::class)->name('files.download');
        Route::get('audio/{message}/{audio}', DownloadMessageAudio::class)->name('audio.download');
        Route::get('videos/{message}/{video}', DownloadMessageVideo::class)->name('videos.download');
    });
    Route::get('invites/{invite:code}/avatar/{size}/{image}', [InviteController::class, 'renderAvatar'])->name('invites.avatar.render');
    Route::get('provider/{alias}/{id}/{size}/{image}', RenderProviderAvatar::class)->name('provider.avatar.render');
    Route::get('bot-package/{size}/{alias}/{image?}', RenderPackagedBotAvatar::class)->name('bot-package.avatar.render');
});
