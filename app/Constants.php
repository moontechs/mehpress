<?php

namespace App;

interface Constants
{
    public const CACHE_UNIQUE_TAGS_KEY = 'unique_tags';

    public const POST_TYPE = 'post';

    public const SHORT_POST_TYPE = 'short';

    public const POST_TYPES = [
        self::POST_TYPE,
        self::SHORT_POST_TYPE,
    ];

    public const LINK = 'link';

    public const QUOTE = 'quote';

    public const FEED = 'feed';

    public const NAVIGATION_ACTION_TYPE = 'action';

    public const NAVIGATION_LINK_TYPE = 'link';

    public const NAVIGATION_EXTERNAL_LINK_TYPE = 'external_link';

    public const NAVIGATION_BUTTON_TYPES = [
        self::NAVIGATION_LINK_TYPE,
        self::NAVIGATION_ACTION_TYPE,
        self::NAVIGATION_EXTERNAL_LINK_TYPE,
    ];
}
