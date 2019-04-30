<?php

namespace AlexLisenkov\LaravelWebPush {

    // Override time
    function time()
    {
        return 2000;
    }
}

namespace AlexLisenkov\LaravelWebPush\Tests {

    use AlexLisenkov\LaravelWebPush\JWTGenerator;
    use Illuminate\Contracts\Config\Repository as ConfigRepository;
    use Orchestra\Testbench\TestCase;
    use PHPUnit\Framework\MockObject\MockObject;
    use AlexLisenkov\LaravelWebPush\LaravelWebPushServiceProvider;

    class JWTGeneratorTest extends TestCase
    {
        /** @var MockObject|ConfigRepository */
        private $config_repository;
        /** @var JWTGenerator */
        private $subject;

        public function testGetHeaderIsJWTES256Header(): void
        {
            $expected = [
                'typ' => 'JWT',
                'alg' => 'ES256',
            ];

            $actual = $this->subject->getHeader();

            $this->assertSame($expected, $actual);
        }

        public function testThatGetPayloadWillContainSetAudience(): void
        {
            $expected = 'Audience';

            $this->subject->withAudience($expected);
            $this->subject->willExpireIn(0);

            $result = json_decode($this->subject->getPayload(), false);

            $this->assertSame($expected, $result->aud);
        }

        public function testThatGetPayloadWillContainSetExpire(): void
        {
            $expected = 3000;

            $this->subject->withAudience('Audience');
            $this->subject->willExpireIn(1000);

            $result = json_decode($this->subject->getPayload(), false);

            $this->assertSame($expected, $result->exp);
        }

        public function testThatGetPayloadWillUseExpireTimeFromConfigWhenNotSet(): void
        {
            $expected = 4000;

            $this->config_repository->method('get')
                                    ->willReturn(2000);

            $this->subject->withAudience('Audience');

            $result = json_decode($this->subject->getPayload(), false);

            $this->assertSame($expected, $result->exp);
        }

        public function testThatGetPayloadWillUseSubFromConfig(): void
        {
            $expected = 'mailto:alex@create.nl';

            $this->config_repository->method('get')
                                    ->willReturn($expected);

            $this->subject->withAudience('Audience');
            $this->subject->willExpireIn(10);

            $result = json_decode($this->subject->getPayload(), false);

            $this->assertSame($expected, $result->sub);
        }

        public function testThatGetJwkKtyEqualsEC(): void
        {
            $this->config_repository
                ->method('get')
                ->willReturnOnConsecutiveCalls(
                    'BBp2ZSrnNp5GLBbBvu9kXPzKXgcSo8XyZXNLjBBuXky-IpzCZSSLyfhTKLPpo3UnlF6UBWgjzrg_cs3f6AqVTD4',
                    'WnThrKeXC_D00FRSITmGAvXGNHlIm3n4zqsvIHIOGMw'
                );

            $this->assertSame('EC', $this->subject->getJWK()->get('kty'));
        }

        public function testThatGetJwkCrvEqualsP256(): void
        {
            $this->config_repository
                ->method('get')
                ->willReturnOnConsecutiveCalls(
                    'BBp2ZSrnNp5GLBbBvu9kXPzKXgcSo8XyZXNLjBBuXky-IpzCZSSLyfhTKLPpo3UnlF6UBWgjzrg_cs3f6AqVTD4',
                    'WnThrKeXC_D00FRSITmGAvXGNHlIm3n4zqsvIHIOGMw'
                );

            $this->assertSame('P-256', $this->subject->getJWK()->get('crv'));
        }

        public function testThatGetJwkXWillBeFirstPartOfPublicKey(): void
        {
            $expected = 'GnZlKuc2nkYsFsG-72Rc_MpeBxKjxfJlc0uMEG5eTL4';

            $this->config_repository
                ->method('get')
                ->willReturnOnConsecutiveCalls(
                    'BBp2ZSrnNp5GLBbBvu9kXPzKXgcSo8XyZXNLjBBuXky-IpzCZSSLyfhTKLPpo3UnlF6UBWgjzrg_cs3f6AqVTD4',
                    'WnThrKeXC_D00FRSITmGAvXGNHlIm3n4zqsvIHIOGMw'
                );

            $this->assertSame($expected, $this->subject->getJWK()->get('x'));
        }

