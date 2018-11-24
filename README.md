# Send Push Notification in Laravel
[![Total Downloads](https://poser.pugx.org/alexlisenkov/laravel-web-push/downloads)](https://packagist.org/packages/alexlisenkov/laravel-web-push)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/AlexLisenkov/laravel-web-push/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/AlexLisenkov/laravel-web-push/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/AlexLisenkov/laravel-web-push/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/AlexLisenkov/laravel-web-push/?branch=master)
[![build](https://travis-ci.org/AlexLisenkov/laravel-web-push.svg?branch=master)](https://travis-ci.org/AlexLisenkov/laravel-web-push)

[![More info](https://developers.google.com/web/fundamentals/push-notifications/images/svgs/server-to-push-service.svg)](https://developers.google.com/web/fundamentals/push-notifications/web-push-protocol)

The `alexlisenkov/laravel-web-push` package is a package to send push notifications.
This package offers you to send notifications quickly by creating your message and subscriptions.

If you are new to the Web Push Protocol please [read about the fundamentals](https://developers.google.com/web/fundamentals/push-notifications/web-push-protocol).

## Installation

```bash
composer require alexlisenkov/laravel-web-push
```

```bash
php artisan vendor:publish --provider="AlexLisenkov\LaravelWebPush\LaravelWebPushServiceProvider"
```

## Configuration
To send out Web Push notifications you need to generate yourself an identity.
The simplest thing to do is to visit [https://web-push-codelab.glitch.me](https://web-push-codelab.glitch.me/)

Open up `config/laravel-web-push.php`

Copy the public key and private key into your configuration. Please note that this public key is the same as you will use in the applicationServerKey in the [JavaScript pushManager api](https://developer.mozilla.org/en-US/docs/Web/API/PushManager).

```php
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
```

# Sending a Web Push

## Creating a message
A message can be created by creating a new class that extends the `AlexLisenkov\LaravelWebPush\PushMessage` class.
Please see [this MDN doc](https://developer.mozilla.org/en-US/docs/Web/API/ServiceWorkerRegistration/showNotification#Parameters) to see all available options.
```php
<?php

namespace App\Http\Controllers;

use AlexLisenkov\LaravelWebPush\PushMessage;

class ExampleMessage extends PushMessage
{
    protected $title = 'Hello world';

    protected $body = 'This message is sent using web push';

    protected $iconPath = 'https://placekitten.com/75/75';
}

```

## Creating a subscription
The `AlexLisenkov\LaravelWebPush\PushSubscription` is used to create a new subscription. 
If you rather want to use your own class you can implement the `AlexLisenlov\LaravelWebPush\Contracts\PushSubscriptionContract`.

```php
<?php
use AlexLisenkov\LaravelWebPush\PushSubscription;

new PushSubscription(
        "endpoint",
        "p256dh",
        "auth"
    );
```

## Sending a notification
Now that we have a subscriber and a message, we can send it out.

```php
<?php
use AlexLisenkov\LaravelWebPush\PushSubscription;

// Create a new message
$message = new ExampleMessage();

// Create a new subscription
$subscription = new PushSubscription(
        "endpoint",
        "p256dh",
        "auth"
    );

// We can either use the message to send it to a subscription
$message->sendTo($subscription);

// Or send the subscription a message
$subscription->sendMessage($message);
```

# TODO:
- Create a `SendsMessages` trait to use in combination with the `PushSubscriptionContract`
- Messages should be able to implement `ShouldQueue` to send messages to queue
- `PushMessage` is missing the [actions](https://developer.mozilla.org/en-US/docs/Web/API/ServiceWorkerRegistration/showNotification#Parameters) value, this should be added
- Create more docs on how to use and install
- Create exceptions for missing configuration variables
