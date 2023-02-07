<?php

namespace Sunil\LaravelRolePermissionAccess\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Sunil\LaravelRolePermissionAccess\Traits\TableInfoTrait;

class UserActivity extends Model
{
    use HasFactory, SoftDeletes, TableInfoTrait;

    protected $casts = [
        'body' => 'array',
    ];    
}
