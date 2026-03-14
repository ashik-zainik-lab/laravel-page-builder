<html class="dark" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords" content="{{ $meta_keywords ?? '' }}" />
    <meta name="description" content="{{ $meta_description ?? '' }}" />
    <meta name="author" content="{{ $url ?? config('app.url') }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>
        {{ $meta_title ?? ($title ?? '') . ' | ' . config('app.name') }}
    </title>

    @stack('style')
</head>

<body class="bg-background-dark text-white overflow-x-hidden font-body antialiased">

    @sections('header')

    @yield('content')

    @sections('footer')

    @stack('script')
</body>

</html>
