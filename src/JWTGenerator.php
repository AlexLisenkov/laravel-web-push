<?php

namespace AlexLisenkov\LaravelWebPush;

use AlexLisenkov\LaravelWebPush\Contracts\JWTGeneratorContract;
use Base64Url\Base64Url;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Core\Converter\StandardConverter;
use Jose\Component\Core\JWK;
use Jose\Component\Signature\Algorithm\ES256;
use Jose\Component\Signature\JWS;
use Jose\Component\Signature\JWSBuilder;
use Jose\Component\Signature\Serializer\CompactSerializer;

class JWTGenerator implements JWTGeneratorContract
{
    /**
     * @var int
     */
    private $expires_at;
    /**
     * @var string
     */
    private $audience;
    /**
     * @var ConfigRepository
     */
    private $config_repository;

    public function __construct(ConfigRepository $config_repository)
    {
        $this->config_repository = $config_repository;
    }

    /**
     * @param string $aud
     *
     * @return JWTGeneratorContract
     */
    public function withAudience(string $aud): JWTGeneratorContract
    {
        $this->audience = $aud;

        return $this;
    }

    /**
     * @param int $time
     *
     * @return JWTGeneratorContract
     */
    public function willExpireAt(int $time): JWTGeneratorContract
    {
        $this->expires_at = $time;

        return $this;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function serialize(): string
    {
        $jsonConverter = new StandardConverter();
        $jwsCompactSerializer = new CompactSerializer($jsonConverter);

        return $jwsCompactSerializer->serialize($this->getJWS(), 0);
    }

    /**
     * @return JWS
     * @throws \Exception
     */
    public function getJWS(): JWS
    {
        $jsonConverter = new StandardConverter();
        $jwsBuilder = new JWSBuilder($jsonConverter, AlgorithmManager::create([new ES256()]));

        return $jwsBuilder
            ->create()
            ->withPayload($this->getPayload())
            ->addSignature($this->getJWK(), $this->getHeader())
            ->build();
    }

    /**
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getPayload(): string
    {
        if (!is_string($this->audience)) {
            throw new \InvalidArgumentException("Audience must be string, " . gettype($this->audience) . " given.");
        }

        if (!$this->getExpiresAt()) {
            $this->willExpireIn($this->getConfigVariable('expiration', Constants::DEFAULT_EXPIRE));
        }

        return json_encode([
            'aud' => $this->audience,
            'exp' => $this->getExpiresAt(),
            'sub' => $this->getConfigVariable('subject', env('APP_URL', 'mailto:name@example.com')),
        ], JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
    }

    /**
     * Get ExpiresAt
     *
     * @return int|null
     */
    public function getExpiresAt(): ?int
    {
        return $this->expires_at;
    }

    /**
     * @param int $time
     *
     * @return JWTGeneratorContract
     */
    public function willExpireIn(int $time): JWTGeneratorContract
    {
        $this->expires_at = time() + $time;

        return $this;
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
     * @return JWK
     */
    public function getJWK(): JWK
    {
        $local_server_key = Base64Url::decode($this->getConfigVariable('public_key'));
        $public = $this->unserializePublicKey($local_server_key);

        return JWK::create([
            'kty' => 'EC',
            'crv' => 'P-256',
            'x' => Base64Url::encode($public['x']),
            'y' => Base64Url::encode($public['y']),
            'd' => $this->getConfigVariable('private_key'),
        ]);
    }

    /**
     * @param string $data
     *
     * @return array
     */
    private function unserializePublicKey(string $data): array
    {
        $data = bin2hex($data);
        $first_byte = mb_substr($data, 0, 2, '8bit');

        if ($first_byte !== '04') {
            throw new \InvalidArgumentException('Invalid data: only uncompressed keys are supported.');
        }

        $data = mb_substr($data, 2, null, '8bit');
        $center = mb_strlen($data) / 2;

        return [
            'x' => hex2bin(mb_substr($data, 0, $center, '8bit')),
            'y' => hex2bin(mb_substr($data, $center, null, '8bit')),
        ];
    }

    /**
     * @return array
     */
    public function getHeader(): array
    {
        return [
            'typ' => 'JWT',
            'alg' => 'ES256',
        ];
    }
}
