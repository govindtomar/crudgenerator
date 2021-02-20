<?php

namespace GovindTomar\CrudGenerator\Helpers;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use GovindTomar\CrudGenerator\Helpers\Helper;

class ControllerHelper{
	
	public static function controller($name, $fields, $tables)
    {
        if (isset($tables)) {
            $controllerTemplate = str_replace(
                [
                    '{{modelName}}',
                    '{{tableName}}',
                    '{{address}}',
                    '{{modelNameSingularVar}}',
                    '{{modelNamePluralVar}}',
                    '{{modelNameSingularLowerCase}}',
                    '{{createControllerFields}}',
                    '{{extraTables}}',
                    '{{getCompact}}',
                    '{{indexWithModel}}',
                    '{{joinSelectTables}}',
                    '{{namespace}}',
                    '{{path}}',
                    '{{backslash}}',
                    '{{forslash}}',
                    '{{relativeModel}}',
                    '{{createExtraFunctionForToggle}}'

                ],
                [
                    $name,
                    Helper::getTableName($name),
                    Helper::getAddress($name),
                    Helper::modelNameSingularVar($name),
                    Helper::modelNamePluralVar($name),
                    Helper::modelNameSingularLowerCase($name),
                    ControllerHelper::getControllerFields($name, $fields),
                    ControllerHelper::getExtraTables($tables),
                    ControllerHelper::getCompactFunction($tables),
                    ControllerHelper::indexWithModel($tables),
                    ControllerHelper::joinSelectTables($name, $tables),
                    Helper::namespace(),
                    Helper::path(),
                    Helper::backslash(),
                    Helper::forslash(),
                    ControllerHelper::relativeModel($tables),
                    ControllerHelper::createExtraFunctionForToggle($name, $fields)

                ],
                Helper::getStub('controllers/CompactController')
            );

            file_put_contents(app_path("/Http/Controllers/".Helper::namespace()."/{$name}Controller.php"), $controllerTemplate);
        }
        else{
            $controllerTemplate = str_replace(
                [
                    '{{modelName}}',
                    '{{tableName}}',
                    '{{address}}',
                    '{{modelNameSingularVar}}',
                    '{{modelNamePluralVar}}',
                    '{{modelNamePluralLowerCase}}',
                    '{{modelNameSingularLowerCase}}',
                    '{{createControllerFields}}',
                    '{{namespace}}',
                    '{{path}}',
                    '{{backslash}}',
                    '{{forslash}}',
                    '{{createExtraFunctionForToggle}}'
                ],
                [
                    $name,
                    Helper::getTableName($name),
                    Helper::getAddress($name),
                    Helper::modelNameSingularVar($name),
                    Helper::modelNamePluralVar($name),
                    Helper::modelNamePluralLowerCase($name),
                    Helper::modelNameSingularLowerCase($name),
                    ControllerHelper::getControllerFields($name, $fields),
                    Helper::namespace(),
                    Helper::path(),
                    Helper::backslash(),
                    Helper::forslash(),
                    ControllerHelper::createExtraFunctionForToggle($name, $fields)
                ],
                Helper::getStub('controllers/Controller')
            );

            file_put_contents(app_path("/Http/Controllers/".Helper::namespace()."/{$name}Controller.php"), $controllerTemplate);

        }
    }

    public static function getControllerFields($name, $fields){
        $fields = explode(',', $fields);
        $field_name = '';
        foreach ($fields as $field) {
            $var = explode('*', $field);
            if ($var[0] == 'select') {
                $field_name .= '_____'.Helper::modelNameSingularVar($name)."->".$var[1]."_id  =  _____request->{$var[1]}_id;\n\t\t\t";
            }
            else if($var[0] == 'image'){
                $field_name .=  "if(_____request->".$var[1]." != ''){\n\t\t\t\t";
                $field_name .= '_____'.Helper::modelNameSingularVar($name)."->".$var[1]."  = CRUDHelper::file_upload(_____request->".$var[1].", 'uploads/user-'.Auth::id());\n\t\t\t";
                $field_name .=  "}\n\t\t\t";
            }
            else if($var[0] == 'toggle'){

            }
            else{
                $field_name .= '_____'.Helper::modelNameSingularVar($name)."->".$var[1]."  =  _____request->$var[1];\n\t\t\t";
            }

        }
        return str_replace('_____', '$', substr($field_name, 0, -4));
    }

    public static function indexWithModel($tables){
        $tables = explode(',', $tables);
        $table_name = '';
        foreach ($tables as $key => $table) {
            $table_name .=  "'".Helper::modelNameSingularLowerCase($table)."',";
        }
        return substr($table_name, 0, -1);
    }

    public static function joinSelectTables($name, $tables){
        $tables = explode(',', $tables);
        $table_name = "->select('".Helper::modelNamePluralVar($name).".*'";
        foreach ($tables as $key => $table) {
        $table_select = ", '".Helper::modelNamePluralVar($table).".name as"." ".Helper::modelNameSingularVar($table)."'";
        $table_name = $table_name.$table_select;
        }
        $table_name = $table_name.")" ;
        return $table_name;
    }

    public static function createExtraFunctionForToggle($name, $fields){
        $fields = explode(',', $fields);
        $field_name = "\n";
        foreach ($fields as $field) {
            $var = explode('*', $field);
            if($var[0] == 'toggle'){
                $field_name .= str_replace(
                    [
                        '{{name}}',
                        '{{fieldNameSigularVar}}',
                        '{{modelNameSingularVar}}'

                    ],
                    [
                        $name,
                        Helper::fieldNameSigularVar($var[1]),
                        Helper::modelNameSingularVar($name),
                    ],
                    Helper::getStub('controllers/create-function-for-toggle.blade')
                );
            }
        }
        return $field_name;
    }

    public static function getExtraTables($tables){
        $tables = explode(',', $tables);
        $table_name = '';
        foreach ($tables as $key => $table) {
            $table_new_name = Helper::getTableName($table);
            $table_name = $table_name . "$".$table_new_name." = ".$table."::all();\n\t\t\t";

        }
        return substr($table_name, 0, -4);
    }

    public static function getCompactFunction($tables){
        $tables = explode(',', $tables);
        $compact = '';
        foreach ($tables as $key => $table) {
            $table = Helper::getTableName($table);
            $compact = $compact .'"'. $table.'",';

        }

        return rtrim($compact, ",");
    }

    public static function relativeModel($tables){
        $tables = explode(',', $tables);
        $relate = '';
        foreach ($tables as $key => $table) {
           $relate .= 'use App\Models'.Helper::backslash().$table.';'; 
        }
        return $relate;
    }

}