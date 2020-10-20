<?php

namespace DCC\CompactRouteList;

use Illuminate\Support\ServiceProvider;

class CompactRouteListServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->commands([
            CompactRouteListCommand::class,
        ]);
    }
}
