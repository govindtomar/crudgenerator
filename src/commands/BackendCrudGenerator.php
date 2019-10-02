<?php

namespace GovindTomar\CrudGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class BackendCrudGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:backend
                        {name : Class (singular), e.g User}
                        {--fields= : Field names for the form & migration.}
                        {--tables= : Tables for select option}';

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
        $tables = $this->option('tables');


        if(!file_exists(app_path('/Http/Controllers/Backend'))){
            mkdir(app_path("/Http/Controllers/Backend"));
        }
        if(!file_exists(app_path('/Models'))){
            mkdir(app_path("/Models"));
        }
        if(!file_exists(resource_path("views/backend"))){
            mkdir(resource_path("views/backend"));
        }


        File::deleteDirectory(resource_path("views/backend/".$this->getAddress($name)));
        File::delete(app_path("/Models/{$name}.php"));
        File::delete(app_path("/Http/Controllers/Backend/{$name}Controller.php"));

        $this->controller($name, $fields, $tables);
        $this->model($name, $fields);
        $this->view($name, $fields);
        $this->routes($name);
        // $this->migration($name);

    }

/******************************************************************
    //  Start getStub Code Generator
********************************************************************/
    protected function getStub($type)
    {
        return file_get_contents(__DIR__."/../stubs/$type.stub");
    }


/******************************************************************
    //  Start migration Code Generator
********************************************************************/
    // protected function migration($name)
    // {
    //     $namePlural = strtolower(Str::plural($name));
    //     Artisan::call(command:"make:migration create_".$namePlural."_table --create=$namePlural");
    // }


    protected function getTableName($name){
        $name = $name.'Z';
        $pieces = preg_split('/(?=[A-Z])/',$name);
        if ($pieces[2] == 'Z') {
            return strtolower(Str::plural($pieces[1]));
        }else
        {
           return strtolower($pieces[1]).'_'.strtolower(Str::plural($pieces[2]));
        }

    }

    protected function getAddress($name){
        $name = $name.'Z';
        $pieces = preg_split('/(?=[A-Z])/',$name);
        if ($pieces[2] == 'Z') {
            return strtolower($pieces[1]);
        }else
        {
           return strtolower($pieces[1]).'-'.strtolower($pieces[2]);
        }
    }

    protected function modelNameSingularVar($name){
        $name = $name.'Z';
        $pieces = preg_split('/(?=[A-Z])/',$name);
        if ($pieces[2] == 'Z') {
            return strtolower($pieces[1]);
        }else
        {
           return strtolower($pieces[1]).'_'.strtolower($pieces[2]);
        }
    }

    protected function modelNamePluralVar($name){
        $name = $name.'Z';
        $pieces = preg_split('/(?=[A-Z])/',$name);
        if ($pieces[2] == 'Z') {
            return strtolower(Str::plural($pieces[1]));
        }else
        {
            return strtolower($pieces[1]).'_'.strtolower(Str::plural($pieces[2]));
        }
    }

    protected function modelNameSigularUpperCase($name){
        $name = $name.'Z';
        $pieces = preg_split('/(?=[A-Z])/',$name);
        if ($pieces[2] == 'Z') {
            return ucfirst($pieces[1]);
        }else
        {
            return ucfirst($pieces[1]).' '.ucfirst($pieces[2]);
        }
    }

    protected function modelNamePluralUpperCase($name){
                $name = $name.'Z';
        $pieces = preg_split('/(?=[A-Z])/',$name);
        if ($pieces[2] == 'Z') {
            return ucfirst(Str::plural($pieces[1]));
        }else
        {
            return ucfirst($pieces[1]).' '.ucfirst(Str::plural($pieces[2]));
        }
    }

    protected function modelNameSingularLowerCase($name){
        $name = $name.'Z';
        $pieces = preg_split('/(?=[A-Z])/',$name);
        if ($pieces[2] == 'Z') {
            return strtolower($pieces[1]);
        }else
        {
            return strtolower($pieces[1]).' '.strtolower($pieces[2]);
        }
    }

    protected function modelNamePluralLowerCase($name){
        $name = $name.'Z';
        $pieces = preg_split('/(?=[A-Z])/',$name);
        if ($pieces[2] == 'Z') {
            return strtolower(Str::plural($pieces[1]));
        }else
        {
            return strtolower($pieces[1]).' '.strtolower(Str::plural($pieces[2]));
        }
    }

    protected function inputNameLabel($input_name){
        return ucfirst(str_replace('_', ' ', $input_name));
    }


    protected function fieldNameSigularVar($input_name){
        return strtolower($input_name);
    }

    protected function fieldNamePluralVar($input_name){
        return strtolower(Str::plural($input_name));
    }


