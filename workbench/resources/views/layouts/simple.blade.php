<html class="dark" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
</head>

<body class="bg-background-dark text-white overflow-x-hidden font-body antialiased">

    @sections('top-bar')
    @sections('header')

    @yield('content')

    @sections('footer')
</body>

</html>
