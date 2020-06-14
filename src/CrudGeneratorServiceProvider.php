<?php

namespace GovindTomar\CrudGenerator;

use Illuminate\Support\ServiceProvider;
use GovindTomar\CrudGenerator\Commands\AdminCrudGenerator;

class CrudGeneratorServiceProvider extends ServiceProvider
{
	public function boot(){
		$this->loadRoutesFrom(__DIR__.'/routes.php');
        // $this->loadViewsFrom(__DIR__.'views', 'contact');
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
