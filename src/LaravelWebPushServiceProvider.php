<?php

namespace AlexLisenkov\LaravelWebPush;

use AlexLisenkov\LaravelWebPush\Contracts\JWTGeneratorContract;
use AlexLisenkov\LaravelWebPush\Contracts\P256EncryptedMessageBuilderContract;
use AlexLisenkov\LaravelWebPush\Contracts\P256EncryptedMessageContract;
use AlexLisenkov\LaravelWebPush\Contracts\WebPushContract;
use Elliptic\EC;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Illuminate\Support\ServiceProvider;

class LaravelWebPushServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(WebPushContract::class, WebPush::class);
        $this->app->bind(P256EncryptedMessageBuilderContract::class, P256EncryptedMessageBuilder::class);
        $this->app->bind(P256EncryptedMessageContract::class, P256EncryptedMessage::class);
        $this->app->bind(JWTGeneratorContract::class, JWTGenerator::class);
        $this->app->bind(EC::class, function () {
            return new EC('p256');
        });

        $this->publishes([
            __DIR__ . '/../config/laravel-web-push.php' => config_path(Constants::CONFIG_KEY . '.php'),
        ]);
    }
}
