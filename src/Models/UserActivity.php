<?php

namespace Sunil\LaravelRolePermissionAccess\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'body' => 'array',
    ];    
}
