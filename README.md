# CRUD Generator

## Installation
composer require govindtomar/crudgenerator <br>
php artisan vendor:publish --provider="GovindTomar\CrudGenerator\CrudGeneratorServiceProvider"


### Configuration
You will find a configuration file located at config/crud.php

'code_type' => 'API',

## Examples

### CRUD Create <br>
`php artisan make:crud ContactMe --fields=text*name,email*email,text*subject,textarea*message`

### CRUD with Relationship <br>
`php artisan make:crud ContactMe --fields=text*name,email*email,text*subject,textarea*message --model=User`

### CRUD with migration <br>
`php artisan make:crud ContactMe --fields=text*name,email*email,text*subject,textarea*message --migration=yes`

### CRUD with Relationship & Migration <br>
`php artisan make:crud Post --fields="text*name,image*profile,image*cover,select*user",toggle*status,toggle*publish --model=User --migration=yes`


### Available fields <br>

text | email | file | hidden | image | number | password | date | radio | select |  textarea | toggle