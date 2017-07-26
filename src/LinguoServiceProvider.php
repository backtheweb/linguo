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

        $this->app->singleton('linguo.loader', function ($app) {

            return new FileLoader($app['files'], $app['path.lang']);
        });

        $this->app->singleton('linguo', function ($app) {


            $config  = $app['config'];
            $options = [

                'locale'    => $config['app.locale'],
                'fallback'  => $config['app.fallback_locale'],
                'domain'    => $config['linguo']['domain'],
                'domains'   => $config['linguo']['domains'],
            ];

            $linguo = new Linguo($app['linguo.loader'], $options);

            return $linguo;
        });


        $this->commands([\Backtheweb\Linguo\Command\ParseCommand::class]);
        //$this->commands([\Backtheweb\Linguo\Command\PoToPhpCommand::class]);

        //$this->enableUi();

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            Linguo::class,
        ];
    }


    public function enableUi()
    {
        $this->app['router']->group([

            'namespace'  => '\Backtheweb\Linguo\Http\Controllers',
            'middleware' =>  [ 'web', 'auth' ]

        ], function () {

            require __DIR__.'/routes/web.php';
        });

        $this->loadViewsFrom(__DIR__.'/views', 'linguo');
    }

}
