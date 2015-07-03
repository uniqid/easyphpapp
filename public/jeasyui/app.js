function iframe_tabs(dom, cfg){
    if(typeof cfg.src != 'undefined'){
        //cfg.src = cfg.src + '?_t=' + (new Date().getTime());
        cfg.content = '<iframe scrolling="auto" frameborder="0" src="'+cfg.src+'" style="width:100%;height:100%;"></iframe>';
        delete cfg.src;
    }
    dom.tabs('add', cfg);
    dom.tabs('getSelected').css({'overflow':'hidden'});
}
function iframe_dialog(dom, cfg){
    if(typeof cfg.href != 'undefined'){
        cfg.href = cfg.href + '?_t=' + (new Date().getTime());
        cfg.content = '<iframe scrolling="auto" frameborder="0" src="'+cfg.href+'" style="width:100%;height:100%;"></iframe>';
        delete cfg.href;
    }
    dom.dialog(cfg);
    dom.dialog('open');
    dom.css({'overflow':'hidden'});
}
function dialog_close(id){
    $('#'+id).dialog('close');
}

function date_formatter(v, r) {
	return _date_formatter(v);
}

function _date_formatter(v, fmt){
    (typeof fmt === 'undefined') && (fmt = 'yyyy-MM-dd hh:mm:ss');
    var dt = new Date(v * 1000);
    var fmts = fmt.split(/[\-\/\s\:]/);
    var len  = fmts.length;
    for(var i=0; i<len; i++){
        switch (fmts[i]) {
            case 'yy':  fmt = fmt.replace('yy', (dt.getFullYear()+'').substring(2)); break;
            case 'yyyy':fmt = fmt.replace('yyyy', dt.getFullYear()); break;
            case 'M':   fmt = fmt.replace('M', dt.getMonth()); break;
            case 'MM':  fmt = fmt.replace('MM', dt.getMonth()>9?dt.getMonth():'0'+dt.getMonth()); break;
            case 'd':   fmt = fmt.replace('d', dt.getDate()); break;
            case 'dd':  fmt = fmt.replace('dd', dt.getDate()>9?dt.getDate():'0'+dt.getDate()); break;
            case 'h':   fmt = fmt.replace('h', dt.getHours()); break;
            case 'hh':  fmt = fmt.replace('hh', dt.getHours()>9?dt.getHours():'0'+dt.getHours()); break;
            case 'm':   fmt = fmt.replace('m', dt.getMinutes()); break;
            case 'mm':  fmt = fmt.replace('mm', dt.getMinutes()>9?dt.getMinutes():'0'+dt.getMinutes()); break;
            case 's':   fmt = fmt.replace('s', dt.getSeconds()); break;
            case 'ss':  fmt = fmt.replace('ss', dt.getSeconds()>9?dt.getSeconds():'0'+dt.getSeconds()); break;
        }
    }
    return fmt;
}

function yes_or_no_formatter(v){
    return v? '是': '否';
}
function orderby_formatter(v,r){
    return '<input type="text" onblur="action_orderby('+r.id+',this)" value="'+v+'" data="'+v+'" size="8" style="text-align:center">';
}
function grid_reflesh(){
    if($('table.easyui-datagrid').length > 0){
        $('table.easyui-datagrid').datagrid('reload');
    }
    if($('table.easyui-treegrid').length > 0){
        $('table.easyui-treegrid').treegrid('reload');
    }
}
function _action_delete(url, id, tip, token){
    $.messager.confirm('提示信息', tip, function(result){
        if(!result) return false;
        $.post(url, {id: id,_token:token}, function(res){
            if(res.status > 0){
                $.messager.alert('提示信息', '删除失败', 'error');
            }else{
                //$.messager.alert('提示信息', '删除成功', 'info');
                grid_reflesh();
            }
        }, 'json');
    });
}
function _action_orderby(url, id, _this, token){
    if($(_this).attr('data') == _this.value){
        return;
    }
    $.post(url, {id:id,orderby:_this.value,_token:token}, function(res){
        if(res.status > 0){
            $.messager.alert('提示信息', '设置失败', 'error');
        } else {
            $(_this).attr('data', _this.value);
            grid_reflesh();
        }
    }, 'json');
}
    
function form_submit(url){
    if(!$('#data_form').form('validate')){return false;}
    $.post(url, $("#data_form").serialize(), function(res){
        if(res.status>0){
            $.messager.alert('提示信息', res.data, 'error');
        }else{
            $.messager.alert('提示信息', '保存成功', 'info', function(){
                parent.grid_reflesh();
                parent.dialog_close('dialog_upsert');
            });
        }
    }, 'json');
}

//set theme
var theme  = Cookies.get('theme') || 'default';
function app_theme_set(_theme){
    $('link[rel*=style][data-theme]').each(function(i){
        this.disabled = !(this.getAttribute('data-theme') == _theme);
    });
    $('iframe').contents().find('link[rel*=style][data-theme]').each(function(i){
        this.disabled = !(this.getAttribute('data-theme') == _theme);
    });
}
