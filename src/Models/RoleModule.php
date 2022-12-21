<?php

namespace Sunil\LaravelRolePermissionAccess\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class RoleModule extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ["role_id", "module_id"];
}
