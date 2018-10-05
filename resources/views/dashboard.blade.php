<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link href='https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Material+Icons' rel="stylesheet">

        <title>ShopWatch</title>

        <link href="{{ url('css/app.css') }}" rel="stylesheet">
        <script>
            window.Laravel = <?php echo json_encode([
              'csrfToken' => csrf_token(),
            ]); ?>
        </script>
        <script>
            const AUTH_ID = '{{ Auth::user()->id }}';
            const AUTH_NAME = '{{ Auth::user()->name }}';
            const AUTH_EMAIL = '{{ Auth::user()->email }}';
            const AUTH_ROLE = '{{ Auth::user()->role }}';
        </script>
    </head>
    <body>
        <div id="app">
            <dashboard></dashboard>
        </div>

        <script src="{{ url('js/app.js') }}"></script>
    </body>
</html>
