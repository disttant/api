<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        

        $this->mapV1Routes();

        $this->mapV2Routes();

        $this->mapCustomFallbackRoutes();

        //$this->mapWebRoutes();

        //
    }

    

    

    /**
     * Define the "v1" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapV1Routes()
    {
        Route::prefix('v1')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/v1.php'));
    }
    
    /**
     * Define the "v2" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapV2Routes()
    {
        Route::prefix('v2')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/v2.php'));
    }

    /**
     * Define the "custom fallback" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapCustomFallbackRoutes()
    {
        Route::middleware('customfallback')
            ->namespace($this->namespace)
            ->group(base_path('routes/customfallback.php'));
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }
    
}
