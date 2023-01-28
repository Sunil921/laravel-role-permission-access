<?php

namespace Sunil\LaravelRolePermissionAccess\Traits;

use Sunil\LaravelRolePermissionAccess\Models\Role;
use Sunil\LaravelRolePermissionAccess\Models\Module;

trait UserRole {

    public function isSuperAdmin() {
        return $this->role_id == 1;
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function module() {
        return $this->belongsTo(Module::class, 'role_id', 'id');
    }
}
