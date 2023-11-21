<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ViewPortalController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::name('messenger.')->group(function () {
    Route::get('/', [ViewPortalController::class, 'index'])->name('portal');
    Route::get('{thread}', [ViewPortalController::class, 'showThread'])->name('show');
    Route::get('/recipient/{alias}/{id}', [ViewPortalController::class, 'showCreatePrivate'])->name('private.create');
    Route::get('threads/{thread}/calls/{call}', [ViewPortalController::class, 'showVideoCall'])->name('threads.show.call');
});
