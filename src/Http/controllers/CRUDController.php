<?php
namespace GovindTomar\CrudGenerator\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class CRUDController extends Controller
{
    public function __construct(){
        $this->middleware('web');
    }
    
    public function file_upload(Request $request){
        if(!file_exists(public_path(config('crud.trash_path')))){
            mkdir(public_path(config('crud.trash_path')));
        }
        $image_data = $request->image;
        $image_array_1 = explode(";", $image_data);
        $image_array_2 = explode(",", $image_array_1[1]);
        $data = base64_decode($image_array_2[1]);
        $image_name = time() . '.png';
        $upload_path = public_path(config('crud.trash_path').'/' . $image_name);
        file_put_contents($upload_path, $data);
        return response()->json(['path' => $image_name]);
    }    
}
