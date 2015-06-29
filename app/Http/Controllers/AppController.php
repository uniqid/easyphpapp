<?php
namespace App\Http\Controllers;
use App\Http\Controllers\BaseController;
use DB;

class AppController extends BaseController {
    public function __construct(){
        parent::__construct();
    }
    
    public function any_welcome()
    {
        return view('greeting', ['name' => 'James']);
    }
    
    public function any_phpinfo()
    {
        phpinfo();
        exit;
    }
    
    public function any_index()
    {
        $users = DB::select('select * from apps limit 10');
        if(empty($users)){
            $this->_init_apps();
            $users = DB::select('select * from apps limit 10');
        }
        
        print_r($users);
        return '';
        return view('user.index', ['users' => $users]);
    }
    
    private function _init_apps(){
        
    }
    
    private function _get_apps(){
        
    }
}