/******************************************************************
    //  Start getStub Code Generator
********************************************************************/
    protected function routes($name)
    {
        File::append(base_path('routes/web.php'), "\n\n // $name Controller Routes \n");
        File::append(base_path('routes/web.php'), "Route::get('{role}/".$this->getAddress($name)."/create', 'Backend\'".$name."Controller@create');\n");
        File::append(base_path('routes/web.php'), "Route::post('{role}/".$this->getAddress($name)."/create', 'Backend\'".$name."Controller@store');\n");
        File::append(base_path('routes/web.php'), "Route::get('{role}/".$this->getAddress($name)."', 'Backend\'".$name."Controller@index');\n");
        File::append(base_path('routes/web.php'), "Route::get('{role}/".$this->getAddress($name)."/{id}', 'Backend\'".$name."Controller@show');\n");
        File::append(base_path('routes/web.php'), "Route::get('{role}/".$this->getAddress($name)."/edit/{id}', 'Backend\'".$name."Controller@edit');\n");
        File::append(base_path('routes/web.php'), "Route::put('{role}/".$this->getAddress($name)."/edit/{id}', 'Backend\'".$name."Controller@update');\n");
        File::append(base_path('routes/web.php'), "Route::delete('{role}/".$this->getAddress($name)."/delete/{id}', 'Backend\'".$name."Controller@destroy');\n");

    }


/******************************************************************
    //  Start Model Code Generator
********************************************************************/
    protected function model($name, $fields)
    {
        $modelTemplate = str_replace(
            [
                '{{modelName}}',
                '{{modelField}}',
                '{{getTableName}}'
            ],
            [
                $name,
                $this->getModelFields($fields),
                $this->getTableName($name)
            ],
            $this->getStub('models/Model')
        );

        file_put_contents(app_path("Models/{$name}.php"), $modelTemplate);
    }

    protected function getModelFields($fields){
        $fields = explode(',', $fields);

        $field_name = '[';
        foreach ($fields as $field) {
            $var = explode('+', $field);
            $field_name = $field_name ."'". $var[1]."',";
        }
        $field_name = $field_name.']';
        return $field_name;
    }
/******************************************************************
    //  End Model Code Generator
    //  Start Controller Code Generator
********************************************************************/

    protected function controller($name, $fields, $tables)
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
                    '{{controllerValidation}}',
                    '{{createControllerFields}}',
                    '{{extraTables}}',
                    '{{getCompact}}',
                    '{{indexJoinTables}}',
                    '{{joinSelectTables}}'
                ],
                [
                    $name,
                    $this->getTableName($name),
                    $this->getAddress($name),
                    $this->modelNameSingularVar($name),
                    $this->modelNamePluralVar($name),
                    $this->modelNameSingularLowerCase($name),
                    $this->controllerValidation($name, $fields),
                    $this->getControllerFields($fields),
                    $this->getExtraTables($tables),
                    $this->getCompactFunction($tables),
                    $this->indexJoinTables($name, $tables),
                    $this->joinSelectTables($name, $tables)

                ],
                $this->getStub('controllers/CompactController')
            );

            file_put_contents(app_path("/Http/Controllers/Backend/{$name}Controller.php"), $controllerTemplate);
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
                    '{{controllerValidation}}',
                    '{{createControllerFields}}'
                ],
                [
                    $name,
                    $this->getTableName($name),
                    $this->getAddress($name),
                    $this->modelNameSingularVar($name),
                    $this->modelNamePluralVar($name),
                    $this->modelNamePluralLowerCase($name),
                    $this->modelNameSingularLowerCase($name),
                    $this->controllerValidation($name, $fields),
                    $this->getControllerFields($fields),
                ],
                $this->getStub('controllers/Controller')
            );

            file_put_contents(app_path("/Http/Controllers/Backend/{$name}Controller.php"), $controllerTemplate);

        }
    }

    protected function controllerValidation($name, $fields){
        $fields = explode(',', $fields);
        $field_name = '';
        foreach ($fields as $field) {
            $var = explode('+', $field);
            if ($var[0] == 'select') {
                $field_name = $field_name."'".$var[1]."_id'  =>  'required',\n";
            }else{
               $field_name = $field_name."'".$var[1]."'  =>  'required',\n";
            }

        }
        return $field_name;
    }

    protected function getControllerFields($fields){
        $fields = explode(',', $fields);
        $field_name = '';
        foreach ($fields as $field) {
            $var = explode('+', $field);
            if ($var[0] == 'select') {
                $field_name = $field_name."'".$var[1]."_id'  =>  _____request->{$var[1]}_id,\n";
            }else{
               $field_name = $field_name."'".$var[1]."'  =>  _____request->$var[1],\n";
            }

        }
        return str_replace('_____', '$', $field_name);
    }

    protected function indexJoinTables($name, $tables){
        $tables = explode(',', $tables);
        $table_name = '';
        foreach ($tables as $key => $table) {
        $table_join =  "->join('".$this->modelNamePluralVar($table)."', '".$this->modelNamePluralVar($name).".".$this->modelNameSingularVar($table)."_id', '=', '".$this->modelNamePluralVar($table).".id')";
        $table_name = $table_name.$table_join;
        }
        return $table_name;
    }

    protected function joinSelectTables($name, $tables){
        $tables = explode(',', $tables);
        $table_name = "->select('".$this->modelNamePluralVar($name).".*'";
        foreach ($tables as $key => $table) {
        $table_select = ", '".$this->modelNamePluralVar($table).".name as"." ".$this->modelNameSingularVar($table)."'";
        $table_name = $table_name.$table_select;
        }
        $table_name = $table_name.")" ;
        return $table_name;
    }


    protected function getExtraTables($tables){
        $tables = explode(',', $tables);
        $table_name = '';
        foreach ($tables as $key => $table) {
            $table = $this->getTableName($table);
            $table_name = $table_name . '$'.$table.' = DB::table("'.$table.'")->orderBy("name", "asc")->get();';

        }
        return $table_name;
    }

    protected function getCompactFunction($tables){
        $tables = explode(',', $tables);
        $compact = '';
        foreach ($tables as $key => $table) {
            $table = $this->getTableName($table);
            $compact = $compact .'"'. $table.'",';

        }

        return rtrim($compact, ",");
    }

