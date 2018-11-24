<?php
declare(strict_types=1);

namespace AlexLisenkov\LaravelWebPush\Contracts;

use AlexLisenkov\LaravelWebPush\PushSubscription;
use GuzzleHttp\Promise\PromiseInterface;

interface PushSubscriptionContract
{
    /**
     * @param PushMessageContract $push_message
     *
     * @return PromiseInterface
     */
    public function sendMessage(PushMessageContract $push_message): PromiseInterface;

    /**
     * Define the endpoint
     *
     * @return string
     */
    public function getEndpoint(): string;

    /**
     * Define public key for the subscriber
     *
     * @return string
     */
    public function getP256dh(): string;

    /**
     * Define the auth string
     *
     * @return string
     */
    public function getAuth(): string;

    /**
     * Set Endpoint
     *
     * @param string $endpoint
     *
     * @return PushSubscription
     */
    public function setEndpoint(string $endpoint): PushSubscription;

    /**
     * Set P256dh
     *
     * @param string $p256dh
     *
     * @return PushSubscription
     */
    public function setP256dh(string $p256dh): PushSubscription;

    /**
     * Set Auth
     *
     * @param string $auth
     *
     * @return PushSubscription
     */
    public function setAuth(string $auth): PushSubscription;

    /**
     * @return string
     */
    public function getAudience(): string;
}
