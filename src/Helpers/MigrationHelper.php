<?php

namespace GovindTomar\CrudGenerator\Helpers;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use GovindTomar\CrudGenerator\Helpers\Helper;

class MigrationHelper{

    public static function migration($name, $fields, $tables)
    {
            $requestTemplate = str_replace(
                [
                    '{{modelNamePluralUpperCase}}',
                    '{{modelNamePluralVar}}',
                    '{{getMigrationFields}}'
                ],
                [
                    str_replace(" ","", Helper::modelNamePluralUpperCase($name)),
                    Helper::modelNamePluralVar($name),
                    MigrationHelper::getMigrationFields($name, $fields, $tables)
                ],
                Helper::getStub('migrations/Migration')
            );

            file_put_contents(database_path("/migrations"."/".date('Y_m_d_His')."_create_".Helper::modelNamePluralVar($name)."_table.php"), $requestTemplate);

    }


    public static function getMigrationFields($name, $fields, $tables){
        $fields = explode(',', $fields);
        $tables = explode(',', $tables);
        $field_name = '';

        foreach ($fields as $field) {
            $var = explode('*', $field);
            if ($var[0] == 'select') {
                if (isset($tables)) {
                    foreach ($tables as $key => $table) {
                        // dd($var[1] ."||||". Helper::modelNameSingularVar($table));
                        if($var[1] == Helper::modelNameSingularVar($table)){
                            $field_name .= "_____table->unsignedBigInteger('".Helper::modelNameSingularVar($table)."_id');\n\t\t\t";
                            $field_name .= "_____table->foreign('".Helper::modelNameSingularVar($table)."_id')->references('id')->on('".Helper::getTableName($table)."')->onDelete('cascade');\n\t\t\t";
                        }
                    }
                }

            }
            else if($var[0] == 'toggle'){
                $field_name .= "_____table->tinyInteger('$var[1]')->default(1);\n\t\t\t";
            }else{
                $field_name .= "_____table->string('$var[1]');\n\t\t\t";
            }
        }

        // if (isset($tables)) {
        //     foreach ($tables as $key => $table) {
        //         $field_name .= "_____table->unsignedBigInteger('".Helper::modelNameSingularVar($table)."_id');\n\t\t\t";
        //         $field_name .= "_____table->foreign('".Helper::modelNameSingularVar($table)."_id')->references('id')->on('".Helper::getTableName($table)."')->onDelete('cascade');\n\t\t\t";
        //     }
        // }


        return str_replace('_____', '$', substr($field_name, 0, -4));
    }

}
