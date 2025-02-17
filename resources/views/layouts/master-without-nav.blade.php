<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts.title-meta')
    @include('layouts.head')
</head>

@section('body')

<body class="authentication-bg">
    @show
    @yield('content')
    @include('layouts.vendor-scripts')
</body>
<script>
    window.onload = function() {
        $('.loader').fadeOut(500)
    }
</script>
</html>
