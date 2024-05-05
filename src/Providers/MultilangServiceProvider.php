<?php

namespace Juzaweb\Multilang\Providers;

use Illuminate\Routing\Router;
use Juzaweb\Backend\Models\Post;
use Juzaweb\Backend\Models\Taxonomy;
use Juzaweb\CMS\Facades\MacroableModel;
use Juzaweb\CMS\Models\Model;
use Juzaweb\Multilang\Http\Middleware\Multilang;
use Juzaweb\CMS\Support\ServiceProvider;
use Juzaweb\Multilang\Models\PostTranslation;
use Juzaweb\Multilang\Models\TaxonomyTranslation;
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

        $this->addTaxonomyRelationship();
    }

    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);

        $this->app->singleton(
            Locale::class,
            fn ($app) => new \Juzaweb\Multilang\Locale($app)
        );
    }

    protected function addTaxonomyRelationship(): void
    {
        MacroableModel::addMacro(
            Taxonomy::class,
            'translations',
            function () {
                /** @var Model $this */
                return $this->hasMany(TaxonomyTranslation::class, 'taxonomy_id', 'id');
            }
        );

        MacroableModel::addMacro(
            Post::class,
            'translations',
            function () {
                /** @var Model $this */
                return $this->hasMany(PostTranslation::class, 'post_id', 'id');
            }
        );
    }
}
