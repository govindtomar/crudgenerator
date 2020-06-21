<?php

namespace GovindTomar\CrudGenerator\Helpers;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use GovindTomar\CrudGenerator\Helpers\Helper;

class ViewHelper{
	
/******************************************************************
    //  End Controller Code Generator
    //  Start View Code Generator
********************************************************************/

    public static function view($name, $fields)
    {
        mkdir(resource_path("views/".Helper::path().Helper::forslash().Helper::getAddress($name)));
        ViewHelper::indexBladePHP($name, $fields);
        ViewHelper::createBladePHP($name, $fields);
        ViewHelper::editBladePHP($name, $fields);
        ViewHelper::showBladePHP($name, $fields);

    }


/******************************************************************
    //  Start indexBladePHP Code Generator
********************************************************************/

    public static function indexBladePHP($name, $fields){
        $viewTemplate = str_replace(
            [
                '{{address}}',
                '{{modelNameSigularUpperCase}}',
                '{{modelNamePluralUpperCase}}',
                '{{modelNamePluralVar}}',
                '{{modelNameSingularVar}}',
                '{{tableHeadFields}}',
                '{{tableBodyFields}}',
                '{{layout}}',
                '{{bodyColClass}}',
                '{{path}}',
                '{{forslash}}'
            ],
            [
                Helper::getAddress($name),
                Helper::modelNameSigularUpperCase($name),
                Helper::modelNamePluralUpperCase($name),
                Helper::modelNamePluralVar($name),
                Helper::modelNameSingularVar($name),
                ViewHelper::tableHeadFields($name, $fields),
                ViewHelper::tableBodyFields($name, $fields),
                Helper::layout(),
                Helper::bodyColClass(),
                Helper::path(),
                Helper::forslash()

            ],
            Helper::getStub('views/index/index.blade')
        );
        $dir = Helper::getAddress($name);
        file_put_contents(resource_path("views/".Helper::path().Helper::forslash()."{$dir}/index.blade.php"), $viewTemplate);
    }

    public static function tableHeadFields($name, $fields){
        $fields = explode(',', $fields);
        $field_name = '';
        foreach ($fields as $field) {
            $var = explode('*', $field);
            $field_name = $field_name.'<th scope="col">'.ucfirst(str_replace("_"," ", $var[1])).'</th>';
            $field_name .= "\n\t\t\t\t\t\t\t\t\t";
        }
        return $field_name;
    }

    public static function tableBodyFields($name, $fields){
        $fields = explode(',', $fields);
        $field_name = '';
        foreach ($fields as $field) {
            $var = explode('*', $field);
            if ($var[0] == 'select') {
                $field_name = $field_name.'<td scope="col">{{ $'.Helper::modelNameSingularVar($name).'->'.$var[1].'->name }}</td>';
                $field_name .= "\n\t\t\t\t\t\t\t\t\t\t";
            }else{
                $field_name = $field_name.'<td scope="col">{{ $'.Helper::modelNameSingularVar($name).'->'.$var[1].' }}</td>';
                $field_name .= "\n\t\t\t\t\t\t\t\t\t\t";
            }
            
        }
        return $field_name;
    }


/******************************************************************
    //  Start showBladePHP Code Generator
********************************************************************/

    public static function showBladePHP($name, $fields){
        $fields = explode(',', $fields);
        $viewTemplate = '';
        foreach ($fields as $field) {

            $var = explode('*', $field);
            $type = $var[0];
            $input_name = $var[1];

            if ($type != 'select') {
                $viewTemplateReturn = ViewHelper::showField($name, $type, $input_name);
            }else{
                $viewTemplateReturn = ViewHelper::showFieldWithRelationship($name, $type, $input_name);
            }
            

            $viewTemplate = $viewTemplate . $viewTemplateReturn;
        }
        $viewTemplateStart = ViewHelper::show_start($name);
        $viewTemplateEnd = ViewHelper::show_end($name);

        $viewTemplate =    $viewTemplateStart.$viewTemplate.$viewTemplateEnd;

        $dir = Helper::getAddress($name);
        file_put_contents(resource_path("views/".Helper::path().Helper::forslash()."{$dir}/show.blade.php"), $viewTemplate);
    }

    public static function show_start($name){
        $viewTemplate = str_replace(
            [
                '{{address}}',
                '{{modelNameSigularUpperCase}}',
                '{{modelNamePluralUpperCase}}',
                '{{layout}}',
                '{{bodyColClass}}',
                '{{path}}',
                '{{forslash}}'
            ],
            [
                Helper::getAddress($name),
                Helper::modelNameSigularUpperCase($name),
                Helper::modelNamePluralUpperCase($name),
                Helper::layout(),
                Helper::bodyColClass(),
                Helper::path(),
                Helper::forslash()
            ],
            Helper::getStub('views/show/show_start.blade')
        );
        return $viewTemplate;
    }

    public static function show_end($name){
        $viewTemplate = str_replace(
            [
                '{{address}}',
                '{{modelNameSingularVar}}',
                '{{path}}',
                '{{forslash}}'
            ],
            [
                Helper::getAddress($name),
                Helper::modelNameSingularVar($name),
                Helper::path(),
                Helper::forslash()
            ],
            Helper::getStub('views/show/show_end.blade')
        );
        return $viewTemplate;
    }

    public static function showField($name, $type, $input_name){
        $viewTemplate = str_replace(
            [
                '{{modelNameSingularVar}}',
                '{{fieldNameSigularVar}}',
                '{{inputNameLabel}}'
            ],
            [
                Helper::modelNameSingularVar($name),
                Helper::fieldNameSigularVar($input_name),
                Helper::inputNameLabel($input_name)
            ],
            Helper::getStub("views/show/show.blade")
        );
        return $viewTemplate;
    }

