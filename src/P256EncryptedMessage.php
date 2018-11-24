<?php

namespace AlexLisenkov\LaravelWebPush;

use AlexLisenkov\LaravelWebPush\Contracts\P256EncryptedMessageContract;
use Base64Url\Base64Url;
use Elliptic\EC\KeyPair;

class P256EncryptedMessage implements P256EncryptedMessageContract
{
    /**
     * @var KeyPair
     */
    private $private;
    /**
     * @var KeyPair
     */
    private $subscriber;
    /**
     * @var string
     */
    private $salt;
    /**
     * @var string
     */
    private $auth_token;
    /**
     * @var string
     */
    private $payload;
    /**
     * @var string
     */
    private $cypher;

    /**
     * P256EncryptedMessage constructor.
     *
     * @param KeyPair $private
     * @param KeyPair $subscriber
     * @param string $auth_token Decoded version of auth token
     * @param string $salt 16 byte salt
     * @param string $payload
     */
    public function __construct(
        KeyPair $private,
        KeyPair $subscriber,
        string $auth_token,
        string $salt,
        string $payload
    ) {
        $this->private = $private;
        $this->subscriber = $subscriber;
        $this->salt = $salt;
        $this->auth_token = $auth_token;
        $this->payload = $payload;
    }

    /**
     * @return int
     */
    public function getCypherLength(): int
    {
        return mb_strlen($this->getCypher(), '8bit');
    }

    /**
     * @return string
     */
    public function getCypher(): string
    {
        if (!$this->cypher) {
            $payload = $this->padPayload($this->payload);

            $cypher = openssl_encrypt(
                $payload,
                'aes-128-gcm',
                $this->getContentEncryptionKey(),
                OPENSSL_RAW_DATA,
                $this->getNonce(),
                $tag
            );

            $this->cypher = $cypher . $tag;
        }

        return $this->cypher;
    }

    /**
     * @param $payload
     *
     * @return string
     */
    private function padPayload($payload): string
    {
        $payloadLen = mb_strlen($payload, '8bit');
        $padLen = 3052 - $payloadLen;

        return pack('n*', $padLen) . str_pad($payload, $padLen + $payloadLen, chr(0), STR_PAD_LEFT);
    }

    /**
     * The content encryption key (CEK) is the key that will ultimately be used to encrypt our payload.
     *
     * @return string
     */
    public function getContentEncryptionKey(): string
    {
        $keyLabelInfo = 'Content-Encoding: aesgcm' . chr(0) . 'P-256' . $this->getContext();

        return $this->hkdf($this->salt, $this->getPseudoRandomKey(), $keyLabelInfo, 16);
    }

    /**
     * The "context" is a set of bytes that is used to calculate two values later on in the encryption browser.
     * It's essentially an array of bytes containing the subscription public key and the local public key.
     */
    public function getContext(): string
    {
        $len = chr(0) . 'A';

        return chr(0) . $len . $this->getSubscriberPublicKey() . $len . $this->getPublicKey();
    }

    /**
     * @return string
     */
    private function getSubscriberPublicKey(): string
    {
        return hex2bin($this->subscriber->getPublic('hex'));
    }

    /**
     * @return string
     */
    public function getPublicKey(): string
    {
        return hex2bin($this->private->getPublic('hex'));
    }

    /**
     * @param string $salt
     * @param string $ikm
     * @param string $info
     * @param int $length
     *
     * @return string
     */
    private function hkdf(string $salt, string $ikm, string $info, int $length): string
    {
        // extract
        $prk = hash_hmac('sha256', $ikm, $salt, true);

        // expand
        return mb_substr(hash_hmac('sha256', $info . chr(1), $prk, true), 0, $length, '8bit');
    }

    /**
     * The Pseudo Random Key (PRK) is the combination of the push subscription's auth secret,
     * and the shared secret.
     *
     * @return string
     */
    public function getPseudoRandomKey(): string
    {
        $info = 'Content-Encoding: auth' . chr(0);

        return $this->hkdf($this->auth_token, $this->getSharedSecret(), $info, 32);
    }

    /**
     * @return bool|string
     */
    public function getSharedSecret(): string
    {
        $shared_secret = $this->private->derive($this->subscriber->getPublic());

        return hex2bin(str_pad(gmp_strval($shared_secret->toString(), 16), 64, '0', STR_PAD_LEFT));
    }

    /**
     * A nonce is a value that prevents replay attacks as it should only be used once.
     *
     * @return string
     */
    public function getNonce(): string
    {
        $nonceEncInfo = 'Content-Encoding: nonce' . chr(0) . 'P-256' . $this->getContext();

        return $this->hkdf($this->salt, $this->getPseudoRandomKey(), $nonceEncInfo, 12);
    }

    /**
     * Get Salt
     *
     * @return string
     */
    public function getSalt(): string
    {
        return $this->salt;
    }

    /**
     * Get Salt
     *
     * @return string
     */
    public function getEncodedSalt(): string
    {
        return Base64Url::encode($this->salt);
    }

    /**
     * @return string
     */
    public function getPrivateKey(): string
    {
        return hex2bin($this->private->getPrivate('hex'));
    }

    /**
     * @return string
     */
    public function getEncodedPrivateKey(): string
    {
        return Base64Url::encode(hex2bin($this->private->getPrivate('hex')));
    }

    /**
     * @return string
     */
    public function getEncodedPublicKey(): string
    {
        return Base64Url::encode(hex2bin($this->private->getPublic('hex')));
    }
}
