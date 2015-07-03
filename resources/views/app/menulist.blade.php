@extends('layout.app')
@section('title', '菜单管理')
@section('stylesheet')
@parent
@endsection
@section('javascript')
@parent
    <script type="text/javascript">
    $(document).ready(function(){
        app_theme_set(theme);
    });
    function action_formatter(v,r){
        var btn = [];
        if(r.is_sys){
            btn.push('<span class="disable">修改</span>');
            btn.push('<span class="disable">删除</span>');
        } else {
            btn.push('<a href="javascript:void(0);" onclick="action_upsert('+r.id+')">修改</a>');
            btn.push('<a href="javascript:void(0);" onclick="action_delete('+r.id+')">删除</a>');
        }
        btn.push('<a href="javascript:void(0);" onclick="action_upsert(0,'+r.id+')">添加子菜单</a>');
        return btn.join(' | ');
    }
    function action_upsert(id, pid){
        (typeof id  !== 'number') && (id  = 0);
        (typeof pid !== 'number') && (pid = 0);
        iframe_dialog($('#dialog_upsert'), {href:'/menuupsert/'+id+'/'+pid});
    }
    function action_delete(id){
        _action_delete('/menudelete', id, '该操作将删除该菜单及其子菜单，确定要删除吗？', _token);
    }
    function action_orderby(id, _this){
        _action_orderby('/menuorderby', id, _this, _token);
    }
    </script>
@endsection
@section('body')
<body style="padding:5px;">
    <table class="easyui-treegrid" style="width:100%;height:auto;"
            data-options="
                url: 'menulist',
                idField: 'id',
                treeField: 'name',
                fit:true,
                toolbar:[{text:'Add',iconCls:'icon-add',handler:action_upsert},'-',{text:'Reload',iconCls:'icon-reload',handler:grid_reflesh}]
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
    <div id="dialog_upsert" class="easyui-dialog" title="菜单管理" data-options="modal:true,closed:true,iconCls:'icon-edit'" style="width:700px;height:432px;"></div>
</body>
@endsection