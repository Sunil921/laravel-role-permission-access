<?php

namespace Sunil\LaravelRolePermissionAccess\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class RoleChecker extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ["role_checker_id", "role_checking_id"];
}
