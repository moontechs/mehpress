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
}
