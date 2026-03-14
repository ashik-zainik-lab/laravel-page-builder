<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Page Builder Editor - {{ config('app.name') }}</title>

    <!-- Page Builder Assets -->
    {{ Coderstm\PageBuilder\PageBuilder::css() }}
</head>

<body class="bg-primary-50">
    <div id="editor"></div>

    <!-- Page Builder Scripts -->
    {{ Coderstm\PageBuilder\PageBuilder::js() }}

    <script type="module">
        const config = @json($config);

        const editor = PageBuilder.init({
            container: '#editor',
            ...config,
        });
    </script>
</body>

</html>
