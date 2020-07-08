<!DOCTYPE html>
<html class="h-100">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="locale" content="{{ App::getLocale() }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>

<body class="h-100">
    @include('layouts.header')
    <div id="app"></div>
    @include('layouts.footer')
    <script src="{{ mix('js/app.js') }}"></script>
</body>

</html>
