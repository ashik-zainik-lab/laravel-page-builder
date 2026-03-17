<html @pbEditorClass lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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

    <!-- Fonts and Icons -->
    @themeFont
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        accent: {
                            DEFAULT: '#3b82f6',
                            hover: '#2563eb',
                        },
                    },
                }
            }
        }
    </script>

    @stack('content_for_head')
</head>

<body class="antialiased bg-white text-gray-900">

    @sections('header')

    @yield('content')

    @sections('footer')

</body>

</html>
