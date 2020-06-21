<?php

namespace GovindTomar\CrudGenerator\Helpers;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ModelHelper{


	public static function model($name, $fields, $tables)
    {
        $modelTemplate = str_replace(
            [
                '{{modelName}}',
                '{{modelField}}',
                '{{getTableName}}',
                '{{modelRelationship}}'
            ],
            [
                $name,
                ModelHelper::getModelFields($fields),
                Helper::getTableName($name),
                ModelHelper::get_retation_to_model($tables),
            ],
            Helper::getStub('models/Model')
        );

        file_put_contents(app_path("Models/{$name}.php"), $modelTemplate);

        if ($tables != null) {
            $tables = explode(',', $tables);
            $relation = '';
            foreach ($tables as $key => $table) {
                ModelHelper::relation_model($name, $table);
            }
        }
    }

    public static function get_retation_to_model($tables){
        if ($tables != null) {
            $tables = explode(',', $tables);
            $relation = '';
            foreach ($tables as $key => $table) {
                $relation .= "public function ". Helper::modelNameSingularVar($table)."(){\n\t\t"."return _____this->belongsTo({$table}::class);\n\t"."}";
            }
        return str_replace('_____', '$', $relation);
        }
    } 


    public static function getModelFields($fields){
        $fields = explode(',', $fields);

        $field_name = '[';
        foreach ($fields as $field) {
            $var = explode('*', $field);
            if ($var[0] == 'select') {
                $field_name = $field_name ."'". $var[1]."_id',";
            }else{
                $field_name = $field_name ."'". $var[1]."',";
            }
        }
        $field_name = $field_name.']';
        return $field_name;
    }


    public static function relation_model($name, $table){
        
        $relation = "\tpublic function ". Helper::modelNamePluralVar($name)."(){\n\t\t"."return _____this->hasMany({$name}::class);\n\t"."}\n\n}";

        $content = str_replace('_____', '$', $relation);
        // dd(app_path("Models/{$table}.php"));
        if(file_exists(app_path("Models/{$table}.php"))){
            $old_content = file_get_contents(app_path("Models/{$table}.php"));
            file_put_contents(app_path("Models/{$table}.php"), substr($old_content, 0, -1).$content);
        }
        
    }

}
