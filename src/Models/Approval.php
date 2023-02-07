<?php

namespace Sunil\LaravelRolePermissionAccess\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Sunil\LaravelRolePermissionAccess\Traits\TableInfoTrait;

class Approval extends Model
{
    use HasFactory, SoftDeletes, TableInfoTrait;
    protected $fillable = ["row_id", "table_name", "module_name", "operation", "link", "added_by", "approve", "approved_by", "approved_at"];
}
