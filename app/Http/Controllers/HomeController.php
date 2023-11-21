<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Contracts\MessengerProvider;
use App\Facades\Messenger;

class HomeController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function getDemoAccounts(): JsonResponse
    {
        $employees = Employee::demo()
            ->get()
            ->shuffle()
            ->filter(fn (Employee $user) => $user->getProviderOnlineStatus() === MessengerProvider::OFFLINE)
            ->take(5);
        $admins = Admin::demo()
            ->get()
            ->shuffle()
            ->filter(fn (Admin $user) => $user->getProviderOnlineStatus() === MessengerProvider::OFFLINE)
            ->take(5);

        return new JsonResponse([
            'html' => view('auth.demoAcc')->with([
                'users' => $employees,
                'type' => 'employee'
            ])->render(),
            'html_admin' => view('auth.demoAcc')->with([
                'users' => $admins,
                'type' => 'admin'
            ])->render(),
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function csrfHeartbeat(): JsonResponse
    {
        return new JsonResponse([
            'auth' => true,
        ]);
    }
}
