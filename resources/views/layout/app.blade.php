<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Easy PHP APP')</title>
    <link rel="stylesheet" type="text/css" href="/jeasyui/themes/default/easyui.css" data-theme="default" />
    <link rel="stylesheet" type="text/css" href="/jeasyui/themes/black/easyui.css" data-theme="black" />
    <link rel="stylesheet" type="text/css" href="/jeasyui/themes/bootstrap/easyui.css" data-theme="bootstrap" />
    <link rel="stylesheet" type="text/css" href="/jeasyui/themes/gray/easyui.css" data-theme="gray" />
    <link rel="stylesheet" type="text/css" href="/jeasyui/themes/metro/easyui.css" data-theme="metro" />
    <link rel="stylesheet" type="text/css" href="/jeasyui/themes/icon.css" />
@section('stylesheet')
    <link rel="stylesheet" type="text/css" href="/jeasyui/app.css" />
@show
    <script type="text/javascript" src="/jeasyui/jquery.min.js"></script>
    <script type="text/javascript" src="/js/js.cookie.js"></script>
    <script type="text/javascript" src="/jeasyui/jquery.easyui.min.js"></script>
@section('javascript')
    <script type="text/javascript" src="/jeasyui/app.js"></script>
    <script type="text/javascript">
        var _token = '{!! csrf_token() !!}';
    </script>
@show
</head>
@yield('body')
</html>