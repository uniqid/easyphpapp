<?php
namespace App\Http\Controllers;
use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use DB;

class AppController extends BaseController {
    public function __construct(){
        parent::__construct();
    }
    
    /**
     * Home page
     */
    public function get_index()
    {
        $top_menus = $this->_init_top_menus();
        return view('app.index', ['top_menus' => $top_menus]);
    }
    
    /**
     * 1.Tree menu in left part of Home page
     * 2.Combox tree
     */
    public function get_menutree($id=0){
        if($id == -1){ //combox tree
            $menus = $this->db_list('select id,name as text,id_path,url from menus where status=0 
                     order by level desc, orderby, id');
        } else { //menu left tree
            $menu = $this->db_get('select id_path from menus where id=:id', ['id' => $id]);
            if(empty($menu)){
                return json_encode(array());
            }
            $id_path = $menu->id_path.$id.'-';
            $menus = $this->db_list('select id,name as text,id_path,url from menus where status=0 and is_show=1 and 
                     (id='.$id.' or id_path like "'.$id_path.'%") order by level desc, orderby');
        }

        foreach($menus as &$_m){
            $_m->attributes = array('url' => $_m->url);
            unset($_m->url);
        }
        $trees = $id == -1? array(array('id' => 0, 'text' => '无-(作为一级菜单)')): array();
        return json_encode($this->_menu_tree_formatter($menus, $trees));
    }
    
    /**
     * Menu list
     */
    public function any_menulist(Request $req){
        $menus = $this->db_list('select * from menus where status=0 order by level desc, orderby, id');
        if($req->isMethod('post')){
            return json_encode($this->_menu_tree_formatter($menus));
        } else {
            return view('app.menulist');
        }
    }
    
    /**
     * Add menu
     */
    public function any_menuadd(Request $req, $pid = 0){
        if($req->isMethod('post')){
            $data = $req->only('pid', 'name', 'url', 'orderby', 'is_show');
            $default = array('id_path'=>'', 'level'=>0, 'status'=>0, 'created'=>time());
            if($data['pid'] != 0){
                $menu = $this->db_get('select level,id_path from menus where id=:id', ['id' => $data['pid']]);
                if(!empty($menu)){
                    $data['level']   = $menu->level + 1;
                    $data['id_path'] = $menu->id_path.$data['pid'].'-';
                }
            }
            $data = array_merge($default, $data);
            $sql  = 'insert into menus('.implode(',', array_keys($data)).') values('.trim(str_repeat("?,", count($data)), ",").')';
            $this->db_insert($sql, array_values($data));
            return $this->success($data);
        } else {
            return view('app.menuadd', ['pid' => $pid]);
        }
    }
    
    /**
     * Update menu
     */
    public function any_menuedit(Request $req, $id=0){
        if($req->isMethod('post')){
            $data = $req->only('pid', 'name', 'url', 'orderby', 'is_show');
            $default = array('id_path'=>'', 'level'=>0);
            if($data['pid'] != 0){
                $pmenu = $this->db_get('select level,id_path from menus where id=:id', ['id' => $data['pid']]);
                if(!empty($pmenu)){
                    $data['level']   = $pmenu->level + 1;
                    $data['id_path'] = $pmenu->id_path.$data['pid'].'-';
                }
            }
            $data = array_merge($default, $data);
            
            $id = $req->input('id');
            $menu = $this->db_get('select id, pid, id_path, level from menus where is_sys=0 and id=:id', ['id' => $id]);
            if(!empty($menu)){
                $this->db_edit('menus', $id, $data);
                $new_menu = (object)array('id_path' => $data['id_path'], 'level' => $data['level']);
                $this->_submenu_update($menu, $new_menu);
            } else {
                $data['status']  = 0;
                $data['created'] = time();
                $this->db_add('menus', $data);
            }
            return $this->success($data);
        } else {
            $menu = $this->db_get('select id, pid, name, url, orderby, is_show from menus where is_sys=0 and id=:id', ['id' => $id]);
            return view('app.menuedit', [
                'menu' => json_encode(!empty($menu)? $menu: array())
            ]);
        }
    }
    
    /**
     * Update menu orderby field
     */
    public function post_menuorderby(Request $req){
        $id = $req->input('id');
        $orderby = intval($req->input('orderby'));
        $orderby = $orderby > 10000? 10000: $orderby;
        
        $menu = $this->db_get('select orderby from menus where id=:id', ['id' => $id]);
        if(empty($menu)){
            return $this->error('菜单不存在！');
        }
        
        $orderby !== $menu->orderby && $this->db_edit('menus', $id, array('orderby' => $orderby));
        return $this->success();
    }
    
    /**
     * Delete menu
     */
    public function post_menudelete(Request $req){
        $id = $req->input('id');
        $menu = $this->db_get('select id, id_path, level, pid, status from menus where is_sys=0 and id=:id', ['id' => $id]);
        if(empty($menu)){
            return $this->error('菜单不存在！');
        }
        if($menu->status !== 1){
            $this->db_edit('menus', $id, array('status' => 1));
            $id_path = $menu->id_path.$id.'-';
            $this->db_update('update menus set status=1 where status=0 and id_path like "'.$id_path.'%"');
        }
        return $this->success();
    }
    
    private function _submenu_update($old_menu, $new_menu){
        $id_path = $old_menu->id_path.$old_menu->id.'-';
        $menus = $this->db_list('select id, id_path, level from menus where id_path like "'.$id_path.'%"');
        if(empty($menus)){
            return true;
        }
        $pos = strlen($old_menu->id_path);
        foreach($menus as $menu){
            $id_path = $new_menu->id_path . substr($menu->id_path, $pos);
            $level   = $menu->level - $old_menu->level + $new_menu->level;
            $data = array('id_path' => $id_path, 'level' => $level);
            $this->db_edit('menus', $menu->id, $data);
        }
        return true;
    }
    
    /**
     * About page
     */
    public function get_about(){
        phpinfo(INFO_GENERAL);
        return '';
    }
    
    /**
     * Tree formatter
     */
    private function _menu_tree_formatter($menus, $trees = array()){
        foreach($menus as $_m){
            $key = $_m->id_path.$_m->id;
            $trees[$key] = $_m;
        }
        
        foreach($trees as $key => $menu){
            $p_key = substr($key, 0, strrpos($key, '-'));
            if(isset($trees[$p_key])){
                $trees[$p_key]->children[] = $menu;
                unset($trees[$key]);
            }
        }
        return array_values($trees);
    }
    
    
    /**
     * Initialize system menus
     */
    private function _init_top_menus(){
        if($rows = $this->db_list('select id,name from menus where level=0 and status=0')){
            return $rows;
        }
        
        //init top menus
        $this->db_statement('truncate menus');
        $default = array(
            'pid'     => 0,
            'url'     => '',
            'id_path' => '',
            'level'   => 0,
            'orderby' => 0,
            'is_show' => 1,
            'is_sys'  => 1,
            'status'  => 0,
            'created' => time()
        );
        $menus = array('系统', '商店系统', '营养师部', '业务管理', '前台CMS管理', '用户管理', '我的面板');
        foreach($menus as $name){
            $data = $default;
            $data['name'] = $name;
            $this->db_add('menus', $data);
        }

        //init system submenu
        $menu = $this->db_get('select id from menus where level=0 and status=0 and name="系统"');
        $data = array('name' => '菜单管理', 'pid' => $menu->id, 'id_path' => $default['id_path'].$menu->id.'-', 'level' => 1, 'url' => 'menulist');
        $data = array_merge($default, $data);
        $this->db_add('menus', $data);
        
        return $this->db_list('select id,name from menus where level=0 and status=0');
    }
}
