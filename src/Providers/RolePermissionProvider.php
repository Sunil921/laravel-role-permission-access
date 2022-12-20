<?php

namespace Sunil\LaravelRolePermissionAccess\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class RolePermissionProvider extends ServiceProvider
{
    public function bladeDirectives() {
        Blade::if('canCreate', function () {
            $super_admin = request()->user()->isSuperAdmin();
            if ($super_admin) return true;
            $operation = getCurrentRoleOperation();
            return str_contains($operation->operation, 'c') ? true : false;
        });

        Blade::if('canRead', function () {
            $super_admin = request()->user()->isSuperAdmin();
            if ($super_admin) return true;
            $operation = getCurrentRoleOperation();
            return str_contains($operation->operation, 'r') ? true : false;
        });

        Blade::if('canUpdate', function ($check = null) {
            $super_admin = request()->user()->isSuperAdmin();
            if ($super_admin) return true;
            $operation = getCurrentRoleOperation();
            if ($check)
                return str_contains($operation->operation, 'h') ? true : false;
            return str_contains($operation->operation, 'u') ? true : false;
        });

        Blade::if('canCreateOrUpdate', function () {
            $super_admin = request()->user()->isSuperAdmin();
            if ($super_admin) return true;
            $operation = getCurrentRoleOperation();
            return str_contains($operation->operation, 'c') ? true : (str_contains($operation->operation, 'u') ? true : false);
        });

        Blade::if('canDelete', function () {
            $super_admin = request()->user()->isSuperAdmin();
            if ($super_admin) return true;
            $operation = getCurrentRoleOperation();
            return str_contains($operation->operation, 'd') ? true : false;
        });

        Blade::if('canCheck', function () {
            $super_admin = request()->user()->isSuperAdmin();
            if ($super_admin) return true;
            $operation = getCurrentRoleOperation();
            return str_contains($operation->operation, 'h') ? true : false;
        });

        Blade::if('canExportOthers', function () {
            $super_admin = request()->user()->isSuperAdmin();
            if ($super_admin) return true;
            $operation = getCurrentRoleOperation();
            return str_contains($operation->operation, 'x') ? true : false;
        });
    }
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->bladeDirectives();
    }
}
