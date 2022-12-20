<?php

use Sunil\LaravelRolePermissionAccess\Controllers\RolePermissionController;
use Illuminate\Support\Facades\Route;

Route::get('roles', RolePermissionController::class);