/******************************************************************
    //  End Controller Code Generator
    //  Start View Code Generator
********************************************************************/

    protected function view($name, $fields)
    {
        mkdir(resource_path("views/backend/".$this->getAddress($name)));
        $this->indexBladePHP($name, $fields);
        $this->createBladePHP($name, $fields);
        $this->editBladePHP($name, $fields);
        $this->showBladePHP($name, $fields);

    }


/******************************************************************
    //  Start indexBladePHP Code Generator
********************************************************************/

    protected function indexBladePHP($name, $fields){
        $viewTemplate = str_replace(
            [
                '{{address}}',
                '{{modelNameSigularUpperCase}}',
                '{{modelNamePluralUpperCase}}',
                '{{modelNamePluralVar}}',
                '{{modelNameSingularVar}}',
                '{{tableHeadFields}}',
                '{{tableBodyFields}}'
            ],
            [
                $this->getAddress($name),
                $this->modelNameSigularUpperCase($name),
                $this->modelNamePluralUpperCase($name),
                $this->modelNamePluralVar($name),
                $this->modelNameSingularVar($name),
                $this->tableHeadFields($name, $fields),
                $this->tableBodyFields($name, $fields)

            ],
            $this->getStub('views/index/index.blade')
        );
        $dir = $this->getAddress($name);
        file_put_contents(resource_path("views/backend/{$dir}/index.blade.php"), $viewTemplate);
    }

    protected function tableHeadFields($name, $fields){
        $fields = explode(',', $fields);
        $field_name = '';
        foreach ($fields as $field) {
            $var = explode('+', $field);
            $field_name = $field_name.'<th scope="col">'.ucfirst(str_replace("_"," ", $var[1])).'</th>';
        }
        return $field_name;
    }

    protected function tableBodyFields($name, $fields){
        $fields = explode(',', $fields);
        $field_name = '';
        foreach ($fields as $field) {
            $var = explode('+', $field);
            $field_name = $field_name.'<td scope="col">{{ $'.$this->modelNameSingularVar($name).'->'.$var[1].' }}</td>';
        }
        return $field_name;
    }


/******************************************************************
    //  Start showBladePHP Code Generator
********************************************************************/

    protected function showBladePHP($name, $fields){
        $fields = explode(',', $fields);
        $viewTemplate = '';
        foreach ($fields as $field) {

            $var = explode('+', $field);
            $type = $var[0];
            $input_name = $var[1];


            $viewTemplateReturn = $this->showField($name, $type, $input_name);

            $viewTemplate = $viewTemplate . $viewTemplateReturn;
        }
        $viewTemplateStart = $this->show_start($name);
        $viewTemplateEnd = $this->show_end($name);

        $viewTemplate =    $viewTemplateStart.$viewTemplate.$viewTemplateEnd;

        $dir = $this->getAddress($name);
        file_put_contents(resource_path("views/backend/{$dir}/show.blade.php"), $viewTemplate);
    }

    protected function show_start($name){
        $viewTemplate = str_replace(
            [
                '{{address}}',
                '{{modelNameSigularUpperCase}}',
                '{{modelNamePluralUpperCase}}'
            ],
            [
                $this->getAddress($name),
                $this->modelNameSigularUpperCase($name),
                $this->modelNamePluralUpperCase($name)
            ],
            $this->getStub('views/show/show_start.blade')
        );
        return $viewTemplate;
    }

    protected function show_end($name){
        $viewTemplate = str_replace(
            [
                '{{address}}',
                '{{modelNameSingularVar}}'
            ],
            [
                $this->getAddress($name),
                $this->modelNameSingularVar($name),
            ],
            $this->getStub('views/show/show_end.blade')
        );
        return $viewTemplate;
    }

    protected function showField($name, $type, $input_name){
        $viewTemplate = str_replace(
            [
                '{{modelNameSingularVar}}',
                '{{fieldNameSigularVar}}',
                '{{inputNameLabel}}'
            ],
            [
                $this->modelNameSingularVar($name),
                $this->fieldNameSigularVar($input_name),
                $this->inputNameLabel($input_name)
            ],
            $this->getStub("views/show/show.blade")
        );
        return $viewTemplate;
    }

