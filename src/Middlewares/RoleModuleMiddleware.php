<?php

namespace Sunil\LaravelRolePermissionAccess\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Sunil\LaravelRolePermissionAccess\Models\UserActivity;

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
        $route_operation = $request->route()->getAction();
        if (isset($route_operation['otype'])) {
            $route_operation = $route_operation['otype'];
            if ($route_operation == 'c' && (isset($request->update) && $request->update == 'true'))
                $route_operation = 'u';
        }
        else {
            $route_name = $request->route()->getActionMethod();
            if ($route_name == 'index')
                $route_operation = 'r';
            else if ($route_name == 'show')
                $route_operation = 'r';
            else if ($route_name == 'create' && (isset($request->update) && $request->update == 'true'))
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
        $user = $request->user();
        $this->setUserActivity($request, $route_operation, $user);
        if ($user->isSuperAdmin())
            return $next($request);
        $auth = authorizeRoleModule($route_operation);
        if ($auth == -1)
            abort(401);
        return $next($request);
    }

    public function setUserActivity($request, $route_operation, $user) {
        $user_activity = new UserActivity();
        $user_activity->added_by = $user->id;
        $user_activity->body = json_encode($request->all());
        $user_activity->module_id = $user->module->id;
        $user_activity->module_url = $request->path();
        $user_activity->request_method = $request->method();
        $user_activity->user_agent = $request->header('User-Agent');
        $user_activity->ipv4 = $request->getClientIp();
        $user_activity->ipv6 = $request->getClientIp(true);
        $user_activity->operation = $route_operation;
        $user_activity->save();
    }
}
