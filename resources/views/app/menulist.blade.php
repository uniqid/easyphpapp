<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>菜单列表</title>
	<link rel="stylesheet" type="text/css" href="/jeasyui/themes/default/easyui.css" data-theme="default" />
	<link rel="stylesheet" type="text/css" href="/jeasyui/themes/black/easyui.css" data-theme="black" />
	<link rel="stylesheet" type="text/css" href="/jeasyui/themes/bootstrap/easyui.css" data-theme="bootstrap" />
	<link rel="stylesheet" type="text/css" href="/jeasyui/themes/gray/easyui.css" data-theme="gray" />
	<link rel="stylesheet" type="text/css" href="/jeasyui/themes/metro/easyui.css" data-theme="metro" />
	<link rel="stylesheet" type="text/css" href="/jeasyui/themes/icon.css" />
	<link rel="stylesheet" type="text/css" href="/jeasyui/app.css" />
	<script type="text/javascript" src="/jeasyui/jquery.min.js"></script>
	<script type="text/javascript" src="/js/js.cookie.js"></script>
	<script type="text/javascript" src="/jeasyui/jquery.easyui.min.js"></script>
	<script type="text/javascript" src="/jeasyui/app.js"></script>
    <script type="text/javascript">
    var theme = Cookies.get('theme') || 'default';
    $(document).ready(function(){
        $('link[rel*=style][data-theme]').each(function(i){
            this.disabled = !(this.getAttribute('data-theme') == theme);
        });
    });
    function action_formatter(v,r){
        var btn = [];
        if(r.is_sys){
            btn.push('<span class="disable">修改</span>');
            btn.push('<span class="disable">删除</span>');
        } else {
            btn.push('<a href="javascript:void(0);" onclick="action_edit('+r.id+')">修改</a>');
            btn.push('<a href="javascript:void(0);" onclick="action_delete('+r.id+')">删除</a>');
        }
        btn.push('<a href="javascript:void(0);" onclick="action_add('+r.id+')">添加子菜单</a>');
        return btn.join(' | ');
    }
    function orderby_formatter(v,r){
        return '<input type="text" onblur="action_orderby('+r.id+',this)" value="'+v+'" data="'+v+'" size="8" style="text-align:center">';
    }
    
    function action_add(pid){
        if(typeof pid === 'number'){
            iframe_dialog($('#dialog_add'), {href:'/menuadd/'+pid});
        } else {
            iframe_dialog($('#dialog_add'), {href:'/menuadd'});
        }
    }
    
    function action_edit(id){
        iframe_dialog($('#dialog_edit'), {href:'/menuedit/'+id});
    }
    
    function action_delete(id){
        $.messager.confirm('提示信息', '该操作将删除该菜单及其子菜单，确定要删除吗？', function(result){
            if(!result) return false;
            $.post('menudelete', {id: id,_token:'{!! csrf_token() !!}'}, function(res){
                if(res.status > 0){
                    $.messager.alert('提示信息', '删除失败', 'error');
                }else{
                    //$.messager.alert('提示信息', '删除成功', 'info');
                    grid_reflesh();
                }
            }, 'json');
        });
    }
    
    function action_orderby(id, _this){
        if($(_this).attr('data') == _this.value){
            return;
        }
        $.post('/menuorderby', {id:id,orderby:_this.value,_token:'{!! csrf_token() !!}'}, function(res){
            if(res.status > 0){
                $.messager.alert('提示信息', '设置失败', 'error');
            } else {
                $(_this).attr('data', _this.value);
                grid_reflesh();
            }
        }, 'json');
    }
    function grid_reflesh(){
        $('table.easyui-treegrid').treegrid('reload');
    }
    </script>
</head>
<body style="padding:5px;">
    <table class="easyui-treegrid" style="width:100%;height:auto;"
            data-options="
                url: 'menulist',
                idField: 'id',
                treeField: 'name',
                fit:true,
                toolbar:[{text:'Add',iconCls:'icon-add',handler:action_add},'-',{text:'Reload',iconCls:'icon-reload',handler:grid_reflesh}]
            ">
        <thead>
            <tr>
                <th data-options="field:'orderby',formatter:orderby_formatter" align="center">排序</th>
                <th data-options="field:'id'" width="50" align="center">ID</th>
                <th data-options="field:'name'">名称</th>
                <th data-options="field:'is_sys',formatter:yes_or_no_formatter" align="center">系统菜单</th>
                <th data-options="field:'created',formatter:date_formatter" width="150" align="center">创建时间</th>
                <th data-options="field:'_act',formatter:action_formatter" align="center">操作</th>
            </tr>
        </thead>
    </table>
    
    <div id="dialog_add"  class="easyui-dialog" title="添加" data-options="modal:true,closed:true,iconCls:'icon-add'"  style="width:700px;height:432px;"></div>
    <div id="dialog_edit" class="easyui-dialog" title="修改" data-options="modal:true,closed:true,iconCls:'icon-edit'" style="width:700px;height:432px;"></div>
</body>
</html>