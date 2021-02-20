<?php

namespace GovindTomar\CrudGenerator;

use Illuminate\Support\ServiceProvider;
use GovindTomar\CrudGenerator\Commands\AdminCrudGenerator;

class CrudGeneratorServiceProvider extends ServiceProvider
{
	public function boot(){
		$this->loadRoutesFrom(__DIR__.'/routes/web.php');

        $this->loadViewsFrom(__DIR__.'/views', 'views');

        $this->mergeConfigFrom(__DIR__.'/config/crud.php', 'crud');

        $this->publishes([__DIR__.'/config/crud.php' => config_path('crud.php')], 'config');
        $this->publishes([__DIR__.'/views' => resource_path('views')], 'views');

        if ($this->app->runningInConsole()) {
            $this->commands([
                AdminCrudGenerator::class,
                // BarCommand::class,
            ]);
        }
	}

	public function register(){

	}
}

// "autoload-dev": {
//     "psr-4": {
//         "Tests\\": "tests/",
//         "GovindTomar\\CrudGenerator\\": "package/govindtomar/crudgenerator/src/"
//     }
// },
