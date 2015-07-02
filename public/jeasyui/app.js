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
function date_formatter(unixTime, onlydate, timeZone) {
	if (typeof (timeZone) == 'number'){
		unixTime = parseInt(unixTime) + parseInt(timeZone)*3600;
	}
	var time = new Date(unixTime * 1000);
	var str = "";
	str += time.getUTCFullYear() + "-";
	str += (time.getUTCMonth()+1) + "-";
	str += time.getUTCDate();
	if (onlydate !== true)
	{
		str += " " + time.getUTCHours() + ":";
		str += time.getUTCMinutes() + ":";
		str += time.getUTCSeconds();
	}
	return str;
}
function yes_or_no_formatter(v){
    return v? '是': '否';
}