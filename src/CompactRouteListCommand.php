<?php

namespace DutchCodingCompany\CompactRouteList;

use Closure;
use Illuminate\Foundation\Console\RouteListCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class CompactRouteListCommand extends RouteListCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'route:compact-list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all registered routes in a compact form';

    /**
     * The table headers for the command.
     *
     * Removed 'Domain' header for better overview
     *
     * @var array
     */
    protected $headers = ['Method', 'URI', 'Name', 'Action', 'Middleware'];

    /**
     * Get before filters.
     *
     * Use old way of presenting middleware (shorthand instead of FQDN) by default
     *
     * @param \Illuminate\Routing\Route $route
     *
     * @return string
     */
    protected function getMiddleware($route)
    {
        // Only use new way of presenting middleware when requested
        if ($this->option('middleware')) {
            return parent::getMiddleware($route);
        }

        return collect($route->gatherMiddleware())->map(function ($middleware) {
            return $middleware instanceof Closure ? 'Closure' : $middleware;
        })->implode(',');
    }

    /**
     * Filter the route by URI and / or name.
     *
     * Filter nova routes by default
     *
     * @param array $route
     *
     * @return array|null
     */
    protected function filterRoute(array $route)
    {
        $filteredRoute = parent::filterRoute($route);

        if ($filteredRoute) {
            foreach ($this->filterOptions() as $option => $uriSegment) {
                if (! $this->option($option) && ! $this->option('without-filter') && Str::contains($filteredRoute['uri'], $uriSegment)) {
                    return;
                }
            }
        }

        return $filteredRoute;
    }

    protected function filterOptions(): array
    {
        return [
            'nova'     => 'nova',
            'horizon'  => 'horizon',
            'debugbar' => '_debugbar',
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        $options = [
            ['middleware', 'm', InputOption::VALUE_NONE, 'Use FQDN for middleware column'],
        ];

        foreach ($this->filterOptions() as $option => $uriSegment) {
            $options[] = [$option, null, InputOption::VALUE_NONE, 'Include '.$option.' routes (filtered by default)'];
        }

        $options[] = ['without-filter', null, InputOption::VALUE_NONE, 'Do not filter routes containing '.collect($this->filterOptions())->keys()->join(', ', ' and ').'.'];

        // Add the extra options
        return array_merge(parent::getOptions(), $options);
    }
}
