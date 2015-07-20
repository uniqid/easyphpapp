@extends('layout.app')
@section('title', '表单管理')
@section('stylesheet')
@parent
    <style>
        table.text .hidden,table.textarea .hidden,table.password .hidden{display:none;}
    </style>
@endsection
@section('javascript')
@parent
    <script type="text/javascript">
    $(document).ready(function(){
        app_theme_set(theme);
        $('#data_form').form('load', <?php echo $data; ?>);
        $('#field_list_table').find('input[type=checkbox]').bind('click', function(){
            var id = '#order_ipt_' + $(this).val();
            if($(this).is(':checked')){
                $(id).html('<input type="text" name="orderby[]" value="0" style="width:48px;text-align:center;">');
            } else {
                $(id).html('');
            }
        });
    });
    </script>
@endsection
@section('body')
<body style="padding:5px;">
<div class="easyui-panel" style="width:auto;">
    <div style="padding:10px 60px 20px 60px">
    <form id="data_form" method="post">
        {!! csrf_field() !!}
        <input type="hidden" name="id" value="0" />
        <table cellpadding="5" style="width:98%">
            <tr>
                <td>表单类型:</td>
                <td>
                    <select class="easyui-combobox" name="type" style="width:60%;">
                        <option value="1">单条记录</option>
                        <option value="2">多条记录</option>
                    </select>                            
                </td>
            </tr>
            <tr>
                <td>表单名称：</td>
                <td><input class="easyui-textbox" type="text" name="name" data-options="required:true,validType:'length[1,20]'" style="width:60%;"></input></td>
            </tr>
            <tr>
                <td>角色选择：</td>
                <td>
                <?php 
                foreach(array(array('id'=>0, 'title' => '全部')) as $role){
                    echo '<div><input name="role_ids[]" type="checkbox" value="'.$role['id'].'" /> '.$role['title'].'</div>';
                }
                ?>
                </td>
            </tr>
            <tr>
                <td>字段选择：</td>
                <td>
                    <table width="100%" class="datagrid-htable" id="field_list_table">
                        <tr  class="datagrid-header"><td colspan="6"></td></tr>
                        <tr  class="datagrid-header"><td width="10"></td><td>名称</td><td>类型</td><td width="54">排序</td></tr>
                        <?php
                        foreach($fields as $f){
                            echo '<tr  class="datagrid-header">
                                    <td><input name="field_id[]" type="checkbox" value="'.$f->id.'" /></td>
                                    <td>'.$f->name.'</td>
                                    <td>'.$f->type.'</td>
                                    <td id="order_ipt_'.$f->id.'"></td>
                                  </tr>';
                        }
                        ?>
                    </table>
                </td>
            </tr>
            <tr>
                <td>排序值：</td>
                <td><input class="easyui-numberbox" name="orderby" value="0" data-options="min:0,max:10000,precision:0,required:true"  style="width:60%;"></td>
            </tr>
        </table>
    </form>
    </div>
</div>
<div style="margin:20px auto;text-align:center;width:100%">
    <a href="javascript:void(0)" class="easyui-linkbutton" onclick="form_submit('/formupsert')">保存</a>
</div>
</body>
@endsection