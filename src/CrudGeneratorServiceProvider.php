<?php

namespace GovindTomar\CrudGenerator;

use Illuminate\Support\ServiceProvider;
use GovindTomar\CrudGenerator\Commands\BackendCrudGenerator;

class CrudGeneratorServiceProvider extends ServiceProvider
{
	public function boot(){
		$this->loadRoutesFrom(__DIR__.'/routes.php');
        // $this->loadViewsFrom(__DIR__.'views', 'contact');
        if ($this->app->runningInConsole()) {
            $this->commands([
                BackendCrudGenerator::class,
                // BarCommand::class,
            ]);
        }
	}

	public function register(){

	}
}
