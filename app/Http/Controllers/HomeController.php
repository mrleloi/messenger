<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Query\JoinClause;
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
            ->leftJoin('personal_access_tokens', function (JoinClause $join) {
                $join->on('employee.id', '=', 'personal_access_tokens.tokenable_id')
                    ->where('personal_access_tokens.tokenable_type', '=', 'App\Models\Employee')
                    ->where('personal_access_tokens.updated_at', '=', \DB::raw('
						(select max(updated_at) from personal_access_tokens
						where employee.id=personal_access_tokens.tokenable_id and personal_access_tokens.tokenable_type="App\Models\Employee")
					'));
            })
            ->get()
            ->shuffle()
//            ->filter(fn (Employee $user) => $user->getProviderOnlineStatus() === MessengerProvider::OFFLINE)
            ->take(5);

        $admins = Admin::demo()
            ->leftJoin('personal_access_tokens', function (JoinClause $join) {
                $join->on('admin.id', '=', 'personal_access_tokens.tokenable_id')
                    ->where('personal_access_tokens.tokenable_type', '=', 'App\Models\Admin')
                    ->where('personal_access_tokens.updated_at', '=', \DB::raw('
						(select max(updated_at) from personal_access_tokens
						where admin.id=personal_access_tokens.tokenable_id and personal_access_tokens.tokenable_type="App\Models\Admin")
					'));
            })
            ->get()
            ->shuffle()
//            ->filter(fn (Admin $user) => $user->getProviderOnlineStatus() === MessengerProvider::OFFLINE)
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
