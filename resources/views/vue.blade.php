<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="plaid-public-key" content="{{ env('PLAID_PUBLIC_KEY') }}">

    <title>{{ config('app.name', 'Wirehub') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@4.x/css/materialdesignicons.min.css" rel="stylesheet">
</head>
<body>
    <div id="app"></div>
    <a id="hiddenDownloadEl" style="visibility: hidden"></a>

    <script>
        window.user = @json(Auth::user()->load('roles.permissions'))
    </script>

    <!-- Scripts -->
    <script src="https://cdn.plaid.com/link/v2/stable/link-initialize.js"></script>
    <script src="{{ mix('dist/js/app.js') }}"></script>
</body>
</html>
