
## 1. composer.json
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

## 2. Run the command to install package
``` bash
composer require Sunil/laravel-role-permission-access
```

## 3. User table must have `role_id` column

## 4. Add the service provider in this file `config/app.php`
``` php
'providers' => [
    // ...
    Sunil\LaravelRolePermissionAccess\Providers\RolePermissionProvider::class,
];
```

## 5. Run the command to publish migration files
``` bash
php artisan vendor:publish --provider="Sunil\LaravelRolePermissionAccess\Providers\RolePermissionProvider"
```
