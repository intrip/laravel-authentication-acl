{{-- Layout base admin panel --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">


    {{ HTML::style('packages/jacopo/authentication/css/bootstrap.min.css') }}
    {{ HTML::style('packages/jacopo/authentication/css/style.css') }}
    {{ HTML::style('packages/jacopo/authentication/css/baselayout.css') }}
    {{ HTML::style('packages/jacopo/authentication/css/fonts.css') }}
    {{ HTML::style('//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css') }}

    @yield('head_css')
    {{-- End head css --}}

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
</head>

    <body>
        {{-- navbar --}}
        @include('authentication::admin.layouts.navbar')

        {{-- content --}}
        <div class="container-fluid">
            @yield('container')
        </div>

        {{-- Start footer scripts --}}
        @yield('before_footer_scripts')

        {{ HTML::script('packages/jacopo/authentication/js/jquery-1.10.2.min.js') }}
        {{ HTML::script('packages/jacopo/authentication/js/bootstrap.min.js') }}

        @yield('footer_scripts')
        {{-- End footer scripts --}}
    </body>
</html>