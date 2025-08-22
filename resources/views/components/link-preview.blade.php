<div class="">
    <a href="{{ $url }}"
       target="_blank"
       rel="noopener noreferrer nofollow"
       class="block group border border-gray-200 rounded-lg p-4 hover:border-gray-300 hover:shadow-md transition-all duration-200 no-underline dark:border-gray-700 dark:hover:border-gray-600 my-4">

        <div class="flex gap-4">
            {{-- Image --}}
            @if($image)
                <div class="flex-shrink-0">
                    <img src="{{ $image }}"
                         alt="{{ $title }}"
                         class="w-20 h-20 object-cover rounded-md bg-gray-100 dark:bg-gray-800"
                         loading="lazy"
                         onerror="this.style.display='none'">
                </div>
            @endif

            {{-- Content --}}
            <div class="flex-1 min-w-0">
                {{-- Site name --}}
                @if($siteName)
                    <div class="text-xs text-gray-500 uppercase tracking-wide font-medium mb-1 dark:text-gray-400">
                        {{ $siteName }}
                    </div>
                @endif

                {{-- Title --}}
                <h3 class="font-semibold text-gray-900 transition-colors duration-200 line-clamp-2 dark:text-white">
                    {{ $title }}
                </h3>

                {{-- Description --}}
                @if($description)
                    <p class="text-gray-600 text-sm mt-2 line-clamp-2 dark:text-gray-400">
                        {{ $description }}
                    </p>
                @endif

                {{-- URL Domain --}}
                <div class="flex items-center mt-2">
                    <span class="text-xs text-gray-500 dark:text-gray-500">
                        {{ parse_url($url, PHP_URL_HOST) }}
                    </span>
                    <svg class="ml-1 w-3 h-3 text-gray-400 group-hover:text-gray-600 transition-colors duration-200"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </div>
            </div>
        </div>
    </a>
</div>

<style>
    /* Line clamp utilities for better text truncation */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
