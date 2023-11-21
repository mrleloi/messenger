<?php

namespace App\Http\Middleware;

use App\Models\AccessLog;
use Closure;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Jenssegers\Agent\Facades\Agent;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Spatie\Permission\Models\Role;

class PermissionMiddleware
{
        public function handle($request, Closure $next, $permission = null, $guard = null)
        {
            $authGuard = app('auth')->guard($guard);

            if ($authGuard->guest()) {
                if (app()->isLocal()) {
                    throw UnauthorizedException::notLoggedIn();
                }
                if(strpos($guard, 'admin') === 0) {
                    return redirect()->route('admin.login')->withError('bạn phải đăng nhập');
                } else {
                    return redirect()->route('employee.login')->withError('bạn phải đăng nhập');
                }

            }

            // if ($authGuard->check()) {
            //     if(!Session()->has($guard.'_access_token')) {
            //         return redirect()->route($guard.'logout');
            //     } else {
            //         //nếu có tocken nhưng tocken ko đúng
            //         // dd();
            //         // $token = Session()->get($guard.'_access_token');
            //         // $model = Sanctum::$personalAccessTokenModel;
            //         // $accessToken = $model::findToken($token);
            //         // dd($accessToken);
            //     }
            // }

            $user = $authGuard->user();

            if (!is_null($permission) && !empty($permission)) {
                $permissions = is_array($permission)
                    ? $permission
                    : explode('|', $permission);

                foreach ($permissions as $permission) {
                    if ($authGuard->user()->can($permission)) {
                        return $next($request);
                    }
                }
            } else {
                $permissions = array($request->route()->getName());
                $role = $user->roles[0];
                $roles_api = Role::where(['name'=>$user->roles[0]->name,'guard_name'=>$user->roles[0]->guard_name . '_api'])->first();
                foreach ($permissions as $permission) {
                    if ($roles_api->checkPermissionTo($permission) || $role->checkPermissionTo($permission)) {
                        return $next($request);
                    }
                }
            }

            if (app()->isLocal()) {
                throw UnauthorizedException::forPermissions($permissions);
            }

            if(strpos($guard, 'admin') === 0) {
                return redirect()->route('admin.logout')->withError('bạn không có quyền');
            } else {
                return redirect()->route('employee.logout')->withError('bạn không có quyền');
            }
        }
}
