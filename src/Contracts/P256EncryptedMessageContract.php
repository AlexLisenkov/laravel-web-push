<?php
declare(strict_types=1);

namespace AlexLisenkov\LaravelWebPush\Contracts;

interface P256EncryptedMessageContract
{
    /**
     * @return int
     */
    public function getCypherLength(): int;

    /**
     * @return string
     */
    public function getCypher(): string;

    /**
     * The content encryption key (CEK) is the key that will ultimately be used to encrypt our payload.
     *
     * @return string
     */
    public function getContentEncryptionKey(): string;

    /**
     * The "context" is a set of bytes that is used to calculate two values later on in the encryption browser.
     * It's essentially an array of bytes containing the subscription public key and the local public key.
     */
    public function getContext(): string;

    /**
     * @return string
     */
    public function getPublicKey(): string;

    /**
     * The Pseudo Random Key (PRK) is the combination of the push subscription's auth secret,
     * and the shared secret.
     *
     * @return string
     */
    public function getPseudoRandomKey(): string;

    /**
     * @return bool|string
     */
    public function getSharedSecret(): string;

    /**
     * A nonce is a value that prevents replay attacks as it should only be used once.
     *
     * @return string
     */
    public function getNonce(): string;

    /**
     * Get Salt
     *
     * @return string
     */
    public function getSalt(): string;

    /**
     * Get Salt
     *
     * @return string
     */
    public function getEncodedSalt(): string;

    /**
     * @return string
     */
    public function getPrivateKey(): string;

    /**
     * @return string
     */
    public function getEncodedPrivateKey(): string;

    /**
     * @return string
     */
    public function getEncodedPublicKey(): string;
}
