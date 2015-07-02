<?php
namespace App\Http\Controllers;
use App\Http\Controllers\BaseController;
use DB;

class ApiController extends BaseController {
    public function __construct(){
        parent::__construct();
    }
    
    public function any_index()
    {
        return view('api.index', ['title' => 'API']);
    }
}
