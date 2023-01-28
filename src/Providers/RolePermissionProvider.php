<?php

namespace Sunil\LaravelRolePermissionAccess\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;

class RolePermissionProvider extends ServiceProvider
{
    protected function offerPublishing() {
        if (! function_exists('config_path')) {
            // function not available and 'publish' not relevant in Lumen
            return;
        }

        $this->publishes([ __DIR__.'/../../database/migrations/create_modules_table.php.stub' => $this->getMigrationFileName('create_modules_table.php', 1) ], 'migrations');
        
        $this->publishes([ __DIR__.'/../../database/migrations/create_roles_table.php.stub' => $this->getMigrationFileName('create_roles_table.php', 2) ], 'migrations');
        
        $this->publishes([ __DIR__.'/../../database/migrations/create_role_modules_table.php.stub' => $this->getMigrationFileName('create_role_modules_table.php.php', 3) ], 'migrations');
        
        $this->publishes([ __DIR__.'/../../database/migrations/create_role_module_operations_table.php.stub' => $this->getMigrationFileName('create_role_module_operations_table.php', 4) ], 'migrations');
        
        $this->publishes([ __DIR__.'/../../database/migrations/create_role_checkers_table.php.stub' => $this->getMigrationFileName('create_role_checkers_table.php', 5) ], 'migrations');
        
        $this->publishes([ __DIR__.'/../../database/migrations/create_approvals_table.php.stub' => $this->getMigrationFileName('create_approvals_table.php', 6) ], 'migrations');
    }

    public function checkPermissions($operation1, $operation2 = null) {
        $super_admin = request()->user()->isSuperAdmin();
        if ($super_admin) return true;
        $operation = getCurrentRoleOperation();
        return str_contains($operation->operation, $operation1) ? true : (isset($operation2) && str_contains($operation->operation, $operation2) ? true : false);
    }

    public function bladeDirectives() {
        Blade::if('canCreate', function () {
            $this->checkPermissions('c');
        });

        Blade::if('canRead', function () {
            $this->checkPermissions('r');
        });

        Blade::if('canReadOrCreate', function () {
            $this->checkPermissions('r', 'c');
        });

        Blade::if('canUpdate', function ($check = null) {
            $this->checkPermissions('h', 'u');
        });

        Blade::if('canCreateOrUpdate', function () {
            $this->checkPermissions('c', 'u');
        });

        Blade::if('canUpdateOrDelete', function () {
            $this->checkPermissions('d', 'u');
        });

        Blade::if('canDelete', function () {
            $this->checkPermissions('d');
        });

        Blade::if('canCheck', function () {
            $this->checkPermissions('h');
        });

        Blade::if('canExportOthers', function () {
            $this->checkPermissions('x');
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
        // $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        $this->offerPublishing();
        $this->bladeDirectives();
    }

    protected function getMigrationFileName($migrationFileName, $order): string {
        $timestamp = date('Y_m_d_His');

        $filesystem = $this->app->make(Filesystem::class);

        return Collection::make($this->app->databasePath().DIRECTORY_SEPARATOR.'migrations'.DIRECTORY_SEPARATOR)
            ->flatMap(function ($path) use ($filesystem, $migrationFileName) {
                return $filesystem->glob($path.'*_'.$migrationFileName);
            })
            ->push($this->app->databasePath()."/migrations/{$timestamp}{$order}_{$migrationFileName}")
            ->first();
    }
}
