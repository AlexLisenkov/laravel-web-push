<?php

namespace AlexLisenkov\LaravelWebPush;

class Constants
{
    public const CONFIG_KEY = 'laravel-web-push';
    public const DEFAULT_EXPIRE = 43200;
    public const SALT_BYTE_LENGTH = 16;
    public const DEFAULT_TTL = 2419200;
    public const PADDED_PAYLOAD_LENGTH = 3052;
}
