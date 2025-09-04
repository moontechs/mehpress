@extends('default.layout')

@section('meta')
    @foreach($post->seo_tags as $seoTag => $value)
        @if(\Illuminate\Support\Str::startsWith($seoTag, 'meta_'))
            @php
                $seoTag = str_replace('meta_', '', $seoTag);
                $seoTagsParts = explode('__', $seoTag, 2);
            @endphp

            <meta {{ $seoTagsParts[0] }}="{{ $seoTagsParts[1] }}" content="{{  $value }}">

            @continue
        @endif

        @if(\Illuminate\Support\Str::startsWith($seoTag, 'link_'))
            @php
                $seoTag = str_replace('link_', '', $seoTag);
                $seoTagsParts = explode('__', $seoTag, 2);
            @endphp

            <link {{ $seoTagsParts[0] }}="{{ $seoTagsParts[1] }}" content="{{  $value }}">

            @continue
        @endif

        <meta {{ $seoTag }}="{{ $value }}">
    @endforeach
@endsection

@section('content')
    <!--    Title -->
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

    <div class="mt-16 space-y-5 prose max-w-full dark:prose-invert">
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
@endsection
