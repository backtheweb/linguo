<?php

namespace Backtheweb\Linguo;

use Illuminate\Support\ServiceProvider;

class LinguoServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([__DIR__.'/../config/linguo.php' => config_path('linguo.php')], 'config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Backtheweb\Linguo\LoaderInterface', 'Backtheweb\Linguo\FileLoader');

        $this->app->singleton('linguo.loader', function ($app) {

            return new FileLoader($app['files'], $app['path.lang']);
        });

        $this->app->singleton('linguo', function ($app) {

            $loader     = $app['linguo.loader'];
            $locale     = $app['config']['app.locale'];
            $translator = new Translator($loader, $locale);

            $translator->setFallback($app['config']['app.fallback_locale']);

            return $translator;
        });


        /*
        if (file_exists($file = __DIR__ . '/' . 'helpers.php')) {
            require $file;
        }*/


        $this->app['router']->group([
            'namespace'  => '\Backtheweb\Linguo\Http\Controllers',
            'middleware' =>  [ 'web' ]
        ], function () {

            require __DIR__.'/routes/web.php';
        });

        $this->loadViewsFrom(__DIR__.'/views', 'linguo');

        $this->commands([\Backtheweb\Linguo\Command\ParseCommand::class]);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            Translator::class,
        ];
    }

}
