<?php

namespace DutchCodingCompany\CompactRouteList;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class CompactRouteList
{
    /**
     * @var array key = option; value = filter
     */
    protected static array $filters = [
        'nova'     => 'nova',
        'horizon'  => 'horizon',
        'debugbar' => '_debugbar',
        'ignition' => '_ignition',
    ];

    protected static ?Closure $filterCallback = null;

    public static function addFilter(string $filter, string $option = null): void
    {
        static::$filters[$option ?? $filter] = $filter;
    }

    public static function addFilters(array $filters): void
    {
        static::$filters = (new Collection($filters))
            ->mapWithKeys(static fn ($filter, $option) => (is_int($option) && is_string($filter)) ? [$filter => $filter] : [$option => $filter])
            ->all() + static::$filters;
    }

    public static function setFilters(array $filters): void
    {
        static::$filters = [];

        static::addFilters($filters);
    }

    public static function getFilters(): array
    {
        return static::$filters;
    }

    public static function setFilterCallback(Closure $callback): void
    {
        static::$filterCallback = $callback;
    }

    public static function filterCallback(): Closure
    {
        return static::$filterCallback ??= static function (array $route, $filter): bool {
            return Str::contains($route['uri'], $filter);
        };
    }
}
