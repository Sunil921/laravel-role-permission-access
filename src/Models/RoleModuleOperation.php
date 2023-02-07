<?php

namespace Sunil\LaravelRolePermissionAccess\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Sunil\LaravelRolePermissionAccess\Traits\TableInfoTrait;

class RoleModuleOperation extends Model
{
    use HasFactory, SoftDeletes, TableInfoTrait;
    protected $fillable = ["role_module_id", "operation"];
}
