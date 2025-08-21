<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $blog->title }}</title>

        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body>

    @include('default.header')

    <main id="content">
        <div class="flex min-h-screen items-center justify-center w-full">
            <div class="w-full max-w-4xl pt-10 px-4 sm:px-6 lg:px-8">
                <div class="mt-10 sm:mt-14">
                    @yield('content')
                </div>
            </div>
        </div>
    </main>

    </body>
</html>
