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
    protected $signature = 'make:crud
                        {name : Class (singular), e.g User}
                        {--fields= : Field names for the form & migration.}
                        {--model= : Tables for select option}
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
        $tables = $this->option('model');
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

        if (Helper::code_type() != 'API') { 

            if(!file_exists(resource_path("views".Helper::forslash().Helper::path()))){
                mkdir(resource_path("views".Helper::forslash().Helper::path()));
            }

            File::deleteDirectory(resource_path("views".Helper::forslash().Helper::path()."/".Helper::getAddress($name)));

            ViewHelper::view($name, $fields);
        }

        

        File::delete(app_path("/Models/{$name}.php"));
        File::delete(app_path("/Http/Controllers".Helper::forslash().Helper::namespace()."/{$name}Controller.php"));
        File::delete(app_path("/Http/Requests/{$name}Request.php"));

        ControllerHelper::controller($name, $fields, $tables);

        RequestHelper::request($name, $fields, $tables);

        ModelHelper::model($name, $fields, $tables);

        if (Helper::code_type() != 'API') {
            $this->routes($name, $fields);
        }else{
            $this->api_routes($name, $fields);
        }
        

        if ($migration == 'y' || $migration == 'yes') {
            $this->migration($name);
        }       

    }


    protected function migration($name)
    {
        $namePlural = strtolower(Str::plural($name));
        Artisan::call('make:migration create_' . $namePlural . '_table --create=' . $namePlural);
    }


    protected function routes($name, $fields)
    {
        $route_page = base_path('routes/web.php');
        
        File::append(base_path('routes/web.php'), 
            "\n\n // $name Controller Routes \n");


        // use App\Http\Controllers\Admin\PostController;

        File::append($route_page, 
            "use App\Http\Controllers".Helper::backslash().Helper::namespace().Helper::backslash().$name."Controller;\n");
        File::append($route_page, 
            "Route::group(['prefix' => '".Helper::path()."', 'as' => '".Helper::path().".'], function(){\n");

        File::append($route_page, 
            "\tRoute::resource('".Helper::getAddress($name)."', ".$name."Controller::class);\n");
        $fields = explode(',', $fields);
        foreach ($fields as $field) {
            $var = explode('*', $field);
            if($var[0] == 'toggle'){
                File::append($route_page, "\tRoute::post('".Helper::getAddress($name)."/".$var[1]."', [".$name."Controller::class, '".$var[1]."'])->name('".Helper::getAddress($name).".".$var[1]."');\n");
            }
        }
        File::append($route_page, 
            "});");
        // File::append($route_page, 
        //     "\tRoute::get('".Helper::path().Helper::forslash().Helper::getAddress($name)."/create', '".Helper::namespace().Helper::backslash().$name."Controller@create')->name('".Helper::path().".".Helper::getAddress($name)."."."create');\n");

        // File::append($route_page, 
        //     "\tRoute::post('".Helper::path().Helper::forslash().Helper::getAddress($name)."', '".Helper::namespace().Helper::backslash().$name."Controller@store')->name('".Helper::path().".".Helper::getAddress($name)."."."store');\n");

        // File::append($route_page, 
        //     "\tRoute::get('".Helper::path().Helper::forslash().Helper::getAddress($name)."', '".Helper::namespace().Helper::backslash().$name."Controller@index')->name('".Helper::path().".".Helper::getAddress($name)."."."index');\n");

        // File::append($route_page, 
        //     "\tRoute::get('".Helper::path().Helper::forslash().Helper::getAddress($name)."/{id}', '".Helper::namespace().Helper::backslash().$name."Controller@show')->name('".Helper::path().".".Helper::getAddress($name)."."."show');\n");

        // File::append($route_page, 
        //     "\tRoute::get('".Helper::path().Helper::forslash().Helper::getAddress($name)."/{id}/edit', '".Helper::namespace().Helper::backslash().$name."Controller@edit')->name('".Helper::path().".".Helper::getAddress($name)."."."edit');\n");

        // File::append($route_page, 
        //     "\tRoute::put('".Helper::path().Helper::forslash().Helper::getAddress($name)."', '".Helper::namespace().Helper::backslash().$name."Controller@update')->name('".Helper::path().".".Helper::getAddress($name)."."."update');\n");

        // File::append($route_page, 
        //     "\tRoute::delete('".Helper::path().Helper::forslash().Helper::getAddress($name)."/{id}','".Helper::namespace().Helper::backslash().$name."Controller@destroy')->name('".Helper::path().".".Helper::getAddress($name)."."."destroy');\n");

        
    }


    protected function api_routes($name, $fields)
    {
        $route_page = base_path('routes/api.php');
        
        File::append(base_path('routes/web.php'), 
            "\n\n // $name Controller Routes \n");

        File::append($route_page, 
            "Route::group(['prefix' => '".Helper::path()."', 'as' => '".Helper::path().".', 'namespace' => '".Helper::namespace()."'], function(){\n");

        File::append($route_page, 
            "\tRoute::resource('".Helper::getAddress($name)."', '".$name."Controller');\n");
        $fields = explode(',', $fields);
        foreach ($fields as $field) {
            $var = explode('*', $field);
            if($var[0] == 'toggle'){
                File::append($route_page, "\tRoute::post('".Helper::getAddress($name)."/".$var[1]."', '".$name."Controller@".$var[1]."')->name('".Helper::getAddress($name).".".$var[1]."');\n");
            }
        }
        File::append($route_page, 
            "});");
       
    }

}
