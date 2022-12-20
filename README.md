
## composer.json
``` json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/Sunil921/laravel-role-permission-access.git"
        }
    ]
}
```

## Run command
``` bash
composer require Sunil/laravel-role-permission-access
```

## If you want to update run command
``` bash
composer update
```

## Run command
``` bash
php artisan vendor:publish --provider="Sunil\LaravelRolePermissionAccess\Providers\RolePermissionProvider"
```
