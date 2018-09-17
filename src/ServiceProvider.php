<?php

namespace JinDai\EasyExcel;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->singleton(EasyExcel::class, function () {
            return new EasyExcel();
        });

        $this->app->alias(EasyExcel::class, 'easyExcel');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [EasyExcel::class, 'easyExcel'];
    }
}
