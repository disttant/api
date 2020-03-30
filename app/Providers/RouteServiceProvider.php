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

        // Defining globally route parameters
        Route::pattern('node', '[0-9]+');
        Route::pattern('userId', '[0-9]+');
        Route::pattern('device', '[a-z0-9]{1,30}');
        Route::pattern('group', '[a-z0-9]{1,30}');
        Route::pattern('number', '[0-9]+');

        parent::boot();
    }



    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        
        $this->mapApiRoutes();

        $this->mapCustomFallbackRoutes();

        //$this->mapWebRoutes();

    }

    

    /**
     * Define the "v1" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
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
    /*
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }
    */
    
}
