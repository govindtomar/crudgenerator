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
                '{{forslash}}',
                '{{toggleAjaxFunction}}'
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
                Helper::forslash(),
                viewHelper::toggleAjaxFunction($name, $fields)

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
            if($var[0] == 'image'){
                $field_name = $field_name.'<th scope="col">'.ucfirst(str_replace("_"," ", $var[1])).'</th>';
                $field_name .= "\n\t\t\t\t\t\t\t\t\t";
            }
        }
        foreach ($fields as $field) {
            $var = explode('*', $field);
            if($var[0] != 'image'){
                $field_name = $field_name.'<th scope="col">'.ucfirst(str_replace("_"," ", $var[1])).'</th>';
                $field_name .= "\n\t\t\t\t\t\t\t\t\t";
            }
        }
        return $field_name;
    }

    public static function tableBodyFields($name, $fields){
        $fields = explode(',', $fields);
        $field_name = "\n";
        foreach ($fields as $field) {
            $var = explode('*', $field);
            if($var[0] == 'image'){
                $field_name .= str_replace(
                    [
                        '{{modelNameSingularVar}}',
                        '{{fieldNameSigularVar}}'
                    ],
                    [
                        Helper::modelNameSingularVar($name),
                        Helper::fieldNameSigularVar($var[1])
                    ],
                    Helper::getStub("views/index/image.blade")
                );
            }
        }
        foreach ($fields as $field) {
            $var = explode('*', $field);
            if ($var[0] == 'select') {
                $field_name .= str_replace(
                    [
                        '{{modelNameSingularVar}}',
                        '{{fieldNameSigularVar}}'
                    ],
                    [
                        Helper::modelNameSingularVar($name),
                        Helper::fieldNameSigularVar($var[1])
                    ],
                    Helper::getStub("views/index/select.blade")
                );
            }else if($var[0] == 'image'){
                $field_name .= '';
            }else if($var[0] == 'toggle'){
                $field_name .= str_replace(
                    [
                        '{{modelNameSingularVar}}',
                        '{{fieldNameSigularVar}}'
                    ],
                    [
                        Helper::modelNameSingularVar($name),
                        Helper::fieldNameSigularVar($var[1])
                    ],
                    Helper::getStub("views/index/toggle.blade")
                );
            }else{
                $field_name .= str_replace(
                    [
                        '{{modelNameSingularVar}}',
                        '{{fieldNameSigularVar}}'
                    ],
                    [
                        Helper::modelNameSingularVar($name),
                        Helper::fieldNameSigularVar($var[1])
                    ],
                    Helper::getStub("views/index/text.blade")
                );
            }            
        }
        return $field_name;
    }

    public static function toggleAjaxFunction($name, $fields){
        $fields = explode(',', $fields);
        $toggle = Helper::getStub("views/extra/toggle-ajax-head.blade");
        foreach ($fields as $field) {
            $var = explode('*', $field);
            if($var[0] == 'toggle'){
                $toggle .= str_replace(
                    [
                        '{{modelNameSingularVar}}',
                        '{{fieldNameSigularVar}}',
                        '{{path}}',
                        '{{forslash}}'
                    ],
                    [
                        Helper::modelNameSingularVar($name),
                        Helper::fieldNameSigularVar($var[1]),
                        Helper::path(),
                        Helper::forslash()
                    ],
                    Helper::getStub("views/extra/toggle-ajax-body.blade")
                );
            }
        }
        $toggle .= Helper::getStub("views/extra/toggle-ajax-footer.blade");
        return $toggle;
    }


