<?php

use Sunil\LaravelRolePermissionAccess\Models\RoleModuleOperation;
use Sunil\LaravelRolePermissionAccess\Models\RoleModule;
use Sunil\LaravelRolePermissionAccess\Models\Module;

if (! function_exists('getModuleFromRoute')) {
    function getModuleFromRoute($module_link = null)
    {
        $module_link = $module_link ? $module_link : request()->segment(1);
        $module = Module::where('link', $module_link)->first();
        return $module;
    }
}

if (! function_exists('authorizeRoleModule')) {
    function authorizeRoleModule($p_operation, $module_name = null)
    {
        $request = request();
        $role_id = $request->user()->role_id;
        if ($role_id == 1)                                                      // role_id 1 means superadmin
            return 0;                                                           // approved
        $module = getModuleFromRoute($module_name);
        if (!$module)
            return -1;                                                          // reject response
        $use_role_module = RoleModule::where('role_id', $role_id)->where('module_id', $module->id)->first();
        if ($use_role_module) {
            $role_module_operation = RoleModuleOperation::where('role_module_id', $use_role_module->id)->first();
            if (isset($role_module_operation)) {
                $operation = $role_module_operation->operation;
                if (str_contains($operation, 'h') && ($p_operation == 'r' || ($p_operation == 'u' && $request->isMethod('GET'))))
                    return 1;                                                           // approve response
                else if (str_contains($operation, $p_operation))
                    return 1;                                                           // approve response
                else
                    return -1;                                                          // reject response
            }
            else
                return -1;                                                          // reject response
        }
        else
            return -1;                                                          // reject response
    }
}

if (! function_exists('getModules')) {
    function getModules()
    {
        return Module::all()->keyBy('link');
    }
}

if (! function_exists('getCurrentRoleOperation')) {
    function getCurrentRoleOperation($module_link = null)
    {
        $operation = RoleModuleOperation::select('role_module_operations.operation')
                                        ->join('role_modules', 'role_modules.id', 'role_module_operations.role_module_id')
                                        ->where('role_modules.role_id', request()->user()->role_id)
                                        ->where('role_modules.module_id', getModuleFromRoute($module_link)?->id)
                                        ->first();
        return $operation;
    }
}

if (! function_exists('showMenu')) {
    function showMenu($modules, $module_link_name)
    {
        try {
            $module_link = ltrim(route($module_link_name, [], false), '/');
            $module_name = $modules[$module_link]->name;
            $link = route($module_link_name);
            $html = "<li aria-haspopup=\"true\"><a href=\"$link\">$module_name</a></li>";

            if (request()->user()->isSuperAdmin()) return $html;

            $operation = getCurrentRoleOperation($module_link);
            if (str_contains((string)$operation?->operation, 'r')) {
                return $html;
            } else if (str_contains((string)$operation?->operation, 'c')) {
                $module_link_name = str_replace('.index', '.create', $module_link_name);
                $link = route($module_link_name);
                $html = "<li aria-haspopup=\"true\"><a href=\"$link\">$module_name</a></li>";
                return $html;
            }
        } catch(Exception $e) {}
        return '';
    }
}

if (! function_exists('getRouteOperation')) {
    /**
     * Returns route's operation
     *
     * @return operation (c / r / u / d / h / x) or boolean false if not found
     */
    function getRouteOperation()
    {
        $request = request();
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
            else if ($route_name == 'create')
                $route_operation = 'c';
            else if ($route_name == 'store' && (isset($request->update) && $request->update == 'true'))
                $route_operation = 'u';
            else if ($route_name == 'store')
                $route_operation = 'c';
            else if ($route_name == 'edit')
                $route_operation = 'u';
            else if ($route_name == 'update')
                $route_operation = 'u';
            else if ($route_name == 'destroy')
                $route_operation = 'd';
            else
                return false;
        }
        return $route_operation;
    }
}
