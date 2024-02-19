<!doctype html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Disable tap highlight on IE -->
        <meta name="msapplication-tap-highlight" content="no">

        <!-- Color the status bar on mobile devices -->
        <meta name="theme-color" content="#0054a4">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>
            @yield('title', 'Home') -
            {{ config('app.name', 'Laravel') }}
        </title>

        <!-- Styles -->
        <link href="https://fonts.googleapis.com/css?family=Roboto:400,700,Montserrat:300,400,600" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" rel="stylesheet">
        <link href="{{ Helper::asset('assets/css/app.css') }}" rel="stylesheet">
    </head>
    <body class="{{ empty($body_class) ? '' : $body_class }}">
        <div id="app">
            @if (Auth::check())
                @include('partials.header')
            @endif

            <div id="site-content">
                @yield('content')
            </div>
        </div>

        @if (Auth::check())
            <div id="site-footer">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            <p class="copyright"><img src="{{ asset('assets/img/logo-notext.png') }}" alt="St Vincent de Paul Society">&copy; <strong>{{ date('Y') }}</strong> St Vincent de Paul Society</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Scripts -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.1/jquery.form.min.js" integrity="sha256-8G/BdtcUMWw3c6j5nBvVtzaoj3sq/kX6xNN2FQ0w0MY=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.15/js/jquery.dataTables.min.js" integrity="sha256-j007R7R6ijEWPa1df7FeJ6AFbQeww0xgif2SJWZOhHw=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.15/js/dataTables.bootstrap.min.js" integrity="sha256-X/58s5WblGMAw9SpDtqnV8dLRNCawsyGwNqnZD0Je/s=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/4.4.0/bootbox.min.js" integrity="sha256-4F7e4JsAJyLUdpP7Q8Sah866jCOhv72zU5E8lIRER4w=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/rome/2.1.22/rome.min.js" integrity="sha256-fIfKV1OHGVdM4hUB9JvDT+eR2iB60OuwqGBUrNJUNrE=" crossorigin="anonymous"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/datepicker/0.6.5/datepicker.min.js" integrity="sha256-/7FLTdzP6CfC1VBAj/rsp3Rinuuu9leMRGd354hvk0k=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js" integrity="sha256-+mWd/G69S4qtgPowSELIeVAv7+FuL871WXaolgXnrwQ=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/mustache.js/2.3.0/mustache.min.js" integrity="sha256-iaqfO5ue0VbSGcEiQn+OeXxnxAMK2+QgHXIDA5bWtGI=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/autosize.js/4.0.0/autosize.min.js" integrity="sha256-F7Bbc+3hGv34D+obsHHsSm3ZKRBudWR7e2H0fS0beok=" crossorigin="anonymous"></script>
        @yield('prejs')

        <script src="{{ Helper::asset('assets/js/vendors.js') }}"></script>
        <script src="{{ Helper::asset('assets/js/app.js') }}"></script>

        @yield('scripts')

        @if (App::environment('production'))
            <script>
                window.ga=function(){ga.q.push(arguments)};ga.q=[];ga.l=+new Date;
                ga('create','UA-109493441-1','auto');ga('send','pageview')
            </script>
            <script src="https://www.google-analytics.com/analytics.js" async defer></script>
        @endif
    </body>
</html>
