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
        $id = intval($id);
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
     * Menu upsert
     */
    public function any_menuupsert(Request $req, $id=0, $pid = 0){
        $id  = intval($id);
        $pid = intval($pid);
        if($req->isMethod('post')){
            $data = $req->only('pid', 'name', 'url', 'orderby', 'is_show');
            $default = array('id_path'=>'', 'level'=>0);
            if($data['pid'] != 0){
                $pmenu = $this->db_get('select level,id_path from menus where id=:id', ['id' => $data['pid']]);
                if(!empty($pmenu)){
                    $data['level']   = $pmenu->level + 1;
                    $data['id_path'] = $pmenu->id_path.$data['pid'].'-';
                } else {
                    $data['pid'] = 0;
                }
            }
            $data = array_merge($default, $data);
            
            $id  = $req->input('id');
            $row = $id>0? $this->db_get('select id,pid,id_path,level from menus where is_sys=0 and id=:id', ['id' => $id]): array();
            if(!empty($row)){
                $this->db_edit('menus', $id, $data);
                $new_menu = (object)array('id_path' => $data['id_path'], 'level' => $data['level']);
                $this->_submenu_update($row, $new_menu);
            } else {
                $data['status']  = 0;
                $data['created'] = time();
                $this->db_add('menus', $data);
            }
            return $this->success($data);
        } else {
            $data = $this->db_get('select id, pid, name, url, orderby, is_show from menus where is_sys=0 and id=:id', ['id' => $id]);
            return view('app.menuupsert', [
                'data' => json_encode(!empty($data)? $data: array('pid' => $pid))
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
     * Field list
     */
    public function any_fieldlist(Request $req){
        $menus = $this->db_list('select * from fields where status=0 order by orderby, id');
        if($req->isMethod('post')){
            return json_encode($menus);
        } else {
            return view('app.fieldlist');
        }
    }
    
    /**
     * Field upsert
     */
    public function any_fieldupsert(Request $req, $id = 0){
        $id = intval($id);
        if($req->isMethod('post')){
            $data = $req->only('name','type','minlength','maxlength','defaultval','required','options','comment','orderby');
            if(!empty($data['options'])){
                $tmp_opts = preg_split('/[;；]/', $data['options']);
                $options  = array();
                foreach($tmp_opts as $opt){
                    $k_v = array_map('trim', explode('|', trim($opt)));
                    if(count($k_v) === 2){
                        list($k, $v) = $k_v;
                        if(strlen($k) && strlen($v)){
                            $options[$k] = preg_replace('/[\r\n]/is', ' ', $v);
                        }
                    }
                }
                $data['options'] = empty($options)? '': json_encode($options);
            }
            $id   = $req->input('id');
            $row  = $id>0? $this->db_get('select id from fields where id=:id', ['id' => $id]): array();
            if(!empty($row)){
                $this->db_edit('fields', $id, $data);
            } else {
                $data['status']  = 0;
                $data['created'] = time();
                $this->db_add('fields', $data);
            }
            return $this->success($data);
        } else {
            $data = $this->db_get('select * from fields where id=:id', ['id' => $id]);
            if(!empty($data) && strlen($data->options)){
                $opts = json_decode($data->options, true);
                $options = '';
                if(!empty($opts)){
                    foreach($opts as $key => $val){
                        $options .= ";\n" . $key .'|'. $val;
                    }
                }
                $data->options = trim($options, ";\n");
            }
            return view('app.fieldupsert', [
                'data' => json_encode(!empty($data)? $data: array())
            ]);
        }
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
        $data = array('pid' => $menu->id, 'id_path' => $default['id_path'].$menu->id.'-', 'level' => 1);
        $submenus = array(
            array('name' => '菜单管理', 'url' => 'menulist'),
            array('name' => '字段管理', 'url' => 'fieldlist')
        );
        foreach($submenus as $_menu){
            $this->db_add('menus', array_merge($default, $data, $_menu));
        }
        
        return $this->db_list('select id,name from menus where level=0 and status=0');
    }
}
