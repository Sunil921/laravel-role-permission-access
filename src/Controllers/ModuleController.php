<?php

namespace Sunil\LaravelRolePermissionAccess\Controllers;

use Sunil\LaravelRolePermissionAccess\Models\Module;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $per_page = $request->per_page ?? config('site_vars.per_page');
        $modules = Module::orderBy('created_at','desc')->paginate($per_page);
        return view('site.module.index', ['per_page' => $per_page, 'modules' => $modules]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('site.module.store');
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
            'name' => 'required',
            'link' => 'required',
            'icon' => 'required',
        ], [
            'name.required' => 'Module name is required',            
            'link.required' => 'Module link is required',            
            'icon.required' => 'Module icon is required',            
        ]);

        if (isset($request->module_id))
            $module = Module::find($request->module_id);
        else
            $module = new Module();

        $module->name = $request->name;
        $module->icon = $request->icon;
        $module->link = $request->link;
        if ($module->save()) {
            return redirect()->route('module.index')->with('success','Module store successfully');
        }
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Module  $module
     * @return \Illuminate\Http\Response
     */
    public function show(Module $module)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Module  $module
     * @return \Illuminate\Http\Response
     */
    public function edit(Module $module)
    {
        return view('site.module.store',['module' => $module]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Module  $module
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Module $module)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Module  $module
     * @return \Illuminate\Http\Response
     */
    public function destroy(Module $module)
    {
        $module->delete();
        return redirect()->route('module.index')->with('success','Module deleted successfully');
    }
}
