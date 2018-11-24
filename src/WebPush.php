<?php

namespace AlexLisenkov\LaravelWebPush;

use AlexLisenkov\LaravelWebPush\Contracts\JWTGeneratorContract;
use AlexLisenkov\LaravelWebPush\Contracts\P256EncryptedMessageBuilderContract;
use AlexLisenkov\LaravelWebPush\Contracts\PushMessageContract;
use AlexLisenkov\LaravelWebPush\Contracts\PushSubscriptionContract;
use AlexLisenkov\LaravelWebPush\Contracts\WebPushContract;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Request;
use Illuminate\Contracts\Config\Repository as ConfigRepository;

class WebPush implements WebPushContract
{
    /**
     * @var ConfigRepository
     */
    private $config_repository;
    /**
     * @var P256EncryptedMessageBuilderContract
     */
    private $encrypted_message_builder;
    /**
     * @var JWTGeneratorContract
     */
    private $JWT_generator;
    /**
     * @var Client
     */
    private $client;

    /**
     * WebPush constructor.
     *
     * @param ConfigRepository $config_repository
     * @param P256EncryptedMessageBuilderContract $encrypted_message_builder
     * @param JWTGeneratorContract $JWT_generator
     * @param Client $client
     */
    public function __construct(
        ConfigRepository $config_repository,
        P256EncryptedMessageBuilderContract $encrypted_message_builder,
        JWTGeneratorContract $JWT_generator,
        Client $client
    ) {
        $this->config_repository = $config_repository;
        $this->encrypted_message_builder = $encrypted_message_builder;
        $this->JWT_generator = $JWT_generator;
        $this->client = $client;
    }

    public function sendMessage(
        PushMessageContract $message,
        PushSubscriptionContract $push_subscription
    ): PromiseInterface {
        $encryptedMessage = $this->encrypted_message_builder
            ->withPublicKey($push_subscription->getP256dh())
            ->withAuthToken($push_subscription->getAuth())
            ->build($message->toJson());

        $jwt = $this->JWT_generator
            ->withAudience($push_subscription->getAudience())
            ->serialize();

        $headers = [
            'Content-Type' => 'application/octet-stream',
            'Content-Encoding' => 'aesgcm',
            'Authorization' => 'WebPush ' . $jwt,
            'Encryption' => 'salt=' . $encryptedMessage->getEncodedSalt(),
            'Crypto-Key' => 'dh=' . $encryptedMessage->getEncodedPublicKey() . ';p256ecdsa=' . $this->getConfigVariable('public_key'),
            'Content-Length' => 'dh=' . $encryptedMessage->getCypherLength(),
            'TTL' => $this->getConfigVariable('TTL', Constants::DEFAULT_TTL),
        ];

        if ($topic = $message->getTopic()) {
            $headers['Topic'] = $topic;
        }

        if ($urgency = $message->getUrgency()) {
            $headers['Urgency'] = $urgency;
        }

        $request = new Request('POST', $push_subscription->getEndpoint(), $headers, $encryptedMessage->getCypher());

        return $this->client->sendAsync($request);
    }

    /**
     * @param string $key
     *
     * @param $default
     *
     * @return mixed
     */
    private function getConfigVariable(string $key, $default = null)
    {
        return $this->config_repository->get(Constants::CONFIG_KEY . '.' . $key, $default);
    }

}
