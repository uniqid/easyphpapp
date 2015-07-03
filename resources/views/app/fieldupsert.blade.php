@extends('layout.app')
@section('title', '字段管理')
@section('stylesheet')
@parent
<style>
table.text .hidden,table.textarea .hidden,table.password .hidden,table.file .hidden{display:none;}
</style>
@endsection
@section('javascript')
@parent
    <script type="text/javascript">
    $(document).ready(function(){
        app_theme_set(theme);
        $('#data_form').find('table').attr('class', 'text');
        $('#data_form').form('load', <?php echo $data; ?>);
    });
    function switch_options(){
        $('#data_form').find('table').attr('class', $(this).combobox('getValue'));
    }
    </script>
@endsection
<body style="padding:5px;">
<div class="easyui-panel" style="width:auto;">
    <div style="padding:10px 60px 20px 60px">
    <form id="data_form" method="post">
        {!! csrf_field() !!}
        <input type="hidden" name="id" value="0" />
        <table cellpadding="5" style="width:98%">
            <tr>
                <td>字段名称：</td>
                <td><input class="easyui-textbox" type="text" name="name" data-options="required:true,validType:'length[1,20]'" style="width:60%;"></input></td>
            </tr>
            <tr>
                <td>字段类型:</td>
                <td>
                    <select class="easyui-combobox" name="type" data-options="onChange:switch_options" style="width:60%;">
                        <option value="text">单行文本框</option>
                        <option value="textarea">多行文本框</option>
                        <option value="select">下拉列表框</option>
                        <option value="radio">单选框</option>
                        <option value="checkbox">多选框</option>
                        <option value="password">密码框</option>
                        <option value="file">文件上传</option>
                    </select>                            
                </td>
            </tr>
            <tr>
                <td>最小[值|长度]：</td>
                <td><input class="easyui-numberbox" name="minlength" value="0" data-options="min:0,precision:0,required:true"  style="width:60%;"></td>
            </tr>
            <tr>
                <td>最大[值|长度]：</td>
                <td><input class="easyui-numberbox" name="maxlength" value="0" data-options="min:0,precision:0,required:true"  style="width:60%;"></td>
            </tr>
            <tr>
                <td>默认值</td>
                <td><input class="easyui-textbox" type="text" name="defaultval" data-options="validType:'length[0,100]'" style="width:60%;"></input></td>
            </tr>
            <tr>
                <td>是否必填：</td>
                <td>
                    <input type="radio" name="required" value="1" checked="checked"><span>是</span>
                    <input type="radio" name="required" value="0"><span>否</span>
            </tr>
            <tr class="hidden">
                <td>选项值:</td>
                <td><input class="easyui-textbox" name="options" data-options="multiline:true" style="width:60%;height:80px"></input></td>
            </tr>
            <tr>
                <td>备注:</td>
                <td><input class="easyui-textbox" name="comment" data-options="multiline:true" style="width:60%;height:80px"></input></td>
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
    <a href="javascript:void(0)" class="easyui-linkbutton" onclick="form_submit('/fieldupsert')">保存</a>
</div>
</body>
</html>