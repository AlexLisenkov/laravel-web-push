<?php

namespace AlexLisenkov\LaravelWebPush;

use AlexLisenkov\LaravelWebPush\Contracts\P256EncryptedMessageBuilderContract;
use AlexLisenkov\LaravelWebPush\Contracts\P256EncryptedMessageContract;
use Base64Url\Base64Url;
use Elliptic\EC;
use Illuminate\Contracts\Container\Container;

class P256EncryptedMessageBuilder implements P256EncryptedMessageBuilderContract
{
    /**
     * @var string
     */
    private $public_key;
    /**
     * @var string
     */
    private $auth_token;
    /**
     * @var EC
     */
    private $ec;
    /**
     * @var Container
     */
    private $container;

    /**
     * P256EncryptedMessageBuilder constructor.
     *
     * @param EC $ec
     * @param Container $container
     */
    public function __construct(EC $ec, Container $container)
    {
        $this->ec = $ec;
        $this->container = $container;
    }

    public function withPublicKey(string $public_key): P256EncryptedMessageBuilderContract
    {
        $this->public_key = $public_key;

        return $this;
    }

    public function withAuthToken(string $auth_token): P256EncryptedMessageBuilderContract
    {
        $this->auth_token = $auth_token;

        return $this;
    }

    public function build(string $payload): P256EncryptedMessageContract
    {
        $subscriber_public_key = Base64Url::decode($this->public_key);
        $subscriber_auth_token = Base64Url::decode($this->auth_token);

        $subscriber_p256 = $this->ec->keyFromPublic(bin2hex($subscriber_public_key), 'hex');
        $server_p256 = $this->ec->genKeyPair();

        $salt = random_bytes(Constants::SALT_BYTE_LENGTH);

        return $this->container->make(P256EncryptedMessageContract::class, [
            'private' => $server_p256,
            'subscriber' => $subscriber_p256,
            'auth_token' => $subscriber_auth_token,
            'salt' => $salt,
            'payload' => $payload,
        ]);
    }
}
