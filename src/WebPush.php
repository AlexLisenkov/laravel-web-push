<?php

namespace AlexLisenkov\LaravelWebPush;

use AlexLisenkov\LaravelWebPush\Contracts\JWTGeneratorContract;
use AlexLisenkov\LaravelWebPush\Contracts\P256EncryptedMessageBuilderContract;
use AlexLisenkov\LaravelWebPush\Contracts\PushMessageContract;
use AlexLisenkov\LaravelWebPush\Contracts\PushSubscriptionContract;
use AlexLisenkov\LaravelWebPush\Contracts\WebPushContract;
use AlexLisenkov\LaravelWebPush\Exceptions\InvalidPrivateKeyException;
use AlexLisenkov\LaravelWebPush\Exceptions\InvalidPublicKeyException;
use Base64Url\Base64Url;
use GuzzleHttp\Client;
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
        $private = $this->getConfigVariable('private_key');
        if (!$this->assertPrivateKeyIsCorrect($private)) {
            throw new InvalidPrivateKeyException('Configured private key is incorrect');
        }

        $public = $this->getConfigVariable('public_key');
        if (!$this->assertPublicKeyIsCorrect($public)) {
            throw new InvalidPublicKeyException('Configured public key is incorrect');
        }

        if (!$this->assertPublicKeyIsCorrect($push_subscription->getP256dh())) {
            throw new InvalidPublicKeyException('Subscriber public key is invalid');
        }

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

    /**
     * Assert that the given private key is correct by size
     *
     * @param $private
     *
     * @return bool
     */
    private function assertPrivateKeyIsCorrect($private): bool
    {
        try {
            $private_key_decoded = Base64Url::decode($private);
        } catch (\InvalidArgumentException $exception) {
            return false;
        }

        return mb_strlen($private_key_decoded, '8bit') === 32;
    }

    /**
     * Assert that the given public key is correct by size
     *
     * @param $public
     *
     * @return bool
     */
    private function assertPublicKeyIsCorrect($public): bool
    {
        try {
            $public_key_decoded = Base64Url::decode($public);
        } catch (\InvalidArgumentException $exception) {
            return false;
        }

        return mb_strlen($public_key_decoded, '8bit') === 65;
    }

}
