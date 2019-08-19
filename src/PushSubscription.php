<?php

namespace AlexLisenkov\LaravelWebPush;

use AlexLisenkov\LaravelWebPush\Contracts\PushMessageContract;
use AlexLisenkov\LaravelWebPush\Contracts\PushSubscriptionContract;
use AlexLisenkov\LaravelWebPush\Contracts\WebPushContract;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\App;

class PushSubscription implements PushSubscriptionContract
{
    /**
     * @var string
     */
    private $endpoint;
    /**
     * @var string
     */
    private $p256dh;
    /**
     * @var string
     */
    private $auth;

    public function __construct(string $endpoint, string $p256dh, string $auth)
    {
        $this->endpoint = $endpoint;
        $this->p256dh = $p256dh;
        $this->auth = $auth;
    }

    /**
     * @param PushMessageContract $push_message
     *
     * @return PromiseInterface
     */
    public function sendMessage(PushMessageContract $push_message): PromiseInterface
    {
        /** @var WebPushContract $web_push */
        $web_push = App::make(WebPushContract::class);

        return $web_push->sendMessage($push_message, $this);
    }

    /**
     * Define public key for the subscriber
     *
     * @return string
     */
    public function getP256dh(): string
    {
        return $this->p256dh;
    }

    /**
     * Set P256dh
     *
     * @param string $p256dh
     *
     * @return PushSubscription
     */
    public function setP256dh(string $p256dh): PushSubscription
    {
        $this->p256dh = $p256dh;

        return $this;
    }

    /**
     * Define the auth string
     *
     * @return string
     */
    public function getAuth(): string
    {
        return $this->auth;
    }

    /**
     * Set Auth
     *
     * @param string $auth
     *
     * @return PushSubscription
     */
    public function setAuth(string $auth): PushSubscription
    {
        $this->auth = $auth;

        return $this;
    }

    /**
     * @return string
     */
    public function getAudience(): string
    {
        $audience = new Request('get', $this->getEndpoint());

        return $audience->getUri()->getScheme() . '://' . $audience->getUri()->getHost();
    }

    /**
     * Define the endpoint
     *
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * Set Endpoint
     *
     * @param string $endpoint
     *
     * @return PushSubscription
     */
    public function setEndpoint(string $endpoint): PushSubscription
    {
        $this->endpoint = $endpoint;

        return $this;
    }
}
