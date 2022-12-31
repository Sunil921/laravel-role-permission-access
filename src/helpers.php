<?php

use Sunil\LaravelRolePermissionAccess\Models\RoleModuleOperation;
use Sunil\LaravelRolePermissionAccess\Models\RoleModule;
use Sunil\LaravelRolePermissionAccess\Models\Module;
use Sunil\LaravelRolePermissionAccess\Models\Approval;

if (! function_exists('getModuleFromRoute')) {
    function getModuleFromRoute($module_name = null)
    {
        $module_name = $module_name ? $module_name : request()->segment(1);
        $module = Module::where('link', $module_name)->first();
        return $module;
    }
}

if (! function_exists('authorizeRoleModule')) {
    function authorizeRoleModule($p_operation, $module_name = null)
    {
        $role_id = request()->user()->role_id;
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
                if (str_contains($operation, 'h') && ($p_operation == 'r' || ($p_operation == 'u' && $module->menu == 'Manage' && in_array("GET", request()->route()->methods()))))
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

if (! function_exists('authorizeModuleMenu')) {
    function authorizeModuleMenu()
    {
        $moduls_menu_items = Module::all();
        return $moduls_menu_items;
    }
}

if (! function_exists('getCurrentRoleOperation')) {
    function getCurrentRoleOperation()
    {
        $operation = RoleModuleOperation::select('role_module_operations.operation')
                                        ->join('role_modules', 'role_modules.id', 'role_module_operations.role_module_id')
                                        ->where('role_modules.role_id', request()->user()->role_id)
                                        ->where('role_modules.module_id', getModuleFromRoute()->id)
                                        ->first();
        return $operation;
    }
}

if (! function_exists('storeApprovalTable')) {
    function storeApprovalTable($table, $id, $link, $operation)
    {
        $table_name = app($table)->getTable();
        $module = getModuleFromRoute();
        $approval_table = ['row_id' => $id, 'table_name' => $table_name, 'module_name' => $module->name, 'link' => url($link), 'added_by' => request()->user()->id, 'operation' => $operation];
        Approval::create($approval_table);
    }
}

if (! function_exists('getTableDataWithApproval')) {
    function getTableDataWithApproval($table)
    {
        $table_name = app($table)->getTable();
        $operation = getCurrentRoleOperation();
        if (!request()->user()->isSuperAdmin() && str_contains($operation->operation, 'h') && !isset(request()->table_id)) {
            $table_records = $table::select($table_name . '.*')
                                    ->join('approvals', 'approvals.row_id', $table_name . '.id')
                                    ->where('approvals.approve', '!=', '1')->orderBy('id','desc')
                                    ->get();
        }
        else if (!request()->user()->isSuperAdmin() && str_contains($operation->operation, 'h') && isset(request()->table_id)) {
            $table_records = $table::select($table_name . '.*')
                                    ->join('approvals', 'approvals.row_id', $table_name . '.id')
                                    ->where('approvals.approve', '!=', '1')
                                    ->where($table_name . '.id', request()->table_id)->orderBy('id','desc')
                                    ->get();
        }
        else
            $table_records = $table::orderBy('id','desc')->get();
        return $table_records;
    }
}
