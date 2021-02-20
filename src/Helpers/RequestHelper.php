<?php

namespace GovindTomar\CrudGenerator\Helpers;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use GovindTomar\CrudGenerator\Helpers\Helper;

class RequestHelper{
	
	public static function request($name, $fields, $tables)
    {
            $requestTemplate = str_replace(
                [
                    '{{modelName}}',
                    '{{controllerValidation}}',

                ],
                [
                    $name,
                    RequestHelper::controllerValidation($name, $fields),

                ],
                Helper::getStub('requests/Request')
            );

            file_put_contents(app_path("/Http/Requests/{$name}Request.php"), $requestTemplate);
       
    }

    public static function controllerValidation($name, $fields){
        $fields = explode(',', $fields);
        $field_name = '';
        foreach ($fields as $field) {
            $var = explode('*', $field);
            if ($var[0] == 'select') {
                $field_name = $field_name."'".$var[1]."_id'  =>  'required',\n\t\t\t";
            }
            else if ($var[0] == 'image') {
                $field_name = $field_name."'".$var[1]."'  =>  '',\n\t\t\t";
            }
            else if ($var[0] == 'toggle') {
                $field_name = $field_name."'".$var[1]."'  =>  '',\n\t\t\t";
            }
            else{
               $field_name = $field_name."'".$var[1]."'  =>  'required',\n\t\t\t";
            }

        }
        return substr($field_name, 0, -4);
    }

}