<?php

namespace Juzaweb\Multilang\Providers;

use Illuminate\Http\Request;
use Juzaweb\CMS\Facades\ActionRegister;
use Juzaweb\Multilang\Http\Middleware\MultipleLanguage;
use Juzaweb\CMS\Support\ServiceProvider;
use Juzaweb\Multilang\MultilangAction;

class MultilangServiceProvider extends ServiceProvider
{
    public function boot()
    {
        ActionRegister::register([MultilangAction::class]);
        
        /** @var \Illuminate\Routing\Router $router */
        $router = $this->app['router'];
        $router->pushMiddlewareToGroup('theme', MultipleLanguage::class);
    
        /**
         * @var Request $request
         */
        //$request = $this->app['request'];
        
        //$config = $this->app['config'];
        //$config->set('theme.route_prefix', '{locale?}');
    }
    
    public function register()
    {
        //
    }
}
