<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        @if(isset($blog) && $blog->title)
            <title>{{ $blog->title }}</title>
        @endif

        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        @yield('meta')

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body>

    @if(isset($blog))
        @include('default.header')
    @endif

    <main id="content">
        <div class="flex min-h-screen justify-center w-full">
            <div class="w-full max-w-4xl px-4 sm:px-6 lg:px-8">
                <div class="">
                    @yield('content')

                    @if(isset($blog))
                        @include('default.footer')
                    @endif
                </div>
            </div>
        </div>
    </main>

    </body>
</html>
