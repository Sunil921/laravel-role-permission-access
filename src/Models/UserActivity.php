<?php

namespace Sunil\LaravelRolePermissionAccess\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Sunil\LaravelRolePermissionAccess\Traits\TableInfoTrait;
use App\Models\User;

class UserActivity extends Model
{
    use HasFactory, SoftDeletes, TableInfoTrait;

    // protected $casts = [ 'body' => 'array' ];
    public function user()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * Get the extended operation name.
     *
     * @param  string  $value
     * @return string
     */
    protected function getOperationAttribute($value)
    {
        switch ($value) {
            case 'c': return 'Create';
            case 'r': return 'Read';
            case 'u': return 'Update';
            case 'd': return 'Delete';
            case 'x': return 'Export';
            default: return '';
        }
    }
}
