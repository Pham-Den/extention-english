<?php

namespace Modules\App;

use App\Actions\ActionHandler;
use App\Providers\AppServiceProvider;
use Plugins\ModelAction\ModelActions;

class ServiceProvider extends AppServiceProvider
{
    protected $middleware = [];
    protected $routePrefix = '/';

    /**
     * Register any application services.
     */
    public function register(): void
    {
        parent::register();
    }

    public function boot(): void
    {

        parent::boot();
        $modelActions = [];

        $pluginActions = [];
        $pluginActions[] = include(base_path('Plugins/Audio/Actions/actions.php'));

        foreach ($pluginActions as $pluginAction) {
            foreach ($pluginAction as $prefix => $config) {
                $modelActions[$prefix] = $config;
            }
        }

        $actionHandler = new ActionHandler();
        foreach ($modelActions as $prefix => $config) {
            $actionHandler->register($prefix, ModelActions::createActions($prefix, $config), $config['permissions'] ?? [],$config);
        }

        \Log::info('boot called at ' . now());
        $actionHandler->middlewares(['web'])->bindRoute('/app/action/{prefix}/{action}/{id?}');

    }
}
