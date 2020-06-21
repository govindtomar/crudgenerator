<?php

namespace GovindTomar\CrudGenerator;

use Illuminate\Support\ServiceProvider;
use GovindTomar\CrudGenerator\Commands\AdminCrudGenerator;

class CrudGeneratorServiceProvider extends ServiceProvider
{
	public function boot(){
		$this->loadRoutesFrom(__DIR__.'/routes.php');
        $this->mergeConfigFrom(__DIR__.'/config/crud.php', 'crud');

        $this->publishes([__DIR__.'/config/crud.php' => config_path('crud.php')], 'config');

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
