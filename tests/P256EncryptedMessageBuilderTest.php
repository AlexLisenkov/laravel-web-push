<?php

namespace AlexLisenkov\LaravelWebPush;

use AlexLisenkov\LaravelWebPush\Contracts\P256EncryptedMessageContract;
use Base64Url\Base64Url;
use Elliptic\EC;
use Illuminate\Contracts\Container\Container;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class P256EncryptedMessageBuilderTest extends TestCase
{
    /**
     * @var MockObject|EC
     */
    private $ec;
    /**
     * @var MockObject|Container
     */
    private $container;
    /**
     * @var P256EncryptedMessageBuilder
     */
    private $subject;

    public function testThatWithPublicKeyWillReturnSelf(): void
    {
        $actual = $this->subject->withPublicKey('');

        $this->assertInstanceOf(P256EncryptedMessageBuilder::class, $actual);
    }

    public function testThatWithAuthTokenWillReturnSelf(): void
    {
        $actual = $this->subject->withAuthToken('');

        $this->assertInstanceOf(P256EncryptedMessageBuilder::class, $actual);
    }

    public function testThatBuildWillBuildP256EncryptedMessageContract(): void
    {
        $this->setAuthTokenAndPublicKey();
        $this->ec->method('keyFromPublic')->willReturn(new EC\KeyPair('', ''));
        $this->ec->method('genKeyPair')->willReturn(new EC\KeyPair('', ''));

        $this->container
            ->expects($this->once())
            ->method('make')
            ->with($this->equalTo(P256EncryptedMessageContract::class), $this->anything())
            ->willReturn($this->createMock(P256EncryptedMessageContract::class));

        $this->subject->build('anything');
    }

    private function setAuthTokenAndPublicKey(): void
    {
        $this->subject->withAuthToken('auth');
        $this->subject->withPublicKey('BBp2ZSrnNp5GLBbBvu9kXPzKXgcSo8XyZXNLjBBuXky-IpzCZSSLyfhTKLPpo3UnlF6UBWgjzrg_cs3f6AqVTD4');
    }

    public function testThatPrivateParameterIsGeneratedKeyPair(): void
    {
        $expected = new EC\KeyPair('', '');

        $this->setAuthTokenAndPublicKey();

        $this->ec->method('keyFromPublic')->willReturn(new EC\KeyPair('', ''));
        $this->ec->method('genKeyPair')->willReturn($expected);

        $this->container
            ->method('make')
            ->with($this->anything(), $this->callback(function ($param) use ($expected) {
                $this->assertSame($expected, $param['private']);

                return true;
            }))
            ->willReturn($this->createMock(P256EncryptedMessageContract::class));

        $this->subject->build('anything');
    }

    public function testThatSubscriberParameterIsGeneratedKeyPair(): void
    {
        $expected = new EC\KeyPair('', '');

        $this->setAuthTokenAndPublicKey();

        $this->ec->method('keyFromPublic')->willReturn($expected);
        $this->ec->method('genKeyPair')->willReturn(new EC\KeyPair('', ''));

        $this->container
            ->method('make')
            ->with($this->anything(), $this->callback(function ($param) use ($expected) {
                $this->assertSame($expected, $param['subscriber']);

                return true;
            }))
            ->willReturn($this->createMock(P256EncryptedMessageContract::class));

        $this->subject->build('anything');
    }

    public function testThatAuthTokenIsDecodedAuthToken(): void
    {
        $expected = Base64Url::decode('auth');

        $this->setAuthTokenAndPublicKey();

        $this->ec->method('keyFromPublic')->willReturn(new EC\KeyPair('', ''));
        $this->ec->method('genKeyPair')->willReturn(new EC\KeyPair('', ''));

        $this->container
            ->method('make')
            ->with($this->anything(), $this->callback(function ($param) use ($expected) {
                $this->assertSame($expected, $param['auth_token']);

                return true;
            }))
            ->willReturn($this->createMock(P256EncryptedMessageContract::class));

        $this->subject->build('anything');
    }

    public function testThatSaltSizeIsExactlyToTheDefinedConstant(): void
    {
        $expected = Constants::SALT_BYTE_LENGTH;

        $this->setAuthTokenAndPublicKey();

        $this->ec->method('keyFromPublic')->willReturn(new EC\KeyPair('', ''));
        $this->ec->method('genKeyPair')->willReturn(new EC\KeyPair('', ''));

        $this->container
            ->method('make')
            ->with($this->anything(), $this->callback(function ($param) use ($expected) {
                $this->assertEquals($expected, mb_strlen($param['salt'], '8bit'));

                return true;
            }))
            ->willReturn($this->createMock(P256EncryptedMessageContract::class));

        $this->subject->build('anything');
    }

    public function testThatPayloadIsSameAsGiven(): void
    {
        $expected = '{payload: true}';

        $this->setAuthTokenAndPublicKey();

        $this->ec->method('keyFromPublic')->willReturn(new EC\KeyPair('', ''));
        $this->ec->method('genKeyPair')->willReturn(new EC\KeyPair('', ''));

        $this->container
            ->method('make')
            ->with($this->anything(), $this->callback(function ($param) use ($expected) {
                $this->assertSame($expected, $param['payload']);

                return true;
            }))
            ->willReturn($this->createMock(P256EncryptedMessageContract::class));

        $this->subject->build($expected);
    }

    public function testThatPublicECIsGeneratedFromGivenPublicKey(): void
    {
        $expected = bin2hex(Base64Url::decode('BBp2ZSrnNp5GLBbBvu9kXPzKXgcSo8XyZXNLjBBuXky-IpzCZSSLyfhTKLPpo3UnlF6UBWgjzrg_cs3f6AqVTD4'));

        $this->setAuthTokenAndPublicKey();

        $this->ec->expects($this->once())
                 ->method('keyFromPublic')
                    ->with($this->equalTo($expected), $this->equalTo('hex'))
                 ->willReturn(new EC\KeyPair('', ''));
        $this->ec->method('genKeyPair')
                 ->willReturn(new EC\KeyPair('', ''));

        $this->container
            ->method('make')
            ->with($this->equalTo(P256EncryptedMessageContract::class), $this->anything())
            ->willReturn($this->createMock(P256EncryptedMessageContract::class));

        $this->subject->build('anything');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->ec = $this->createMock(EC::class);
        $this->container = $this->createMock(Container::class);
        $this->subject = new P256EncryptedMessageBuilder($this->ec, $this->container);
    }

    protected function getPackageProviders($app): array
    {
        return [LaravelWebPushServiceProvider::class];
    }
}
