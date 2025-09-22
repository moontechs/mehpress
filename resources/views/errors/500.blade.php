@extends('default.layout')

@section('content')
    <div class="text-center py-10 px-4 sm:px-6 lg:px-8">
        <h1 class="block text-7xl font-bold text-gray-800 sm:text-9xl">500</h1>
        <p class="mt-3 text-gray-600">Our bad</p>
        <p class="text-gray-600">
            @if(isset($message) && $message)
                {{ $message }}
            @else
                Server error. Please try again later.
            @endif
        </p>
    </div>
@endsection
