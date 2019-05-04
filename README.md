# Send Push Notification in Laravel
[![Total Downloads](https://poser.pugx.org/alexlisenkov/laravel-web-push/downloads)](https://packagist.org/packages/alexlisenkov/laravel-web-push)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/AlexLisenkov/laravel-web-push/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/AlexLisenkov/laravel-web-push/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/AlexLisenkov/laravel-web-push/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/AlexLisenkov/laravel-web-push/?branch=master)
[![build](https://travis-ci.org/AlexLisenkov/laravel-web-push.svg?branch=master)](https://travis-ci.org/AlexLisenkov/laravel-web-push)

[![More info](https://developers.google.com/web/fundamentals/push-notifications/images/svgs/server-to-push-service.svg)](https://developers.google.com/web/fundamentals/push-notifications/web-push-protocol)

The `alexlisenkov/laravel-web-push` package is a package to send push notifications.
Send out push messages as a standalone package. Use this if you dont work with laravel notification channels.

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

## Quick guide
A message can be created by creating a new `AlexLisenkov\LaravelWebPush\PushMessage` class.

Please see [this MDN doc](https://developer.mozilla.org/en-US/docs/Web/API/ServiceWorkerRegistration/showNotification#Parameters) or the [Living Standards](https://notifications.spec.whatwg.org/#dictdef-notificationoptions) to see all available options.
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use AlexLisenkov\LaravelWebPush\PushMessage;
use AlexLisenkov\LaravelWebPush\PushSubscription;

class PushMessageController
{
    public function sendPushMessage(): Response
    {
        // Create a subscription
        $subscription = new PushSubscription(
            "endpoint",
            "p256dh",
            "auth"
        );
        
        // Create a message
        $message = new PushMessage();
        $message->setTitle('Hello World');
        $message->setBody('This message is sent using web push');
        $message->setIcon('https://placekitten.com/75/75');
        
        // We can either use the message to send it to a subscription
        $message->sendTo($subscription)->wait();
        
        // Or send the subscription a message
        $subscription->sendMessage($message)->wait();
        
        return response('ok');
    }
}

```

## Creating message objects
A message can be created by creating a new class that extends the `AlexLisenkov\LaravelWebPush\PushMessage` class.
Please see [this MDN doc](https://developer.mozilla.org/en-US/docs/Web/API/ServiceWorkerRegistration/showNotification#Parameters) or the [Living Standards](https://notifications.spec.whatwg.org/#dictdef-notificationoptions) to see all available options.
```php
<?php

namespace App\Http\Controllers;

use AlexLisenkov\LaravelWebPush\PushMessage;

class ExampleMessage extends PushMessage
{
    protected $title = 'Hello world';

    protected $body = 'This message is sent using web push';

    protected $icon = 'https://placekitten.com/75/75';
    
    // Or overwrite a getter
    public function getData()
    {
        return User()->name;
    }
}

```

## Creating a subscription
The `AlexLisenkov\LaravelWebPush\PushSubscription` is used to create a new subscription. 
```php
<?php
use AlexLisenkov\LaravelWebPush\PushSubscription;

new PushSubscription(
        "endpoint",
        "p256dh",
        "auth"
    );
```

It is also possible for you to implement `AlexLisenlov\LaravelWebPush\Contracts\PushSubscriptionContract` into any class. 
For example on a model.

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

## Service worker
Show a notification to the subscriber by adding an event listener in your service worker.
```js
self.addEventListener('push', function(e) {
    let data = e.data.json();
    
    self.registration.showNotification(data.title, data.options);
});
```
