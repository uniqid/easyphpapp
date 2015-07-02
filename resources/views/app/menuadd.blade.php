<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>菜单管理</title>
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
    function submitForm(){
        if(!$('#menu_form').form('validate')){return false;}
        
        $.post('menuadd', $("#menu_form").serialize(), function(res){
            if(res.status>0){
                $.messager.alert('提示信息', '保存失败', 'error');
            }else{
                $.messager.alert('提示信息', '保存成功', 'info');
                parent.grid_reflesh();
                parent.dialog_close('dialog_add');
            }
        });
    }
    </script>
</head>
<body style="padding:5px;">
<div class="easyui-panel" style="width:auto;">
    <div style="padding:10px 60px 20px 60px">
    <form id="menu_form" method="post" action="menuadd">
        {!! csrf_field() !!}
        <table cellpadding="5" style="width:98%">
            <tr>
                <td>上级菜单:</td>
                <td>
                    <input class="easyui-combotree" name="pid" value="<?php echo $pid; ?>" data-options="url:'/menutree/-1',method:'get',required:true" style="width:60%;">
                </td>
            </tr>
            <tr>
                <td>菜单名称：</td>
                <td><input class="easyui-textbox" type="text" name="name" data-options="required:true,validType:'length[1,50]'" style="width:60%;"></input></td>
            </tr>
            <tr>
                <td>菜单链接：</td>
                <td><input class="easyui-textbox" type="text" name="url" data-options="validType:'length[0,100]'" style="width:60%;"></input></td>
            </tr>
            <tr>
                <td>排序值：</td>
                <td><input class="easyui-numberbox" name="orderby" value="0" data-options="min:0,max:10000,precision:0,required:true"  style="width:60%;"></td>
            </tr>
            <tr>
                <td>是否显示：</td>
                <td>
                    <input type="radio" name="is_show" value="1" checked="checked"><span>是</span>
                    <input type="radio" name="is_show" value="0"><span>否</span>
                </tr>
        </table>
    </form>
    </div>
</div>
<div style="margin:20px auto;text-align:center;width:100%">
    <a href="javascript:void(0)" class="easyui-linkbutton" onclick="submitForm()">保存</a>
</div>
</body>
</html>