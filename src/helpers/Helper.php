<?php

namespace GovindTomar\CrudGenerator\Helpers;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Config;

class Helper{

	public static function getStub($type)
    {
        return file_get_contents(__DIR__."/../stubs/$type.stub");
    }

    public static function layout(){
        return config('crudgenerator.layout');
        // return 'layouts.admin.app';
    }

    public static function path(){
        return config('crudgenerator.path');
        // return 'admin';
    }

    public static function namespace(){
        return config('crudgenerator.namespace');
        // return 'Admin';
    }

    public static function backslash(){
        if (config('crudgenerator.backslash') != '') {
            return explode('"', config('crudgenerator.backslash'))[0];            
        }else{
            return '';
        }
    }

    public static function forslash(){
        return config('crudgenerator.forslash');
    }

    public static function bodyColClass(){
        return 'col-xl-12 col-xxl-12';
    }

    public static function formColClass(){
        return 'col-lg-6';
    }

    public static function getTableName($name){
        $name = $name.'Z';
        $pieces = preg_split('/(?=[A-Z])/',$name);
        if ($pieces[2] == 'Z') {
            return strtolower(Str::plural($pieces[1]));
        }else
        {
           return strtolower($pieces[1]).'_'.strtolower(Str::plural($pieces[2]));
        }

    }

    public static function getAddress($name){
        $name = $name.'Z';
        $pieces = preg_split('/(?=[A-Z])/',$name);
        if ($pieces[2] == 'Z') {
            return strtolower($pieces[1]);
        }else
        {
           return strtolower($pieces[1]).'-'.strtolower($pieces[2]);
        }
    }

    public static function modelNameSingularVar($name){
        $name = $name.'Z';
        $pieces = preg_split('/(?=[A-Z])/',$name);
        if ($pieces[2] == 'Z') {
            return strtolower($pieces[1]);
        }else
        {
           return strtolower($pieces[1]).'_'.strtolower($pieces[2]);
        }
    }

    public static function modelNamePluralVar($name){
        $name = $name.'Z';
        $pieces = preg_split('/(?=[A-Z])/',$name);
        if ($pieces[2] == 'Z') {
            return strtolower(Str::plural($pieces[1]));
        }else
        {
            return strtolower($pieces[1]).'_'.strtolower(Str::plural($pieces[2]));
        }
    }

    public static function modelNameSigularUpperCase($name){
        $name = $name.'Z';
        $pieces = preg_split('/(?=[A-Z])/',$name);
        if ($pieces[2] == 'Z') {
            return ucfirst($pieces[1]);
        }else
        {
            return ucfirst($pieces[1]).' '.ucfirst($pieces[2]);
        }
    }

    public static function modelNamePluralUpperCase($name){
        $name = $name.'Z';
        $pieces = preg_split('/(?=[A-Z])/',$name);
        if ($pieces[2] == 'Z') {
            return ucfirst(Str::plural($pieces[1]));
        }else
        {
            return ucfirst($pieces[1]).' '.ucfirst(Str::plural($pieces[2]));
        }
    }

    public static function modelNameSingularLowerCase($name){
        $name = $name.'Z';
        $pieces = preg_split('/(?=[A-Z])/',$name);
        if ($pieces[2] == 'Z') {
            return strtolower($pieces[1]);
        }else
        {
            return strtolower($pieces[1]).' '.strtolower($pieces[2]);
        }
    }


    public static function modelNamePluralLowerCase($name){
        $name = $name.'Z';
        $pieces = preg_split('/(?=[A-Z])/',$name);
        if ($pieces[2] == 'Z') {
            return strtolower(Str::plural($pieces[1]));
        }else
        {
            return strtolower($pieces[1]).' '.strtolower(Str::plural($pieces[2]));
        }
    }


    public static function inputNameLabel($input_name){
        return ucfirst(str_replace('_', ' ', $input_name));
    }


    public static function fieldNameSigularVar($input_name){
        return strtolower($input_name);
    }


    public static function fieldNamePluralVar($input_name){
        return strtolower(Str::plural($input_name));
    }

}