<?php

namespace Sunil\LaravelRolePermissionAccess\Controllers;

// use App\Models\Role;
use Sunil\LaravelRolePermissionAccess\Models\Role;
use Sunil\LaravelRolePermissionAccess\Models\Module;
use Sunil\LaravelRolePermissionAccess\Models\RoleModuleOperation;
use Sunil\LaravelRolePermissionAccess\Models\RoleModule;
use Sunil\LaravelRolePermissionAccess\Models\RoleChecker;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $per_page = $request->per_page ?? config('site_vars.per_page');
        $roles = Role::orderBy('created_at','desc')->paginate($per_page);
        return view('site.role.index', ['roles' => $roles, 'per_page' => $per_page]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::all();
        $modules = Module::all();
        return view('site.role.store', ['roles' => $roles, 'modules' => $modules, 'role_modules' => [-1]]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'role' => 'required|max:50|regex:/^[\pL\s\-]+$/u',    
            'dashboard_module_id' => 'required',    
            ],[
            'role.required' => 'User role is required',
            'role.regex' => 'Numbers & Special Characters Not Allowed',
            'dashboard_module_id.required' => 'Role\'s dashboard module is required',
        ]);

        if($request->role_id){
            $role = Role::find($request->role_id);
        }
        else{
            $role = Role::where('name', $request->role)->first();
            if (isset($role))
                return redirect()->route('role.index')->withErrors(['error' => 'Role already exists.']);

            $role = new Role();
        }
        
        $role->name = $request->role;
        $role->dashboard_module_id = $request->dashboard_module_id;
        $role_save_response = $role->save();

        RoleModuleOperation::join('role_modules', 'role_modules.id', 'role_module_operations.role_module_id')
                            ->where('role_modules.role_id', $role->id)
                            ->delete();

        RoleModule::where('role_id', $role->id)->delete();
        if (isset($request->module_ids)) {
            foreach ($request->module_ids as $index => $module_id) {
                if (isset($module_id)) {
                    $role_module = RoleModule::firstOrCreate(['role_id' => $role->id, 'module_id' => $module_id]);
                    if (isset($request->operations[$index])) {
                        $operation = implode(',', $request->operations[$index]);
                            RoleModuleOperation::firstOrCreate(['role_module_id' => $role_module->id, 'operation' => $operation]);
                        }
                    }
                }
            }

        RoleChecker::where('role_checking_id', $role->id)->delete();
        if (isset($request->role_checkers)) {
            foreach ($request->role_checkers as $role_checker)
                RoleChecker::firstOrCreate(['role_checker_id' => $role_checker, 'role_checking_id' => $role->id]);
        }

        $link = 'role?table_id=' . $role->id;
        if ($role_save_response && isset($request->role_id)) {
            storeApprovalTable(Role::class, $role->id, $link, '1');
            return redirect()->route('role.index')->with('success', 'Role updated successfully.')   ;
        }
        else if ($role_save_response) {
            storeApprovalTable(Role::class, $role->id, $link, '0');
            return redirect()->route('role.index')->with('success', 'Role Added Successfully!');
        }

        return redirect()->route('role.index')->with('error', 'Role Not Added!');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = Role::find($id);
        return json_encode($role);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role_id = request()->user()->role_id;
        if (!request()->user()->isSuperAdmin() && $id <= $role_id)
            abort(401);
        $role = Role::find($id);
        $roles = Role::where('id', '!=', $id)->get();
        $role_checkers = RoleChecker::where('role_checking_id', $id)->pluck('role_checker_id')->toArray();
        $role_modules = RoleModule::where('role_id', $id)->pluck('module_id')->toArray();
        $operations = RoleModuleOperation::select('role_module_operations.operation', 'role_modules.module_id')
                                        ->join('role_modules', 'role_modules.id', 'role_module_operations.role_module_id')
                                        ->where('role_modules.role_id', $id)
                                        ->get()
                                        ->toArray();
        $operations = array_column($operations, 'operation', 'module_id');
        $modules = Module::all();
        return view('site.role.store',['role' => $role, 'modules' => $modules, 'role_modules' => [-1] ,'role_modules' => $role_modules, 'roles' => $roles, 'role_checkers' => $role_checkers, 'operations' => $operations]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Role::destroy($id);
        RoleModule::where('role_id', $id)->delete();
        RoleModuleOperation::join('role_modules', 'role_modules.id', 'role_module_operations.role_module_id')
                            ->where('role_modules.role_id', $id)
                            ->delete();
        RoleChecker::where('role_checking_id', $id)->delete();
        return redirect()->route('role.index')->with('success', 'Role Deleted Successfully!');
    }
}
