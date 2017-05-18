<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
    <title>@yield('title') - PROJ1</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:regular,bold,italic,thin,light,bolditalic,black,medium&amp;lang=en">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://code.getmdl.io/1.2.1/material.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @stack('styles')
</head>

<body>
    @yield('content')

    @if ($utilisateur->isGestionnaire())
        @include('shared.gestion-sidebar')
    @else
        @include('shared.user-sidebar')
    @endif

    <div id="message-snackbar" class="mdl-js-snackbar mdl-snackbar">
        <div class="mdl-snackbar__text"></div>
        <button class="mdl-snackbar__action" type="button"></button>
    </div>

    <script src="https://code.getmdl.io/1.2.1/material.min.js"></script>
    <script>
        document.CSRF_TOKEN = {!! json_encode(csrf_token()) !!};
    </script>
    @if(session('message'))
    <script>
        window.addEventListener('load', function () {
            document.getElementById('message-snackbar').MaterialSnackbar.showSnackbar({
                message: {!! json_encode(session('message')) !!}
            });
        });
    </script>
    @endif
    @stack('scripts')
</body>
</html>