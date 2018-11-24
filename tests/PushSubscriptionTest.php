<?php

namespace AlexLisenkov\LaravelWebPush;

use AlexLisenkov\LaravelWebPush\Contracts\PushMessageContract;
use AlexLisenkov\LaravelWebPush\Contracts\WebPushContract;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Support\Facades\App;
use Orchestra\Testbench\TestCase;

class PushSubscriptionTest extends TestCase
{
    private $endpoint = 'https://example.com/somejibberish';
    private $p256dh = '12345678';
    private $auth = 'auth';
    /**
     * @var PushSubscription
     */
    private $subject;

    public function testIfGetEndpointIsInitialEndpoint()
    {
        $actual = $this->subject->getEndpoint();

        $this->assertSame($this->endpoint, $actual);
    }

    public function testIfGetP256dhIsInitialP256dh()
    {
        $actual = $this->subject->getP256dh();

        $this->assertSame($this->p256dh, $actual);
    }

    public function testIfGetAuthIsInitialAuth()
    {
        $actual = $this->subject->getAuth();

        $this->assertSame($this->auth, $actual);
    }

    public function testIfSetEndpointWillUpdateEndpoint()
    {
        $expected = 'new endpoint';
        $this->subject->setEndpoint($expected);
        $actual = $this->subject->getEndpoint();

        $this->assertSame($expected, $actual);
    }

    public function testIfSetP256dhWillUpdateP256dh()
    {
        $expected = 'new p256';
        $this->subject->setP256dh($expected);
        $actual = $this->subject->getP256dh();

        $this->assertSame($expected, $actual);
    }

    public function testIfSetAuthWillUpdateAuth()
    {
        $expected = 'new auth';
        $this->subject->setAuth($expected);
        $actual = $this->subject->getAuth();

        $this->assertSame($expected, $actual);
    }

    public function testGetAudience()
    {
        $expected = 'https://example.com';
        $actual = $this->subject->getAudience();

        $this->assertSame($expected, $actual);
    }

    public function testSendMessage()
    {
        $message = $this->createMock(PushMessageContract::class);
        $promise = $this->createMock(PromiseInterface::class);
        $web_push = $this->createMock(WebPushContract::class);

        App::shouldReceive('make')
           ->once()
           ->with(WebPushContract::class)
           ->andReturn($web_push);

        $web_push->expects($this->once())
                 ->method('sendMessage')
                 ->with($this->equalTo($message), $this->equalTo($this->subject))
                 ->willReturn($promise);

        $this->subject->sendMessage($message);
    }

    protected function getPackageProviders($app)
    {
        return ['AlexLisenkov\LaravelWebPush\LaravelWebPushServiceProvider'];
    }

    protected function setUp()
    {
        parent::setUp();

        $this->subject = new PushSubscription($this->endpoint, $this->p256dh, $this->auth);
    }
}
