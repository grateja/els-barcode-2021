<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- <link rel="stylesheet" href="{{ asset('css/bootstrap-theme.min.css') }}"> -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <title>{{ config('app.name') }}</title>
    @yield('styles')
</head>
<body>

    @if(auth()->user())
    <nav class="navbar navbar-default">
        <div class="container">

            <!-- Brand/logo -->
            <div class="navbar-header">
                <span class="navbar-brand" href="#">{{auth()->user()->name}}</span>
            </div>

            <!-- Collapsible Navbar -->

            <form class="navbar-form navbar-right"  action="/logout" method="post">
                {{csrf_field()}}
                <input type="submit" value="Log out" class="btn btn-default">
            </form>

        </div>
    </nav>
    <hr>
    @endif

    <div class="ribbon">
    </div>
    @yield('content')
    @yield('scripts')
</body>
</html>
