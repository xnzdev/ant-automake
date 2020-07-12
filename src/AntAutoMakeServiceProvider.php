<?php

namespace XnzDev\AntAutoMake;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class AntAutoMakeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('xnz', function () {
            return new Test();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__."/routes/web.php");
        $this->loadViewsFrom(__DIR__.'/react_views/', 'xnz_ant');
        $this->loadViewsFrom(__DIR__.'/stub/ant_v4/', 'ant');
        $this->publishes([
            __DIR__.'/react_views/static' => public_path('/xnzdev/ant/static'),
        ], 'xnzdev/antd-automake');
    }
}
