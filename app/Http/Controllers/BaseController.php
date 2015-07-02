<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use DB;

class BaseController extends Controller {
    public function __construct(){
        
    }
    
    protected function success($data = ''){
        $r = array('status' => 0, 'data' => $data);
        return json_encode($r);
    }
    
    protected function error($data){
        $r = array('status' => 1, 'data' => $data);
        return json_encode($r);
    }
    
    protected function db_add($table, $data){
        $sql = 'insert into '.$table.'('.implode(',', array_keys($data)).') values('.trim(str_repeat("?,", count($data)), ",").')';
        $this->db_insert($sql, array_values($data));
    }
    
    protected function db_edit($table, $id, $data){
        $sql  = 'update '.$table.' set '.implode('=?,', array_keys($data)).'=? where id='.$id;
        return $this->db_update($sql, array_values($data));
    }
    
    protected function db_get($query, $bindings = []){
        $results = DB::selectOne($query, $bindings);
        return empty($results)? array(): $results;
    }
    
    protected function db_list($query, $bindings = [], $useReadPdo = true){
        return DB::select($query, $bindings, $useReadPdo);
    }
    
    protected function db_page($query, $bindings = [], $useReadPdo = true){
        $results = DB::select($query, $bindings, $useReadPdo);
    }
    
    protected function db_count($query, $bindings = []){
        $results = DB::selectOne($query, $bindings);
    }
    
    protected function db_insert($query, $bindings = []){
        DB::insert($query, $bindings);
    }
    
    protected function db_update($query, $bindings = []){
        return DB::update($query, $bindings);
    }
    
    protected function db_delete($query, $bindings = []){
        return DB::delete($query, $bindings);
    }
    
    protected function db_query($query, $bindings = []){
        return DB::statement($query, $bindings);
    }
    
    protected function db_statement($query, $bindings = []){
        return DB::statement($query, $bindings);
    }
    
    protected function db_transaction(Closure $callback){
        return DB::transaction($callback);
    }

    protected function db_begin(){
        DB::beginTransaction();
    }
    
    protected function db_rollback(){
        DB::rollBack();
    }
    
    protected function db_commit(){
        DB::commit();
    }
}
