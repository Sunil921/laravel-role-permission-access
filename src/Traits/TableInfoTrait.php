<?php
namespace Sunil\LaravelRolePermissionAccess\Traits;

use Illuminate\Support\Facades\Schema;

trait TableInfoTrait {

    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    public static function getTableColumns(array $excludes = [])
    {
        return array_diff(Schema::getColumnListing(self::getTableName()), $excludes);
    }

    public static function getRecords($per_page = null)
    {
        return $per_page ? self::orderBy('created_at', 'desc')->paginate($per_page) : self::orderBy('created_at', 'desc')->get();
    }

}
