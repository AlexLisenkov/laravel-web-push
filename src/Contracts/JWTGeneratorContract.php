<?php
declare(strict_types=1);

namespace AlexLisenkov\LaravelWebPush\Contracts;

use Jose\Component\Core\JWK;
use Jose\Component\Signature\JWS;

interface JWTGeneratorContract
{
    /**
     * @param string $aud
     *
     * @return JWTGeneratorContract
     */
    public function withAudience(string $aud): JWTGeneratorContract;

    /**
     * @param int $time
     *
     * @return JWTGeneratorContract
     */
    public function willExpireAt(int $time): JWTGeneratorContract;

    /**
     * @param int $time
     *
     * @return JWTGeneratorContract
     */
    public function willExpireIn(int $time): JWTGeneratorContract;

    /**
     * @return array
     */
    public function getHeader(): array;

    /**
     * @return string
     */
    public function getPayload(): string;

    /**
     * @return JWK
     */
    public function getJWK(): JWK;

    /**
     * @return JWS
     */
    public function getJWS(): JWS;

    /**
     * @return string
     * @throws \Exception
     */
    public function serialize(): string;
}
