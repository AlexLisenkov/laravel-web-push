<?php

namespace AlexLisenkov\LaravelWebPush;

class Constants
{
    const CONFIG_KEY = 'laravel-web-push';
    const DEFAULT_EXPIRE = 43200;
    const SALT_BYTE_LENGTH = 16;
    const DEFAULT_TTL = 2419200;
    const PADDED_PAYLOAD_LENGTH = 3052;
}
