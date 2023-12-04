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
            ->leftJoin(\DB::raw('(select  max(id) as mid,tokenable_id,tokenable_type from personal_access_tokens group by tokenable_type,tokenable_id having max(id)) as `table_tokens`'), function (JoinClause $join) {
                $join->on('employee.id', '=', 'table_tokens.tokenable_id')
                    ->where('table_tokens.tokenable_type', '=', '\'App\\\Models\\\Employee\'');
            })
			->leftJoin('personal_access_tokens', 'table_tokens.mid', 'personal_access_tokens.id')
            ->get()
            ->shuffle()
//            ->filter(fn (Employee $user) => $user->getProviderOnlineStatus() === MessengerProvide>
            ->take(5);

        $admins = Admin::demo()
            ->leftJoin(\DB::raw('(select  max(id) as mid,tokenable_id,tokenable_type from personal_access_tokens group by tokenable_type,tokenable_id having max(id)) as `table_tokens`'), function (JoinClause $join) {
                $join->on('admin.id', '=', 'table_tokens.tokenable_id')
                    ->where('table_tokens.tokenable_type', '=', '\'App\\\Models\\\Admin\'');
            })
			->leftJoin('personal_access_tokens', 'table_tokens.mid', 'personal_access_tokens.id')
            ->get()
            ->shuffle()
//            ->filter(fn (Admin $user) => $user->getProviderOnlineStatus() === MessengerProvider::>
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
