<?php
/*
 * To generate a application server keys
 * Visit: https://web-push-codelab.glitch.me/
 */

return [
    'public_key' => '',
    'private_key' => '',
    'subject' => config('APP_URL', 'mailto:me@website.com'),
    'expiration' => 43200,
    'TTL' => 2419200,
];
