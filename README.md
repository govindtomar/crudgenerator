CRUD Generator

Installation
composer require govindtomar/crudgenerator


Configuration
You will find a configuration file located at config/crud.php

'code_type' => 'API',


CRUD Create
php artisan make:crud ContactMe --fields=text*name,email*email,text*subject,textarea*message

CRUD with Relationship
php artisan make:crud ContactMe --fields=text*name,email*email,text*subject,textarea*message --model=User

CRUD with migration
php artisan make:crud ContactMe --fields=text*name,email*email,text*subject,textarea*message --migration=yes


Migration
php artisan make:crud Post --fields="text*name,image*profile,image*cover,select*user",toggle*status,toggle*publish --model=User --migration=yes


Available fields

text
email
file
hidden
image
number
password
date
radio
select
textarea
toggle