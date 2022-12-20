<?php

namespace Sunil\LaravelRolePermissionAccess\Traits;

trait UserRole {

    public function isSuperAdmin() {
        return request()->user()->role_id == 1;
    }
}
