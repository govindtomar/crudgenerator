CRUD Generator

 php artisan make:crud User --fields="text*name,image*profile,email*email,password*password"

 php artisan make:crud Post --fields="text*name,image*profile,image*cover,select*user",toggle*status,toggle*publish --model=User --migration=yes