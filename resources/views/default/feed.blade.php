@extends('default.layout')

@section('content')
    <div>
        @if($posts->isEmpty())
            <div class="text-center py-20">
                <h2 class="text-2xl font-semibold text-gray-800 dark:text-neutral-200">No posts found</h2>
                <p class="mt-2 text-gray-600 dark:text-neutral-400">There are no posts available at the moment. Please check back later.</p>
            </div>
        @else
            <div class="group relative flex gap-x-5">
                <!-- Icon -->
                <div class="relative group-last:after:hidden after:absolute after:top-8 after:bottom-2 after:start-3 after:w-px after:-translate-x-[0.5px] after:bg-gray-200 dark:after:bg-neutral-700">
                    <div class="relative z-10 size-6 flex justify-center items-center">
                        <img src="/icons/tabler/outline/calendar-week.svg" alt="{{ $monthAndYear }}" />
                    </div>
                </div>
                <!-- End Icon -->

                <!-- Right Content -->
                <div class="grow pb-8 group-last:pb-0">
                    <p class="font-semibold text-lg text-gray-800 dark:text-neutral-200">
                        {{ $monthAndYear }}
                    </p>

                </div>
                <!-- End Right Content -->
            </div>
        @endif

        @foreach($posts as $post)
            <div class="group relative flex gap-x-5">
                <!-- Icon -->
                <div class="relative group-last:after:hidden after:absolute after:top-8 after:bottom-2 after:start-3 after:w-px after:-translate-x-[0.5px] after:bg-gray-200 dark:after:bg-neutral-700">
                    <div class="relative z-10 size-6 flex justify-center items-center">
                        @if ($post->isPostType())
                            <img src="/icons/tabler/outline/article.svg" alt="Post icon" />
                        @endif

                        @if ($post->isShortType())
                            @php
                                $iconLetter = null;
                                $tries = 0;
                                foreach (str_split($post->description) as $char) {
                                    if (preg_match('/[a-zA-Z]/', $char)) {
                                        $iconLetter = strtolower($char);
                                        break;
                                    }
                                    $tries++;
                                    if ($tries >= 5) {
                                        break;
                                    }
                                }
                                if (!$iconLetter) {
                                    $iconLetter = chr(rand(97, 122));
                                }
                            @endphp
                            <img src="/icons/tabler/outline/square-letter-{{ $iconLetter }}.svg" alt="Short icon" />
                        @endif
                    </div>
                </div>
                <!-- End Icon -->

                <!-- Right Content -->
                <div class="grow pb-8 group-last:pb-0">
                    <h3 class="mb-1 text-xs text-gray-600 dark:text-neutral-400">
                        {{ $post->created_at->format('d F') }}
                    </h3>

                    <a href="{{ $post->getUrl() }}">
                        <p class="font-semibold text-lg text-gray-800 dark:text-neutral-200">
                            @if ($post->isPostType())
                                {{ $post->title }}
                            @endif
                        </p>
                    </a>

                    <a href="{{ $post->getUrl() }}">
                        <div class="mt-1 text-lg text-gray-600 dark:text-neutral-400">
                            @if ($post->isPostType())
                                <p>{{ $post->description }}</p>
                            @endif

                            @if ($post->isShortType())
                                <div class="prose prose-sm dark:prose-invert max-w-none">
                                    <x-markdown>
                                        {!! $post->text !!}
                                    </x-markdown>
                                </div>
                            @endif
                        </div>
                    </a>
                </div>
                <!-- End Right Content -->
            </div>
        @endforeach

        <div class="mt-10 lg:mt-20 flex justify-between">
            @include('default.navigation')
        </div>
    </div>
@endsection
