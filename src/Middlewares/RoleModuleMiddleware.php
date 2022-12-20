<?php

namespace Sunil\LaravelRolePermissionAccess\Middlewares;

use Closure;
use Illuminate\Http\Request;

class RoleModuleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $route_operation = request()->route()->getAction();
        if (isset($route_operation['otype'])) {
            $route_operation = $route_operation['otype'];
            if ($route_operation == 'c' && (isset(request()->update) && request()->update == 'true'))
                $route_operation = 'u';
        }
        else {
            $route_name = request()->route()->getActionMethod();
            if ($route_name == 'index')
                $route_operation = 'r';
            else if ($route_name == 'show')
                $route_operation = 'r';
            else if ($route_name == 'create' && (isset(request()->update) && request()->update == 'true'))
                $route_operation = 'u';
            else if ($route_name == 'create')
                $route_operation = 'c';
            else if ($route_name == 'store')
                $route_operation = 'c';
            else if ($route_name == 'edit')
                $route_operation = 'u';
            else if ($route_name == 'update')
                $route_operation = 'u';
            else if ($route_name == 'destroy')
                $route_operation = 'd';
            else
                abort(500);
        }
        $role_id = $request->user()->role_id;
        if ($request->user()->isSuperAdmin())
            return $next($request);
        $auth = authorizeRoleModule($route_operation);
        if ($auth == -1)
            abort(401);
        return $next($request);
    }
}
