<?php

namespace GovindTomar\CrudGenerator\Helpers;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use File;
use Config;

class CRUDHelper{

    public static function file_upload($file, $path=null){
        if(!file_exists(public_path(config('gt-crud.trash_path')))){
            mkdir(public_path(config('gt-crud.trash_path')));
        }
        if(!file_exists(public_path($path))){
            mkdir(public_path($path));
        }
        // if(!file_exists(public_path(config('gt-crud.trash_path').'/'.$file))){
            File::move(public_path(config('gt-crud.trash_path').'/'.$file), public_path($path.'/'.$file));
            return $path.'/'.$file;
        // }
        // return '';

    }


}
