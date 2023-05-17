<?php

namespace Saidy\VoyagerSurvey\Providers;

use TCG\Voyager\Facades\Voyager;
use Illuminate\Support\ServiceProvider;
use Saidy\VoyagerSurvey\Actions\SurveyAction;


class VoyagerSurveyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Voyager::addAction(SurveyAction::class);
        $this->registerConfigs();

        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'saidy-voyager-survey');
        $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');
        $this->loadMigrationsFrom(realpath(__DIR__.'/../../migrations/'));



        $this->publishes([
            __DIR__ . '/../../resources/views'                => resource_path('views/vendor/saidy-voyager-survey'),
            __DIR__ . '/../../resources/views/bread/partials' => resource_path('views/vendor/voyager/bread/partials'),
        ], 'views');

        $this->publishes([
            __DIR__ . '/../../migrations/' => database_path('migrations/migrations'),
        ], 'saidy-voyager-migrations');

        $this->publishes([
            __DIR__ .'/../../config/voyager-survey.php' => config_path('voyager/survey.php'),
        ], 'saidy-voyager-config');
    }

    public function registerConfigs()
    {
        $this->mergeConfigFrom(
            __DIR__ .'/../../config/voyager-survey.php',
            'voyager.survey'
        );
    }
}
