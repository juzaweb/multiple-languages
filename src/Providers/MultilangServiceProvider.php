<?php

namespace Juzaweb\Multilang\Providers;

use Juzaweb\Multilang\Http\Middleware\MultipleLanguage;
use Juzaweb\CMS\Support\ServiceProvider;

class MultilangServiceProvider extends ServiceProvider
{
    public function boot()
    {
        /** @var \Illuminate\Routing\Router $router */
        $router = $this->app['router'];
        $router->pushMiddlewareToGroup('theme', MultipleLanguage::class);
        
        $config = $this->app['config'];
        $config->set('theme.route_prefix', '{locale}');
    }
}
