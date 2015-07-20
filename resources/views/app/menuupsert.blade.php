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
        $('#data_form').form('load', <?php echo $data; ?>);
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
                <td>上级菜单:</td>
                <td>
                    <input class="easyui-combotree" name="pid" value="0" data-options="url:'/menutree/-1',method:'get',required:true" style="width:60%;">
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
    <a href="javascript:void(0)" class="easyui-linkbutton" onclick="form_submit('/menuupsert')">保存</a>
</div>
</body>
@endsection