<?php

namespace GovindTomar\CrudGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use GovindTomar\CrudGenerator\Helpers\Helper;
use GovindTomar\CrudGenerator\Helpers\ModelHelper;
use GovindTomar\CrudGenerator\Helpers\ControllerHelper;
use GovindTomar\CrudGenerator\Helpers\ViewHelper;
use GovindTomar\CrudGenerator\Helpers\RequestHelper;

class AdminCrudGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:backend
                        {name : Class (singular), e.g User}
                        {--fields= : Field names for the form & migration.}
                        {--tables= : Tables for select option}
                        {--migration= : Tables for select option}
                        ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create CRUD operations';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');
        $fields = $this->option('fields');
        $tables = $this->option('tables');
        $migration = $this->option('migration');


        if(!file_exists(app_path('/Http/Controllers'.Helper::forslash().Helper::namespace()))){
            mkdir(app_path("/Http/Controllers".Helper::forslash().Helper::namespace()));
        }

        if(!file_exists(app_path('/Http/Requests'))){
            mkdir(app_path("/Http/Requests"));
        }

        if(!file_exists(app_path('/Models'))){
            mkdir(app_path("/Models"));
        }

        if(!file_exists(resource_path("views".Helper::forslash().Helper::path()))){
            mkdir(resource_path("views".Helper::forslash().Helper::path()));
        }

        File::deleteDirectory(resource_path("views".Helper::forslash().Helper::path()."/".Helper::getAddress($name)));
        File::delete(app_path("/Models/{$name}.php"));
        File::delete(app_path("/Http/Controllers".Helper::forslash().Helper::namespace()."/{$name}Controller.php"));
        File::delete(app_path("/Http/Requests/{$name}Request.php"));

        ControllerHelper::controller($name, $fields, $tables);

        RequestHelper::request($name, $fields, $tables);

        ModelHelper::model($name, $fields, $tables);

        ViewHelper::view($name, $fields);

        $this->routes($name);

        if ($migration == 'y' || $migration == 'yes') {
            $this->migration($name);
        }       

    }


    protected function migration($name)
    {
        $namePlural = strtolower(Str::plural($name));
        Artisan::call('make:migration create_' . $namePlural . '_table --create=' . $namePlural);
    }


    protected function routes($name)
    {
        $route_page = base_path('routes/web.php');
        
        File::append(base_path('routes/web.php'), 
            "\n\n // $name Controller Routes \n");

        // File::append($route_page, 
        //     "Route::group(['namespace' => '".Helper::namespace().Helper::backslash()"'], function(){\n");
        
        File::append($route_page, 
            "\tRoute::get('".Helper::path().Helper::forslash().Helper::getAddress($name)."/create', '".Helper::namespace().Helper::backslash().$name."Controller@create');\n");

        File::append($route_page, 
            "\tRoute::post('".Helper::path().Helper::forslash().Helper::getAddress($name)."', '".Helper::namespace().Helper::backslash().$name."Controller@store');\n");

        File::append($route_page, 
            "\tRoute::get('".Helper::path().Helper::forslash().Helper::getAddress($name)."', '".Helper::namespace().Helper::backslash().$name."Controller@index');\n");

        File::append($route_page, 
            "\tRoute::get('".Helper::path().Helper::forslash().Helper::getAddress($name)."/{id}', '".Helper::namespace().Helper::backslash().$name."Controller@show');\n");

        File::append($route_page, 
            "\tRoute::get('".Helper::path().Helper::forslash().Helper::getAddress($name)."/{id}/edit', '".Helper::namespace().Helper::backslash().$name."Controller@edit');\n");

        File::append($route_page, 
            "\tRoute::put('".Helper::path().Helper::forslash().Helper::getAddress($name)."', '".Helper::namespace().Helper::backslash().$name."Controller@update');\n");

        File::append($route_page, 
            "\tRoute::delete('".Helper::path().Helper::forslash().Helper::getAddress($name)."/{id}','".Helper::namespace().Helper::backslash().$name."Controller@destroy');\n");

        // File::append($route_page, 
        //     "});");
    }

}
