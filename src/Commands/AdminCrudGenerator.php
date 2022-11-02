<?php

namespace GovindTomar\CrudGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use GovindTomar\CrudGenerator\Helpers\Helper;
use GovindTomar\CrudGenerator\Helpers\ModelHelper;
use GovindTomar\CrudGenerator\Helpers\ControllerHelper;
use GovindTomar\CrudGenerator\Helpers\ControllerApiHelper;
use GovindTomar\CrudGenerator\Helpers\ViewHelper;
use GovindTomar\CrudGenerator\Helpers\RequestHelper;
use GovindTomar\CrudGenerator\Helpers\ActionHelper;

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
                        {--delete= : Delete CRUD files}
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
        $delete = $this->option('delete');

        if ($delete == 'y' || $delete == 'yes') {
            File::delete(app_path("/Models/{$name}.php"));
            File::delete(app_path("/Http/Controllers".Helper::forslash().Helper::namespace()."/{$name}Controller.php"));
            File::delete(app_path("/Http/Requests".Helper::forslash().Helper::namespace()."/{$name}Request.php"));
            ActionHelper::delete_current_table($name);
            
            if (Helper::code_type() != 'API') { 
                File::deleteDirectory(resource_path("views".Helper::forslash().Helper::path()."/".Helper::getAddress($name)));
            }
            return false;
        }  

        if(!file_exists(app_path('/Http/Controllers'.Helper::forslash().Helper::namespace()))){
            mkdir(app_path("/Http/Controllers".Helper::forslash().Helper::namespace()));
        }

        if(!file_exists(app_path('/Http/Requests'.Helper::forslash().Helper::namespace()))){
            mkdir(app_path("/Http/Requests".Helper::forslash().Helper::namespace()));
        }

        if(!file_exists(app_path('/Models'))){
            mkdir(app_path("/Models"));
        }

        File::delete(app_path("/Http/Controllers".Helper::forslash().Helper::namespace()."/{$name}Controller.php"));

        if (Helper::code_type() != 'API') { 

            if(!file_exists(resource_path("views".Helper::forslash().Helper::path()))){
                mkdir(resource_path("views".Helper::forslash().Helper::path()));
            }

            File::deleteDirectory(resource_path("views".Helper::forslash().Helper::path()."/".Helper::getAddress($name)));

            ViewHelper::view($name, $fields);


            ControllerHelper::controller($name, $fields, $tables);
        }else{
            ControllerApiHelper::controller($name, $fields, $tables);
        }

        

        File::delete(app_path("/Models/{$name}.php"));
        ModelHelper::model($name, $fields, $tables);

        File::delete(app_path("/Http/Requests/".Helper::forslash().Helper::namespace()."/{$name}Request.php"));
        RequestHelper::request($name, $fields, $tables);
    

        if (Helper::code_type() != 'API') {
            ActionHelper::routes($name, $fields);
        }else{
            ActionHelper::api_routes($name, $fields);
        }
        
        if ($migration == 'y' || $migration == 'yes') {
            ActionHelper::migration($name, $fields, $tables);
        }       

    }


}
