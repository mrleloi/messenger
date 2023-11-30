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


Route::middleware(['auth:employee,admin'])->group(function () {
    Route::get('home', [HomeController::class, 'index'])->name('home');
})->as('v2.');

Route::get('/', function() {
    if(auth('employee')->check()){
        return redirect()->route('messenger.portal')->withSuccess(__('messages_employee.login_success'));
    }
    else if(auth('admin')->check()){
        return redirect()->route('messenger.portal')->withSuccess(__('messages_admin.login_success'));
    }
    return view('splash');
})->name('home');
Route::get('demo-logins', [HomeController::class, 'getDemoAccounts'])->middleware('guest');
Route::view('config', 'config')->name('config');
Route::get('login-page', [LoginController::class, 'showLoginForm'])->name('login');
Route::match(['get', 'post'], 'login', [LoginController::class, 'login']);
Route::post('heartbeat', [HomeController::class, 'csrfHeartbeat'])->middleware(['auth:employee,admin']);
Route::get('logout', [LoginController::class, 'logout'])->middleware(['auth:employee,admin']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout')->middleware(['auth:employee,admin']);

//Route::middleware(['auth:employee,admin'])->group(function () {
Route::get('test', [\App\Http\Controllers\TestController::class, 'index'])->middleware(['auth:employee,admin']);
