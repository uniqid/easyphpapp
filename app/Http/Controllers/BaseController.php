<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use DB;

class BaseController extends Controller {
    public function __construct(){
        echo "Yes, Here is parent controller!";
    }
}
