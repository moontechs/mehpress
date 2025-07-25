@extends('default.layout')

@section('content')
    <div>
        <!-- Title -->
        @if($post->isPostType())
            <div>
                <h1 class="text-3xl font-bold text-gray-800 sm:text-4xl dark:text-white">
                    {{ $post->title }}
                </h1>

                <p class="mt-3 text-gray-600 dark:text-neutral-400">
                    {{ $post->description }}
                </p>
            </div>
        @endif
        <!-- End Title -->

        <div class="mt-16 space-y-5 prose dark:prose-invert">
            <x-markdown>
                {!! $post->text !!}
            </x-markdown>
        </div>

        <div class="flex items-center gap-x-4 text-xs mt-6">
            <time datetime="2020-03-16" class="text-gray-500">{{ $post->created_at->format('F d, Y') }}</time>
        </div>

        @if($post->tags && count($post->tags) > 0)
            <div class="flex items-center gap-x-4 text-xs mt-6">
                @foreach($post->tags as $tag)
                    <a href="/?tag={{ $tag }}" class="relative inline-block font-medium md:text-lg text-black before:absolute before:bottom-0.5 before:end-0 before:-z-1 before:w-full before:h-1 before:bg-yellow-400 hover:before:bg-black focus:outline-hidden focus:before:bg-black dark:text-white dark:hover:before:bg-white dark:focus:before:bg-white">
                        {{ $tag }}
                    </a>
                @endforeach
            </div>
        @endif

        <div class="mt-10 lg:mt-20 flex justify-between">
            @include('default.navigation')
        </div>
    </div>
@endsection
