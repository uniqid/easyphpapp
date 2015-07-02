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
function date_formatter(v, r) {
	return _date_formatter(v);
}
function yes_or_no_formatter(v){
    return v? '是': '否';
}