        public function testThatGetJwkYWillBeFirstPartOfPublicKey(): void
        {
            $expected = 'IpzCZSSLyfhTKLPpo3UnlF6UBWgjzrg_cs3f6AqVTD4';

            $this->config_repository
                ->method('get')
                ->willReturnOnConsecutiveCalls(
                    'BBp2ZSrnNp5GLBbBvu9kXPzKXgcSo8XyZXNLjBBuXky-IpzCZSSLyfhTKLPpo3UnlF6UBWgjzrg_cs3f6AqVTD4',
                    'WnThrKeXC_D00FRSITmGAvXGNHlIm3n4zqsvIHIOGMw'
                );

            $this->assertSame($expected, $this->subject->getJWK()->get('y'));
        }

        public function testThatGetJwkDWillBeFirstPartOfPublicKey(): void
        {
            $expected = 'WnThrKeXC_D00FRSITmGAvXGNHlIm3n4zqsvIHIOGMw';

            $this->config_repository
                ->method('get')
                ->willReturnOnConsecutiveCalls(
                    'BBp2ZSrnNp5GLBbBvu9kXPzKXgcSo8XyZXNLjBBuXky-IpzCZSSLyfhTKLPpo3UnlF6UBWgjzrg_cs3f6AqVTD4',
                    'WnThrKeXC_D00FRSITmGAvXGNHlIm3n4zqsvIHIOGMw'
                );

            $this->assertSame($expected, $this->subject->getJWK()->get('d'));
        }

        public function testGetJWSHasCorrectPayload(): void
        {
            $this->subject->withAudience('test');
            $this->subject->willExpireIn(12);

            $this->config_repository
                ->method('get')
                ->willReturnOnConsecutiveCalls(
                    'subject',
                    'BBp2ZSrnNp5GLBbBvu9kXPzKXgcSo8XyZXNLjBBuXky-IpzCZSSLyfhTKLPpo3UnlF6UBWgjzrg_cs3f6AqVTD4',
                    'WnThrKeXC_D00FRSITmGAvXGNHlIm3n4zqsvIHIOGMw'
                );

            $this->assertSame('{"aud":"test","exp":2012,"sub":"subject"}', $this->subject->getJWS()->getPayload());
        }

        public function testGetJWSHasOnlyOneSignature(): void
        {
            $this->subject->withAudience('test');
            $this->subject->willExpireIn(12);

            $this->config_repository
                ->method('get')
                ->willReturnOnConsecutiveCalls(
                    'subject',
                    'BBp2ZSrnNp5GLBbBvu9kXPzKXgcSo8XyZXNLjBBuXky-IpzCZSSLyfhTKLPpo3UnlF6UBWgjzrg_cs3f6AqVTD4',
                    'WnThrKeXC_D00FRSITmGAvXGNHlIm3n4zqsvIHIOGMw'
                );

            $this->assertSame(1, $this->subject->getJWS()->countSignatures());
        }

        public function testThatGetPayloadWithoutAudienceSetWillThrowException(): void
        {
            $this->expectException(\Exception::class);

            $this->subject->getPayload();
        }

        public function testThatGetJwkWithInvalidPublicKeyWillThrowException(): void
        {
            $this->expectException(\InvalidArgumentException::class);

            $this->config_repository
                ->method('get')
                ->willReturnOnConsecutiveCalls(
                    'invalid data'
                );

            $this->subject->getJWK();
        }

        public function testWillExpireAt(): void
        {
            $this->subject->willExpireAt(12345);

            $this->assertEquals(12345, $this->subject->getExpiresAt());
        }

        public function testSerialize(): void
        {
            $this->subject->withAudience('test');
            $this->subject->willExpireIn(12);

            $this->config_repository
                ->method('get')
                ->willReturnOnConsecutiveCalls(
                    'subject',
                    'BBp2ZSrnNp5GLBbBvu9kXPzKXgcSo8XyZXNLjBBuXky-IpzCZSSLyfhTKLPpo3UnlF6UBWgjzrg_cs3f6AqVTD4',
                    'WnThrKeXC_D00FRSITmGAvXGNHlIm3n4zqsvIHIOGMw'
                );

            $res = $this->subject->serialize();

            $this->assertEquals(179, strlen($res));
        }

        protected function getPackageProviders($app): array
        {
            return [LaravelWebPushServiceProvider::class];
        }

        protected function setUp(): void
        {
            parent::setUp();

            $this->config_repository = $this->createMock(ConfigRepository::class);
            $this->subject = new JWTGenerator($this->config_repository);
        }
    }
}
