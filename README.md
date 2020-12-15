# Compact Route List

This package extends the laravel provided route list command into `php artisan route:compact-list` and lists everything more compact and hides some first-party package url's.

## Options
As the command extends the default route list command, all those options are available. It is intentional the older short middleware style is used.

By default the compact-list command filters all `nova`, `horizon` and `debugbar` routes. There are flags (with the same name) to enable some or all of them.

- `php artisan route:compact-list --nova` includes nova routes
- `php artisan route:compact-list --horizon` includes horizon routes
- `php artisan route:compact-list --debugbar` includes debugbar routes
- `php artisan route:compact-list --without-filter` does not filter any routes

## Configure filters
One can add extra filters:
```php
// adds php artisan route:compact-list --api
CompactRouteList::addFilter('api'); // adds ['api' => 'api] to filters

// adds php artisan route:compact-list --api
// adds php artisan route:compact-list --api-v2
CompactRouteList::addFilters([
    'api',
    'old-api' => ['api/v1', 'api/v2'],
]);

// adds php artisan route:compact-list --api-v1
CompactRouteList::setFilters([
    'api-v1' => 'api/v1',
]); // removes all existing filters and only uses new filters
```

Also, one can change how the filter is applied by setting a callback
```php
// this is the default callback
CompactRouteList::setFilterCallback(static function (array $route, $filter): bool {
    return \Illuminate\Support\Str::contains($route['uri'], $filter);
});
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.