/******************************************************************
    //  Start createBladePHP Code Generator
********************************************************************/

    protected function createBladePHP($name, $fields){
        $fields = explode(',', $fields);
        $viewTemplate = '';
        foreach ($fields as $field) {

            $var = explode('+', $field);
            $type = $var[0];
            $input_name = $var[1];


            $viewTemplateReturn = $this->createTextField($name, $type, $input_name);

            $viewTemplate = $viewTemplate . $viewTemplateReturn;
        }
        $viewTemplateStart = $this->create_start($name);
        $viewTemplateEnd = $this->getStub('views/create/create_end.blade');

        $viewTemplate =    $viewTemplateStart.$viewTemplate.$viewTemplateEnd;

        $dir = $this->getAddress($name);
        file_put_contents(resource_path("views/backend/{$dir}/create.blade.php"), $viewTemplate);
    }


    protected function create_start($name){
        $viewTemplate = str_replace(
            [
                '{{address}}',
                '{{modelNameSigularUpperCase}}',
                '{{modelNamePluralUpperCase}}'
            ],
            [
                $this->getAddress($name),
                $this->modelNameSigularUpperCase($name),
                $this->modelNamePluralUpperCase($name)
            ],
            $this->getStub('views/create/create_start.blade')
        );
        return $viewTemplate;
    }

    protected function createTextField($name, $type, $input_name){
        $viewTemplate = str_replace(
            [
                '{{fieldNameSigularVar}}',
                '{{fieldNamePluralVar}}',
                '{{type}}',
                '{{inputNameLabel}}',
                '{{modelNameSingularVar}}'
            ],
            [
                $this->fieldNameSigularVar($input_name),
                $this->fieldNamePluralVar($input_name),
                $type,
                $this->inputNameLabel($input_name),
                $this->modelNameSingularVar($name)
            ],
            $this->getStub("views/create/{$type}.blade")
        );
        return $viewTemplate;
    }

/*****************************************************************
    //  Start editBladePHP Code Generator
*******************************************************************/

    protected function editBladePHP($name, $fields){
        $fields = explode(',', $fields);
        $viewTemplate = $this->editTextField($name, 'hidden', 'id');
        foreach ($fields as $field) {

            $var = explode('+', $field);
            $type = $var[0];
            $input_name = $var[1];


            $viewTemplateReturn = $this->editTextField($name, $type, $input_name);

            $viewTemplate = $viewTemplate . $viewTemplateReturn;
        }
        $viewTemplateStart = $this->edit_start($name);
        $viewTemplateEnd = $this->getStub('views/edit/edit_end.blade');

        $viewTemplate =    $viewTemplateStart.$viewTemplate.$viewTemplateEnd;

        $dir = $this->getAddress($name);
        file_put_contents(resource_path("views/backend/{$dir}/edit.blade.php"), $viewTemplate);
    }

    protected function editTextField($name, $type, $input_name){
        $viewTemplate = str_replace(
            [
                '{{fieldNameSigularVar}}',
                '{{fieldNamePluralVar}}',
                '{{type}}',
                '{{inputNameLabel}}',
                '{{modelNameSingularVar}}',
                '{{modelNamePluralVar}}'
            ],
            [
                $this->fieldNameSigularVar($input_name),
                $this->fieldNamePluralVar($input_name),
                $type,
                $this->inputNameLabel($input_name),
                $this->modelNameSingularVar($name),
                $this->modelNamePluralVar($name),
            ],
            $this->getStub("views/edit/{$type}.blade")
        );
        return $viewTemplate;
    }

    protected function edit_start($name){
        $viewTemplate = str_replace(
            [
                '{{address}}',
                '{{modelNameSigularUpperCase}}',
                '{{modelNamePluralUpperCase}}'
            ],
            [
                $this->getAddress($name),
                $this->modelNameSigularUpperCase($name),
                $this->modelNamePluralUpperCase($name)
            ],
            $this->getStub('views/edit/edit_start.blade')
        );
        return $viewTemplate;
    }

}
