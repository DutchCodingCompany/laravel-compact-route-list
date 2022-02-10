<?php

namespace DutchCodingCompany\CompactRouteList;

use Closure;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Foundation\Console\RouteListCommand;

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
     * Display the route information on the console.
     *
     * @param  array  $routes
     * @return void
     */
    protected function displayRoutes(array $routes)
    {
        if ($this->option('json')) {
            if (method_exists($this, $method = 'asJson')) {
                $this->output->writeln($this->{$method}(collect($routes)));
            } else {
                $this->line(json_encode(array_values($routes)));
            }

            return;
        }

        $this->table($this->getHeaders(), $routes);
    }

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
        if (version_compare(\Illuminate\Foundation\Application::VERSION, '9.0.0', '>=')) {
            return collect(explode("\n", parent::getMiddleware($route)))->implode(',');
        } elseif ($this->option('middleware')) {
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
            foreach ($this->filterOptions() as $option => $filter) {
                if ($this->shouldFilterOption($option, $filteredRoute, $filter)) {
                    return;
                }
            }
        }

        return $filteredRoute;
    }

    protected function shouldFilterOption(string $option, array $route, $filter): bool
    {
        return
            ! $this->option($option)
         && ! $this->option('without-filter')
         && call_user_func(CompactRouteList::filterCallback(), $route, $filter);
    }

    protected function filterOptions(): array
    {
        return CompactRouteList::getFilters();
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        $options = [
            ['middleware', 'm', InputOption::VALUE_NONE, 'Use FQDN for middleware column (6.x - 8.x only)'],
        ];

        foreach ($this->filterOptions() as $option => $uriSegment) {
            $options[] = [$option, null, InputOption::VALUE_NONE, 'Include '.$option.' routes (filtered by default)'];
        }

        $options[] = ['without-filter', null, InputOption::VALUE_NONE, 'Do not filter routes containing '.collect($this->filterOptions())->keys()->join(', ', ' and ').'.'];

        // Add the extra options
        return array_merge(parent::getOptions(), $options);
    }
}
