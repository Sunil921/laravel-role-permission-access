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
        $route_operation = getRouteOperation();
        if ($route_operation === false) abort(500);

        $user = $request->user();
        $this->setUserActivity($request, $route_operation, $user);

        if ($user->isSuperAdmin()) return $next($request);

        $auth = authorizeRoleModule($route_operation);
        if ($auth == -1) abort(401);

        return $next($request);
    }

    public function setUserActivity($request, $route_operation, $user) {
        $user_activity = new UserActivity();
        $user_activity->added_by = $user->id;
        $user_activity->body = json_encode($request->except('_token', '_method'));
        $user_activity->module_id = getModuleFromRoute()?->id;
        $user_activity->module_url = $request->path();
        $user_activity->request_method = $request->method();
        $user_activity->user_agent = $request->header('User-Agent');
        $user_activity->ipv4 = $request->getClientIp();
        $user_activity->ipv6 = $request->getClientIp(true);
        $user_activity->operation = $route_operation;
        $user_activity->save();
    }
}
