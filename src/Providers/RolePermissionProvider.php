<?php

namespace Sunil\LaravelRolePermissionAccess\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;

class RolePermissionProvider extends ServiceProvider
{
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
    }

    public function checkPermissions($operation1, $operation2 = null, $module_link = null) {
        $super_admin = request()->user()->isSuperAdmin();
        if ($super_admin) return true;
        $operation = getCurrentRoleOperation($module_link);
        if (empty($operation)) return false;
        return str_contains($operation->operation, $operation1) ? true : (isset($operation2) && str_contains($operation->operation, $operation2) ? true : false);
    }

    public function bladeDirectives() {
        Blade::if('canCreate', function () {
            return $this->checkPermissions('c');
        });

        Blade::if('canRead', function () {
            return $this->checkPermissions('r');
        });

        Blade::if('canReadOrCreate', function ($module_link = null) {
            $module_link = ltrim(route($module_link, [], false), '/');
            return $this->checkPermissions('r', 'c', $module_link);
        });

        Blade::if('canUpdate', function ($check = null) {
            return $this->checkPermissions('h', 'u');
        });

        Blade::if('canCreateOrUpdate', function () {
            return $this->checkPermissions('c', 'u');
        });

        Blade::if('canUpdateOrDelete', function () {
            return $this->checkPermissions('d', 'u');
        });

        Blade::if('canDelete', function () {
            return $this->checkPermissions('d');
        });

        Blade::if('canCheck', function () {
            return $this->checkPermissions('h');
        });

        Blade::if('canExportOthers', function () {
            return $this->checkPermissions('x');
        });
    }

    public function registerLockModelEvents()
    {
        $events = ['eloquent.saving: *', 'eloquent.creating: *', 'eloquent.updating: *', 'eloquent.deleting: *', 'eloquent.restoring: *'];
        Event::listen($events, function ($model) {
            return false;
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
        // $this->registerLockModelEvents();
    }
}
