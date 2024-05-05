<?php

namespace Juzaweb\Multilang\Providers;

use Illuminate\Routing\Router;
use Juzaweb\Multilang\Http\Middleware\Multilang;
use Juzaweb\CMS\Support\ServiceProvider;
use Juzaweb\Multilang\MultilangAction;
use Juzaweb\Multilang\Contracts\Locale;

class MultilangServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        /** @var Router $router */
        $router = $this->app['router'];
        $router->pushMiddlewareToGroup('theme', Multilang::class);

        $this->registerHookActions([MultilangAction::class]);

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'mlla');

        $this->app['config']->set('theme.route_prefix', \Juzaweb\Multilang\Facades\Locale::setLocale());
    }

    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);

        $this->app->singleton(
            Locale::class,
            fn ($app) => new \Juzaweb\Multilang\Locale($app)
        );
    }
}
