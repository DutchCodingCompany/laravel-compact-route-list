# Compact Route List

This package extends the laravel provided route list command into `php artisan route:compact-list` and lists everything more compact and hides some first-party package url's.

## Options
As the command extends the default route list command, all those options are available. It is intentional the older short middleware style is used.

By default the compact-list command filters all `nova`, `horizon` and `debugbar` routes. There are flags (with the same name) to enable some or all of them.

- `php artisan route:compact-list --nova` includes nova routes
- `php artisan route:compact-list --horizon` includes horizon routes
- `php artisan route:compact-list --debugbar` includes debugbar routes
- `php artisan route:compact-list --without-filter` does not filter any routes

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.