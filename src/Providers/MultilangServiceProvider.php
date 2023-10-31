<?php

namespace Juzaweb\Multilang\Providers;

use Illuminate\Routing\Router;
use Juzaweb\Multilang\Http\Middleware\Multilang;
use Juzaweb\CMS\Support\ServiceProvider;
use Juzaweb\Multilang\MultilangAction;

class MultilangServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        /** @var Router $router */
        $router = $this->app['router'];
        $router->pushMiddlewareToGroup('theme', Multilang::class);

        $this->registerHookActions([MultilangAction::class]);

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'mlla');
    }

    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }
}
