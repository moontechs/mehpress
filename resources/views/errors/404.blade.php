@extends('default.layout')

@section('content')
    <div class="text-center py-10 px-4 sm:px-6 lg:px-8">
        <h1 class="block text-7xl font-bold text-gray-800 sm:text-9xl">404</h1>
        <p class="mt-3 text-gray-600">Nope</p>
        <p class="text-gray-600">Sorry, we couldn't find your page.</p>
        <div class="mt-5 flex flex-col justify-center items-center gap-2 sm:flex-row sm:gap-3">
            <a class="relative inline-block font-medium md:text-lg text-black before:absolute before:bottom-0.5 before:end-0 before:-z-1 before:w-full before:h-1 before:bg-yellow-400 hover:before:bg-black focus:outline-hidden focus:before:bg-black dark:text-white dark:hover:before:bg-white dark:focus:before:bg-white" href="/">
                Back to the main page
            </a>
        </div>
    </div>
@endsection
