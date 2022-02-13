<?php

namespace GovindTomar\CrudGenerator\Helpers;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use GovindTomar\CrudGenerator\Helpers\Helper;

class ActionHelper{
	
    public static function migration($name, $fields, $tables)
    {
        ActionHelper::delete_current_table($name);

        MigrationHelper::migration($name, $fields, $tables); 
    }


    public static function routes($name, $fields)
    {
        $route_page = base_path('routes/web.php');

        $route_exists = ActionHelper::string_match($route_page, "$name Controller Routes");

        if($route_exists == 0){
            File::append($route_page, "\n\n // $name Controller Routes \n");        

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
            File::append($route_page, "});");
        }
        
    }


    public static function api_routes($name, $fields)
    {
        $route_page = base_path('routes/api.php');

        $route_exists = ActionHelper::string_match($route_page, "$name Controller Routes");

        if($route_exists == 0){
            File::append(base_path('routes/web.php'), "\n\n // $name Controller Routes \n");

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
            File::append($route_page, "});");
        }
       
    }

    public static function delete_current_table($name){  
        $migration_old = database_path("migrations");
        $migrationFiles = File::glob($migration_old.'/*.php');

        foreach ($migrationFiles as $migrationFile) {
            $matches = Str::is('*_create_'.Helper::modelNamePluralVar($name).'_table.php', $migrationFile);

            if ($matches == true) {
                File::delete($migrationFile);
            }
        }      
    } 

    public static function string_match($file, $text){
        $contents = file_get_contents($file);
        $pattern = preg_quote($text, '/');
        $pattern = "/^.*$pattern.*\$/m";
        if(preg_match_all($pattern, $contents, $matches)){
            return true;
        }
        else{
            return false;
        }
    }

}