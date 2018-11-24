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
    private $expires_as;
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
     * @return array
     */
    public function getHeader(): array
    {
        return [
            'typ' => 'JWT',
            'alg' => 'ES256',
        ];
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
        $this->expires_as = $time;

        return $this;
    }

    /**
     * @param int $time
     *
     * @return JWTGeneratorContract
     */
    public function willExpireIn(int $time): JWTGeneratorContract
    {
        $this->expires_as = time()+$time;

        return $this;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getPayload(): string
    {
        if( !$this->audience ){
            throw new \Exception('No audience set');
        }

        if( !$this->expires_as ){
            $this->willExpireIn($this->getConfigVariable('expiration', Constants::DEFAULT_EXPIRE));
        }

        return json_encode([
            'aud' => $this->audience,
            'exp' => $this->expires_as,
            'sub' => $this->getConfigVariable('subject', env('APP_URL', 'mailto:name@example.com'))
        ], JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
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
            'x' => Base64Url::encode(hex2bin($public['x'])),
            'y' => Base64Url::encode(hex2bin($public['y'])),
            'd' => $this->getConfigVariable('private_key'),
        ]);
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
     * @throws \Exception
     */
    public function serialize(): string
    {
        $jsonConverter = new StandardConverter();
        $jwsCompactSerializer = new CompactSerializer($jsonConverter);

        return $jwsCompactSerializer->serialize($this->getJWS(), 0);
    }

    /**
     * @param string $data
     *
     * @return array
     */
    private function unserializePublicKey(string $data): array
    {
        $data = bin2hex($data);
        if (mb_substr($data, 0, 2, '8bit') !== '04') {
            throw new \InvalidArgumentException('Invalid data: only uncompressed keys are supported.');
        }
        $data = mb_substr($data, 2, null, '8bit');
        $dataLength = mb_strlen($data, '8bit');

        return [
            'x' => mb_substr($data, 0, $dataLength / 2, '8bit'),
            'y' => mb_substr($data, $dataLength / 2, null, '8bit'),
        ];
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
        return $this->config_repository->get(Constants::CONFIG_KEY.'.'.$key, $default);
    }
}
