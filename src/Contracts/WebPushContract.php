<?php

namespace AlexLisenkov\LaravelWebPush\Contracts;

use GuzzleHttp\Promise\PromiseInterface;

interface WebPushContract
{
    public function sendMessage(
        PushMessageContract $message,
        PushSubscriptionContract $push_subscription
    ): PromiseInterface;
}
