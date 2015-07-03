@extends('layout.index')
@section('title', '后台管理系统')
@section('stylesheet')
@parent
@endsection
@section('javascript')
@parent
    <script type="text/javascript">
    $(document).ready(function(){
        $('#app-topmenu li.app-nav>span').bind('click', function(){
            var _this = $(this);
            if(_this.hasClass('focus')){return;}
            _this.parent().children().removeClass('focus');
            _this.addClass('focus');
            app_switch_left_menutree(_this.attr('data-id'), _this.html());
        });
        $('#app-main-tabs').tabs({onSelect:function(title){
            $('body').layout('panel', 'center').panel('setTitle', title);
        }});

        app_theme_set(theme); //init set
        $('#app-topmenu li.app-nav>span:first').click();
        app_tab_open(0, 'About', '/about');
    });

    function app_theme_init(r){
        (r.value == theme) && (r.selected = true);
        var opts = $('#app-theme-combobox').combobox('options');
        return r[opts.textField];
    }

    function app_theme_change(_theme){
        app_theme_set(_theme);
        Cookies.set('theme', _theme, {path:'/', expires:365});
    }

    function app_switch_left_menutree(id, title){
        var _layout = $('body').layout('panel', 'west');
        var options = _layout.panel('options');
        if(title == options.title){
            return false;
        }
        _layout.panel('setTitle', title);
        var url = '/menutree/' + id;
        $("#left_menutree").tree({url:url, method:'get', animate:true, onClick:function(n){
            app_tab_open(n.id, n.text, (n.attributes && n.attributes.url)? n.attributes.url: false);
        }});
    }

    function app_tab_open(id, title, url){
        $('body').layout('panel', 'center').panel('setTitle', title);
        var src = url === false? '/menutree/' + id: url;
        title = '<span data-src="'+src.replace('"', '')+'">' + title + '</span>';
        if($('#app-main-tabs').tabs('exists', title)){
            $('#app-main-tabs').tabs('select', title);
        }else{
            iframe_tabs($('#app-main-tabs'), {title: title, src:src, closable:true, cache:true});
        }
    }
    </script>
@endsection
@section('body')
<body class="easyui-layout">
    <div data-options="region:'north',border:false" class="app-north">
        <div id="app-topmenu" class="easyui-panel" data-options="fit:true,border:false">
            <ul>
                <li class="app-logo">后台管理系统</li>
                <li class="app-nav">
                <?php
                foreach($top_menus as $_m){
                    echo '<span data-id="'.$_m->id.'">'.$_m->name.'</span>';
                }
                ?>
                </li>
                <li class="app-theme">
                    <select id="app-theme-combobox" class="easyui-combobox" data-options="editable:false,panelHeight:'auto',onChange:app_theme_change, formatter:app_theme_init">
                        <option value='default'>Default</option>
                        <option value='gray'>Gray</option>
                        <option value='bootstrap'>Bootstrap</option>
                        <option value='metro'>Metro</option>
                    </select>
                </li>
                <li class="app-welcome">您好！ XXX [超级管理员] | [退出]</li>
            </ul>
        </div>
    </div>
    <div data-options="region:'west',iconCls:'icon-house',split:true,title:'&nbsp;'" class="app-west">
        <ul id="left_menutree" class="easyui-tree"></ul>
    </div>
    <div data-options="region:'east',split:true,collapsed:true,title:'East'" class="app-east">
        <div class="easyui-accordion" data-options="fit:true,border:false">
            <div title="Title1">content1</div>
            <div title="Title2" data-options="selected:true">content2</div>
            <div title="Title3">content3</div>
        </div>
    </div>
    <div data-options="region:'south',border:false" class="app-south"></div>
    <div data-options="region:'center',title:'About'">
        <div id="app-main-tabs" class="easyui-tabs" data-options="tabPosition:'bottom',fit:true,border:false,plain:true"></div>
    </div>
</body>
@endsection