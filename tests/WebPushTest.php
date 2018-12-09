<?php

namespace AlexLisenkov\LaravelWebPush;

use AlexLisenkov\LaravelWebPush\Contracts\JWTGeneratorContract;
use AlexLisenkov\LaravelWebPush\Contracts\P256EncryptedMessageBuilderContract;
use AlexLisenkov\LaravelWebPush\Contracts\P256EncryptedMessageContract;
use AlexLisenkov\LaravelWebPush\Contracts\PushMessageContract;
use AlexLisenkov\LaravelWebPush\Contracts\PushSubscriptionContract;
use AlexLisenkov\LaravelWebPush\Exceptions\InvalidPrivateKeyException;
use AlexLisenkov\LaravelWebPush\Exceptions\InvalidPublicKeyException;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Request;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class WebPushTest extends TestCase
{
    /**
     * @var MockObject|ConfigRepository
     */
    private $config_repository;
    /**
     * @var MockObject|P256EncryptedMessageBuilderContract
     */
    private $encrypted_message_builder;
    /**
     * @var MockObject|JWTGeneratorContract
     */
    private $JWT_generator;
    /**
     * @var MockObject|ClientInterface
     */
    private $client;
    /**
     * @var MockObject|PushMessageContract
     */
    private $message;
    /**
     * @var MockObject|PushSubscriptionContract
     */
    private $subscription;
    /**
     * @var MockObject|P256EncryptedMessageContract
     */
    private $encrypted_message;
    /**
     * @var WebPush
     */
    private $subject;

    public function testSendMessageBuildsEncryptedMessageFromSubscription()
    {
        $this->setGetConfigVariableMock();

        $p256 = 'BBp2ZSrnNp5GLBbBvu9kXPzKXgcSo8XyZXNLjBBuXky-IpzCZSSLyfhTKLPpo3UnlF6UBWgjzrg_cs3f6AqVTD4';
        $this->subscription->expects($this->exactly(2))
                           ->method('getP256dh')
                           ->willReturn($p256);

        $auth = 'auth';
        $this->subscription->expects($this->once())
                           ->method('getAuth')
                           ->willReturn($auth);
        $message = 'test';
        $this->message->method('toJson')
                      ->willReturn($message);

        $this->encrypted_message_builder
            ->expects($this->once())
            ->method('withPublicKey')
            ->with($this->equalTo($p256))
            ->willReturnSelf();

        $this->encrypted_message_builder
            ->expects($this->once())
            ->method('withAuthToken')
            ->with($this->equalTo($auth))
            ->willReturnSelf();

        $this->encrypted_message_builder
            ->expects($this->once())
            ->method('build')
            ->with($this->equalTo($this->message->toJson()))
            ->willReturn($this->createMock(P256EncryptedMessageContract::class));

        $this->client->method('sendAsync')
                     ->willReturn($this->createMock(PromiseInterface::class));

        $this->subject->sendMessage($this->message, $this->subscription);
    }

    private function setGetConfigVariableMock(array $with = [])
    {
        $this->config_repository
            ->method('get')
            ->will($this->returnValueMap(array_merge([
                [Constants::CONFIG_KEY . '.' . 'private_key', null, 'RofDYZ1GFp-oPDdvXVKN29yl1xKfJUPjTWYKQkcUuJU'],
                [
                    Constants::CONFIG_KEY . '.' . 'public_key',
                    null,
                    'BBp2ZSrnNp5GLBbBvu9kXPzKXgcSo8XyZXNLjBBuXky-IpzCZSSLyfhTKLPpo3UnlF6UBWgjzrg_cs3f6AqVTD4',
                ],
            ], $with)));
    }

    public function testSendMessageJWTGeneratorWillUseAudienceFromSubscription()
    {
        $this->setGetConfigVariableMock();
        $this->setEncryptedMessageBuilderMocks();

        $audience = 'https://example.net';
        $this->subscription->expects($this->once())
                           ->method('getAudience')
                           ->willReturn($audience);

        $this->JWT_generator
            ->expects($this->once())
            ->method('withAudience')
            ->with($this->equalTo($audience))
            ->willReturnSelf();

        $this->JWT_generator
            ->expects($this->once())
            ->method('serialize')
            ->willReturn('serialized string');

        $this->client->method('sendAsync')
                     ->willReturn($this->createMock(PromiseInterface::class));

        $this->subject->sendMessage($this->message, $this->subscription);
    }

    private function setEncryptedMessageBuilderMocks()
    {
        $this->setGetConfigVariableMock();
        $p256 = 'BBp2ZSrnNp5GLBbBvu9kXPzKXgcSo8XyZXNLjBBuXky-IpzCZSSLyfhTKLPpo3UnlF6UBWgjzrg_cs3f6AqVTD4';
        $this->subscription
            ->method('getP256dh')
            ->willReturn($p256);

        $auth = 'auth';
        $this->subscription
            ->method('getAuth')
            ->willReturn($auth);

        $message = 'test';
        $this->message
            ->method('toJson')
            ->willReturn($message);

        $this->encrypted_message_builder
            ->method('withPublicKey')
            ->willReturnSelf();

        $this->encrypted_message_builder
            ->method('withAuthToken')
            ->willReturnSelf();

        $this->encrypted_message_builder
            ->method('build')
            ->willReturn($this->encrypted_message);
    }

    public function testThatRequestMethodIsPost()
    {
        $this->setGetConfigVariableMock();
        $this->setEncryptedMessageBuilderMocks();
        $this->setJWTGeneratorMocks();

        $this->client
            ->method('sendAsync')
            ->with($this->callback(function (Request $request) {
                $this->assertEquals('POST', $request->getMethod());

                return true;
            }))
            ->willReturn($this->createMock(PromiseInterface::class));

        $this->subject->sendMessage($this->message, $this->subscription);
    }

    private function setJWTGeneratorMocks()
    {
        $audience = 'https://example.net';
        $this->subscription
            ->method('getAudience')
            ->willReturn($audience);

        $this->JWT_generator
            ->method('withAudience')
            ->willReturnSelf();

        $this->JWT_generator
            ->method('serialize')
            ->willReturn('serialized string');
    }

    public function testThatRequestUriIsSubscriptionEndpoint()
    {
        $endpoint = 'https://example.net/endpoint';

        $this->setGetConfigVariableMock();
        $this->setEncryptedMessageBuilderMocks();
        $this->setJWTGeneratorMocks();

        $this->subscription
            ->expects($this->once())
            ->method('getEndpoint')
            ->willReturn($endpoint);

        $this->client
            ->method('sendAsync')
            ->with($this->callback(function (Request $request) use ($endpoint) {
                $this->assertEquals($endpoint, $request->getUri());

                return true;
            }))
            ->willReturn($this->createMock(PromiseInterface::class));

        $this->subject->sendMessage($this->message, $this->subscription);
    }

    public function testThatRequestBodyIsMessageCypher()
    {
        $cypher = '1234459696959592373737';

        $this->setGetConfigVariableMock();
        $this->setEncryptedMessageBuilderMocks();
        $this->setJWTGeneratorMocks();

        $this->encrypted_message
            ->expects($this->once())
            ->method('getCypher')
            ->willReturn($cypher);

        $this->client
            ->method('sendAsync')
            ->with($this->callback(function (Request $request) use ($cypher) {
                $this->assertEquals($cypher, $request->getBody());

                return true;
            }))
            ->willReturn($this->createMock(PromiseInterface::class));

        $this->subject->sendMessage($this->message, $this->subscription);
    }

    public function testThatRequestContentTypeHeaderIsApplicationOctetStream()
    {
        $this->setGetConfigVariableMock();
        $this->setEncryptedMessageBuilderMocks();
        $this->setJWTGeneratorMocks();

        $this->client
            ->method('sendAsync')
            ->with($this->callback(function (Request $request) {
                $this->assertEquals('application/octet-stream', $request->getHeaderLine('Content-Type'));

                return true;
            }))
            ->willReturn($this->createMock(PromiseInterface::class));

        $this->subject->sendMessage($this->message, $this->subscription);
    }

    public function testThatRequestContentEncodingHeaderIsAesgcm()
    {
        $this->setGetConfigVariableMock();
        $this->setEncryptedMessageBuilderMocks();
        $this->setJWTGeneratorMocks();

        $this->client
            ->method('sendAsync')
            ->with($this->callback(function (Request $request) {
                $this->assertEquals('aesgcm', $request->getHeaderLine('Content-Encoding'));

                return true;
            }))
            ->willReturn($this->createMock(PromiseInterface::class));

        $this->subject->sendMessage($this->message, $this->subscription);
    }

    public function testThatRequestAuthorizationHeaderIsWebpushWithJWT()
    {
        $this->setGetConfigVariableMock();
        $this->setEncryptedMessageBuilderMocks();
        $this->setJWTGeneratorMocks();

        $this->client
            ->method('sendAsync')
            ->with($this->callback(function (Request $request) {
                $this->assertEquals('WebPush serialized string', $request->getHeaderLine('Authorization'));

                return true;
            }))
            ->willReturn($this->createMock(PromiseInterface::class));

        $this->subject->sendMessage($this->message, $this->subscription);
    }

    public function testThatRequestEncryptionHeaderIsEncodedSalt()
    {
        $expected = 'Salted potato chips';

        $this->setGetConfigVariableMock();
        $this->setEncryptedMessageBuilderMocks();
        $this->setJWTGeneratorMocks();

        $this->encrypted_message
            ->expects($this->once())
            ->method('getEncodedSalt')
            ->willReturn($expected);

        $this->client
            ->method('sendAsync')
            ->with($this->callback(function (Request $request) use ($expected) {
                $this->assertEquals('salt=' . $expected, $request->getHeaderLine('Encryption'));

                return true;
            }))
            ->willReturn($this->createMock(PromiseInterface::class));

        $this->subject->sendMessage($this->message, $this->subscription);
    }

    public function testThatRequestCryptoKeyHeaderIsEncodedPublicKeyAndPublicKey()
    {
        $expected_message_pub_key = 'BBp2ZSrnNp5GLBbBvu9kXPzKXgcSo8XyZXNLjBBuXky-IpzCZSSLyfhTKLPpo3UnlF6UBWgjzrg_cs3f6AqVTD4';
        $expected_config_pub_key = 'BBp2ZSrnNp5GLBbBvu9kXPzKXgcSo8XyZXNLjBBuXky-IpzCZSSLyfhTKLPpo3UnlF6UBWgjzrg_cs3f6AqVTD4';

        $this->setGetConfigVariableMock();
        $this->setEncryptedMessageBuilderMocks();
        $this->setJWTGeneratorMocks();

        $this->encrypted_message
            ->expects($this->once())
            ->method('getEncodedPublicKey')
            ->willReturn($expected_message_pub_key);

        $this->config_repository
            ->method('get')
            ->willReturn($expected_config_pub_key);

        $this->client
            ->method('sendAsync')
            ->with($this->callback(function (Request $request) use (
                $expected_message_pub_key,
                $expected_config_pub_key
            ) {
                $this->assertEquals('dh=' . $expected_message_pub_key . ';p256ecdsa=' . $expected_config_pub_key,
                    $request->getHeaderLine('Crypto-Key'));

                return true;
            }))
            ->willReturn($this->createMock(PromiseInterface::class));

        $this->subject->sendMessage($this->message, $this->subscription);
    }

    public function testThatRequestContentLengthHeaderIsCypherLength()
    {
        $expected = 12345;

        $this->setGetConfigVariableMock();
        $this->setEncryptedMessageBuilderMocks();
        $this->setJWTGeneratorMocks();

        $this->encrypted_message
            ->expects($this->once())
            ->method('getCypherLength')
            ->willReturn($expected);

        $this->client
            ->method('sendAsync')
            ->with($this->callback(function (Request $request) use ($expected) {
                $this->assertEquals('dh=' . $expected, $request->getHeaderLine('Content-Length'));

                return true;
            }))
            ->willReturn($this->createMock(PromiseInterface::class));

        $this->subject->sendMessage($this->message, $this->subscription);
    }

    public function testThatRequestTTLHeaderIsConfiguredTTL()
    {
        $expected = 50000;

        $this->setGetConfigVariableMock([
            [Constants::CONFIG_KEY . '.TTL', Constants::DEFAULT_TTL, $expected],
        ]);
        $this->setEncryptedMessageBuilderMocks();
        $this->setJWTGeneratorMocks();

        $this->client
            ->method('sendAsync')
            ->with($this->callback(function (Request $request) use ($expected) {
                $this->assertEquals($expected, $request->getHeaderLine('TTL'));

                return true;
            }))
            ->willReturn($this->createMock(PromiseInterface::class));

        $this->subject->sendMessage($this->message, $this->subscription);
    }

    public function testThatRequestTopicHeaderIsMessageTopic()
    {
        $expected = 'topic';

        $this->setGetConfigVariableMock();
        $this->setEncryptedMessageBuilderMocks();
        $this->setJWTGeneratorMocks();

        $this->message
            ->expects($this->once())
            ->method('getTopic')
            ->willReturn($expected);

        $this->client
            ->method('sendAsync')
            ->with($this->callback(function (Request $request) use ($expected) {
                $this->assertEquals($expected, $request->getHeaderLine('Topic'));

                return true;
            }))
            ->willReturn($this->createMock(PromiseInterface::class));

        $this->subject->sendMessage($this->message, $this->subscription);
    }

    public function testThatRequestUrgencyHeaderIsMessageUrgency()
    {
        $expected = 'high';

        $this->setGetConfigVariableMock();
        $this->setEncryptedMessageBuilderMocks();
        $this->setJWTGeneratorMocks();

        $this->message
            ->expects($this->once())
            ->method('getUrgency')
            ->willReturn($expected);

        $this->client
            ->method('sendAsync')
            ->with($this->callback(function (Request $request) use ($expected) {
                $this->assertEquals($expected, $request->getHeaderLine('Urgency'));

                return true;
            }))
            ->willReturn($this->createMock(PromiseInterface::class));

        $this->subject->sendMessage($this->message, $this->subscription);
    }

    public function testThatIncorrectPrivateKeyWillThrowException()
    {
        $this->config_repository
            ->method('get')
            ->will($this->returnValueMap(array_merge([
                [Constants::CONFIG_KEY . '.' . 'private_key', null, 'incorrect'],
                [
                    Constants::CONFIG_KEY . '.' . 'public_key',
                    null,
                    'BBp2ZSrnNp5GLBbBvu9kXPzKXgcSo8XyZXNLjBBuXky-IpzCZSSLyfhTKLPpo3UnlF6UBWgjzrg_cs3f6AqVTD4',
                ],
            ])));

        $this->expectException(InvalidPrivateKeyException::class);

        $this->subject->sendMessage($this->message, $this->subscription);
    }

    public function testThatIncorrectPublicKeyWillThrowException()
    {
        $this->config_repository
            ->method('get')
            ->will($this->returnValueMap(array_merge([
                [Constants::CONFIG_KEY . '.' . 'private_key', null, 'RofDYZ1GFp-oPDdvXVKN29yl1xKfJUPjTWYKQkcUuJU'],
                [Constants::CONFIG_KEY . '.' . 'public_key', null, 'incorrect'],
            ])));

        $this->expectException(InvalidPublicKeyException::class);

        $this->subject->sendMessage($this->message, $this->subscription);
    }

    protected function getPackageProviders($app)
    {
        return ['AlexLisenkov\LaravelWebPush\LaravelWebPushServiceProvider'];
    }

    protected function setUp()
    {
        parent::setUp();

        $this->message = $this->createMock(PushMessageContract::class);
        $this->subscription = $this->createMock(PushSubscriptionContract::class);

        $this->config_repository = $this->createMock(ConfigRepository::class);
        $this->encrypted_message_builder = $this->createMock(P256EncryptedMessageBuilderContract::class);
        $this->JWT_generator = $this->createMock(JWTGeneratorContract::class);
        $this->client = $this->createMock(Client::class);

        $this->encrypted_message = $this->createMock(P256EncryptedMessageContract::class);

        $this->subject = new WebPush($this->config_repository, $this->encrypted_message_builder, $this->JWT_generator,
            $this->client);
    }
}