/******************************************************************
    //  Start showBladePHP Code Generator
********************************************************************/

    public static function showBladePHP($name, $fields){
        $fields_without_explode = $fields;
        $fields = explode(',', $fields);
        $viewTemplate = '';
        foreach ($fields as $field) {
            $var = explode('*', $field);
            $type = $var[0];
            $input_name = $var[1];

            if ($type == 'select') {
                $viewTemplateReturn = ViewHelper::showFieldWithRelationship($name, $type, $input_name);                
            }else if ($type == 'toggle') {
                $viewTemplateReturn = ViewHelper::showFieldToggle($name, $type, $input_name);                
            }else if($type == 'image'){
                $viewTemplateReturn = '';
            }
            else{
                $viewTemplateReturn = ViewHelper::showField($name, $type, $input_name);
            }
            $viewTemplate = $viewTemplate . $viewTemplateReturn;
        }
        $viewTemplateStart = ViewHelper::show_start($name);
        $viewTemplateEnd = ViewHelper::show_end($name, $fields, $fields_without_explode);

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

    public static function show_end($name, $fields, $fields_without_explode){
        $viewTemplate = str_replace(
            [
                '{{address}}',
                '{{modelNameSingularVar}}',
                '{{path}}',
                '{{forslash}}',
                '{{showImage}}',
                '{{toggleAjaxFunction}}'
            ],
            [
                Helper::getAddress($name),
                Helper::modelNameSingularVar($name),
                Helper::path(),
                Helper::forslash(),
                viewHelper::showImage($name, $fields),
                viewHelper::toggleAjaxFunction($name, $fields_without_explode)
            ],
            Helper::getStub('views/show/show_end.blade')
        );
        return $viewTemplate;
    }
    public static function showImage($name, $fields){
        $image = '';
        foreach ($fields as $field) {
            $var = explode('*', $field);
            $type = $var[0];
            $input_name = $var[1];            
            if ($type == 'image') {
                $image .= str_replace(
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
                    Helper::getStub("views/show/side-image.blade")
                );
            }
        }
        return $image;
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

    public static function showFieldToggle($name, $type, $input_name){
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
            Helper::getStub("views/show/toggle.blade")
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
        $viewTemplateEnd = ViewHelper::create_end($name, $fields);

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

    public static function create_end($name, $fields){
        $viewTemplate = str_replace(
            [
                '{{name}}',
                '{{address}}',
                '{{createImageUpload}}'

            ],
            [
                $name,
                Helper::getAddress($name),
                ViewHelper::createImageUpload($name, $fields),
            ],
            Helper::getStub('views/create/create_end.blade')
        );
        return $viewTemplate;
    }
    public static function createImageUpload($name, $fields){
        $image = '';
        foreach ($fields as $field) {
            $var = explode('*', $field);
            $type = $var[0];
            $input_name = $var[1];            
            if ($type == 'image') {
                $image .= str_replace(
                    [
                        '{{name}}',
                        '{{address}}',
                        '{{fieldNameSigularVar}}',

                    ],
                    [
                        $name,
                        Helper::getAddress($name),
                        Helper::fieldNameSigularVar($input_name),
                    ],
                    Helper::getStub('views/create/side-image.blade')
                );
            }
        }
        $image .= "@include('includes.crud-file-upload-modal')";
        return $image;
    }
    public static function createTextField($name, $type, $input_name){
        $viewTemplate = str_replace(
            [
                '{{fieldNameSigularVar}}',
                '{{fieldNamePluralVar}}',
                '{{type}}',
                '{{inputNameLabel}}',
                '{{modelNameSingularVar}}',
                '{{formColClass}}',
                '{{formLabelClass}}'                
            ],
            [
                Helper::fieldNameSigularVar($input_name),
                Helper::fieldNamePluralVar($input_name),
                $type,
                Helper::inputNameLabel($input_name),
                Helper::modelNameSingularVar($name),
                Helper::formColClass(), 
                Helper::formLabelClass(),               
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
        // $viewTemplate = ViewHelper::editTextField($name, 'hidden', 'id');
        $viewTemplate = '';
        foreach ($fields as $field) {

            $var = explode('*', $field);
            $type = $var[0];
            $input_name = $var[1];


            $viewTemplateReturn = ViewHelper::editTextField($name, $type, $input_name);

            $viewTemplate = $viewTemplate . $viewTemplateReturn;
        }
        $viewTemplateStart = ViewHelper::edit_start($name);
        $viewTemplateEnd = ViewHelper::edit_end($name, $fields);

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
                '{{formColClass}}',
                '{{formLabelClass}}'
            ],
            [
                Helper::fieldNameSigularVar($input_name),
                Helper::fieldNamePluralVar($input_name),
                $type,
                Helper::inputNameLabel($input_name),
                Helper::modelNameSingularVar($name),
                Helper::modelNamePluralVar($name),
                Helper::formColClass(),
                Helper::formLabelClass(),
                
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

    public static function edit_end($name, $fields){
        $viewTemplate = str_replace(
            [
                '{{name}}',
                '{{address}}',
                '{{editImageUpload}}'

            ],
            [
                $name,
                Helper::getAddress($name),
                viewHelper::editImageUpload($name, $fields)
            ],
            Helper::getStub('views/edit/edit_end.blade')
        );
        return $viewTemplate;
    }

    public static function editImageUpload($name, $fields){
        $image = '';
        foreach ($fields as $field) {
            $var = explode('*', $field);
            $type = $var[0];
            $input_name = $var[1];            
            if ($type == 'image') {
                $image .= str_replace(
                    [
                        '{{address}}',
                        '{{fieldNameSigularVar}}',
                        '{{modelNameSingularVar}}'

                    ],
                    [   
                        Helper::getAddress($name),
                        Helper::fieldNameSigularVar($input_name),
                        Helper::modelNameSingularVar($name)
                    ],
                    Helper::getStub('views/edit/side-image.blade')
                );
            }
        }
        $image .= "@include('includes.crud-file-upload-modal')";
        return $image;
    }
	
}