    public static function showFieldWithRelationship($name, $type, $input_name){
        $viewTemplate = str_replace(
            [
                '{{modelNameSingularVar}}',
                '{{fieldNameSigularVar}}',
                '{{inputNameLabel}}'
            ],
            [
                Helper::modelNameSingularVar($name),
                Helper::fieldNameSigularVar($input_name),
                Helper::inputNameLabel($input_name)
            ],
            Helper::getStub("views/show/show_select.blade")
        );
        return $viewTemplate;
    }

/******************************************************************
    //  Start createBladePHP Code Generator
********************************************************************/

    public static function createBladePHP($name, $fields){
        $fields = explode(',', $fields);
        $viewTemplate = '';
        foreach ($fields as $field) {

            $var = explode('*', $field);
            $type = $var[0];
            $input_name = $var[1];


            $viewTemplateReturn = ViewHelper::createTextField($name, $type, $input_name);

            $viewTemplate = $viewTemplate . $viewTemplateReturn;
        }
        $viewTemplateStart = ViewHelper::create_start($name);
        $viewTemplateEnd = ViewHelper::create_end($name);

        $viewTemplate =    $viewTemplateStart.$viewTemplate.$viewTemplateEnd;

        $dir = Helper::getAddress($name);
        file_put_contents(resource_path("views/".Helper::path().Helper::forslash()."{$dir}/create.blade.php"), $viewTemplate);
    }


    public static function create_start($name){
        $viewTemplate = str_replace(
            [
                '{{address}}',
                '{{modelNameSigularUpperCase}}',
                '{{modelNamePluralUpperCase}}',
                '{{layout}}',
                '{{bodyColClass}}',
                '{{path}}',
                '{{forslash}}'

            ],
            [
                Helper::getAddress($name),
                Helper::modelNameSigularUpperCase($name),
                Helper::modelNamePluralUpperCase($name),
                Helper::layout(),
                Helper::bodyColClass(),
                Helper::path(),
                Helper::forslash()
            ],
            Helper::getStub('views/create/create_start.blade')
        );
        return $viewTemplate;
    }

    public static function create_end($name){
        $viewTemplate = str_replace(
            [
                '{{name}}',

            ],
            [
                $name,
            ],
            Helper::getStub('views/create/create_end.blade')
        );
        return $viewTemplate;
    }

    public static function createTextField($name, $type, $input_name){
        $viewTemplate = str_replace(
            [
                '{{fieldNameSigularVar}}',
                '{{fieldNamePluralVar}}',
                '{{type}}',
                '{{inputNameLabel}}',
                '{{modelNameSingularVar}}',
                '{{formColClass}}'                
            ],
            [
                Helper::fieldNameSigularVar($input_name),
                Helper::fieldNamePluralVar($input_name),
                $type,
                Helper::inputNameLabel($input_name),
                Helper::modelNameSingularVar($name),
                Helper::formColClass(),                
            ],
            Helper::getStub("views/create/{$type}.blade")
        );
        return $viewTemplate;
    }

/*****************************************************************
    //  Start editBladePHP Code Generator
*******************************************************************/

    public static function editBladePHP($name, $fields){
        $fields = explode(',', $fields);
        $viewTemplate = ViewHelper::editTextField($name, 'hidden', 'id');
        foreach ($fields as $field) {

            $var = explode('*', $field);
            $type = $var[0];
            $input_name = $var[1];


            $viewTemplateReturn = ViewHelper::editTextField($name, $type, $input_name);

            $viewTemplate = $viewTemplate . $viewTemplateReturn;
        }
        $viewTemplateStart = ViewHelper::edit_start($name);
        $viewTemplateEnd = ViewHelper::edit_end($name);

        $viewTemplate =    $viewTemplateStart.$viewTemplate.$viewTemplateEnd;

        $dir = Helper::getAddress($name);
        file_put_contents(resource_path("views/".Helper::path().Helper::forslash()."{$dir}/edit.blade.php"), $viewTemplate);
    }

    public static function editTextField($name, $type, $input_name){
        $viewTemplate = str_replace(
            [
                '{{fieldNameSigularVar}}',
                '{{fieldNamePluralVar}}',
                '{{type}}',
                '{{inputNameLabel}}',
                '{{modelNameSingularVar}}',
                '{{modelNamePluralVar}}',
                '{{formColClass}}'
            ],
            [
                Helper::fieldNameSigularVar($input_name),
                Helper::fieldNamePluralVar($input_name),
                $type,
                Helper::inputNameLabel($input_name),
                Helper::modelNameSingularVar($name),
                Helper::modelNamePluralVar($name),
                Helper::formColClass()
                
            ],
            Helper::getStub("views/edit/{$type}.blade")
        );
        return $viewTemplate;
    }

    public static function edit_start($name){
        $viewTemplate = str_replace(
            [
                '{{address}}',
                '{{modelNameSigularUpperCase}}',
                '{{modelNamePluralUpperCase}}',
                '{{layout}}',
                '{{bodyColClass}}',
                '{{path}}',
                '{{forslash}}'

            ],
            [
                Helper::getAddress($name),
                Helper::modelNameSigularUpperCase($name),
                Helper::modelNamePluralUpperCase($name),
                Helper::layout(),
                Helper::bodyColClass(),
                Helper::path(),
                Helper::forslash()
            ],
            Helper::getStub('views/edit/edit_start.blade')
        );
        return $viewTemplate;
    }

    public static function edit_end($name){
        $viewTemplate = str_replace(
            [
                '{{name}}',

            ],
            [
                $name,
            ],
            Helper::getStub('views/edit/edit_end.blade')
        );
        return $viewTemplate;
    }
	
}