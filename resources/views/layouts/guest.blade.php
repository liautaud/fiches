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
    <style>
        .mdl-layout {
            align-items: center;
            justify-content: center;
        }

        .mdl-layout__content {
            padding: 24px;
            flex: none;
        }

        p.message {
            position: absolute;
            bottom: 0;
            right: 20px;
            color: #aaa;
            text-align: right;
        }
    </style>
</head>

<body>
    @yield('content')

    @if(session('message'))
    <div id="message-snackbar" class="mdl-js-snackbar mdl-snackbar">
        <div class="mdl-snackbar__text"></div>
        <button class="mdl-snackbar__action" type="button"></button>
    </div>
    @endif

    <script src="https://code.getmdl.io/1.2.1/material.min.js"></script>
    @if(session('message'))
    <script>
        window.onload = function () {
            document.getElementById('message-snackbar').MaterialSnackbar.showSnackbar({
                message: {!! json_encode(session('message')) !!}
            });
        };
    </script>
    @endif
</body>
</html>
