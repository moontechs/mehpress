@isset($navigation)
    @if($navigation->next && $navigation->next->url)
        <a class="relative inline-block font-medium md:text-lg text-black before:absolute before:bottom-0.5 before:start-0 before:-z-1 before:w-full before:h-1 before:bg-yellow-400 hover:before:bg-black focus:outline-hidden focus:before:bg-black dark:text-white dark:hover:before:bg-white dark:focus:before:bg-white"
           href="{{ $navigation->next->url }}">
            {{ $navigation->next->title }}
        </a>
    @else
        <span class="relative inline-block font-medium md:text-lg text-gray-400 before:absolute before:bottom-0.5 before:start-0 before:-z-1 before:w-full before:h-1 before:bg-yellow-400 dark:text-neutral-600"></span>
    @endif

    @if($navigation->previous && $navigation->previous->url)
        <a class="relative inline-block font-medium md:text-lg text-black before:absolute before:bottom-0.5 before:end-0 before:-z-1 before:w-full before:h-1 before:bg-yellow-400 hover:before:bg-black focus:outline-hidden focus:before:bg-black dark:text-white dark:hover:before:bg-white dark:focus:before:bg-white"
           href="{{ $navigation->previous->url }}">
            {{ $navigation->previous->title }}
        </a>
    @else
        <span class="relative inline-block font-medium md:text-lg text-gray-400 before:absolute before:bottom-0.5 before:end-0 before:-z-1 before:w-full before:h-1 before:bg-yellow-400 dark:text-neutral-600"></span>
    @endif
@endisset
