<?php

namespace AlexLisenkov\LaravelWebPush;

use AlexLisenkov\LaravelWebPush\Contracts\PushSubscriptionContract;
use AlexLisenkov\LaravelWebPush\Contracts\WebPushContract;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Support\Facades\App;
use Orchestra\Testbench\TestCase;
use AlexLisenkov\LaravelWebPush\LaravelWebPushServiceProvider;

class PushMessageTest extends TestCase
{
    /**
     * @var PushMessage
     */
    private $subject;

    public function testSendTo(): void
    {
        $subscription = $this->createMock(PushSubscriptionContract::class);
        $promise = $this->createMock(PromiseInterface::class);
        $web_push = $this->createMock(WebPushContract::class);

        App::shouldReceive('make')
           ->once()
           ->with(WebPushContract::class)
           ->andReturn($web_push);

        $web_push->expects($this->once())
                 ->method('sendMessage')
                 ->with($this->equalTo($this->subject), $this->equalTo($subscription))
                 ->willReturn($promise);

        $this->subject->sendTo($subscription);
    }

    public function testTitle(): void
    {
        $expected = 'Title';

        $this->subject->setTitle($expected);

        $this->assertSame($expected, $this->subject->getTitle());
    }

    public function testBody(): void
    {
        $expected = 'Body';

        $this->subject->setBody($expected);

        $this->assertSame($expected, $this->subject->getBody());
    }

    public function testIconPath(): void
    {
        $expected = 'https://example.com';

        $this->subject->setIconPath($expected);

        $this->assertSame($expected, $this->subject->getIconPath());
    }

    public function testUrgency(): void
    {
        $expected = 'urgent';

        $this->subject->setUrgency($expected);

        $this->assertSame($expected, $this->subject->getUrgency());
    }

    public function testTopic(): void
    {
        $expected = 'topic';

        $this->subject->setTopic($expected);

        $this->assertSame($expected, $this->subject->getTopic());
    }

    public function testTag(): void
    {
        $expected = 'tag';

        $this->subject->setTag($expected);

        $this->assertSame($expected, $this->subject->getTag());
    }

    public function testVibrationPattern(): void
    {
        $expected = [1, 3, 5];

        $this->subject->setVibrationPattern($expected);

        $this->assertSame($expected, $this->subject->getVibrationPattern());
    }

    public function testTimestamp(): void
    {
        $expected = 12345;

        $this->subject->setTimestamp($expected);

        $this->assertSame($expected, $this->subject->getTimestamp());
    }

    public function testLang(): void
    {
        $expected = 'nl';

        $this->subject->setLang($expected);

        $this->assertSame($expected, $this->subject->getLang());
    }

    public function testSilent(): void
    {
        $expected = false;

        $this->subject->setSilent($expected);

        $this->assertSame($expected, $this->subject->isSilent());
    }

    public function testToArray(): void
    {
        $expected = [
            'title' => 'title',
            'body' => 'body',
            'icon' => 'example',
            'urgency' => 'high',
            'topic' => 'topic',
            'tag' => 'test',
            'vibrate' => [1, 2, 3],
            'timestamp' => 12345,
            'lang' => 'nl',
        ];

        $this->subject->setTitle('title')
                      ->setBody('body')
                      ->setIconPath('example')
                      ->setUrgency('high')
                      ->setTopic('topic')
                      ->setTag('test')
                      ->setVibrationPattern([1, 2, 3])
                      ->setTimestamp(12345)
                      ->setLang('nl');

        $this->assertSame($expected, $this->subject->toArray());
    }

    public function testToJson(): void
    {
        $expected = json_encode([
            'title' => 'title',
            'body' => 'body',
            'icon' => 'example',
            'urgency' => 'high',
            'topic' => 'topic',
            'tag' => 'test',
            'vibrate' => [1, 2, 3],
            'timestamp' => 12345,
            'lang' => 'nl',
        ]);

        $this->subject->setTitle('title')
                      ->setBody('body')
                      ->setIconPath('example')
                      ->setUrgency('high')
                      ->setTopic('topic')
                      ->setTag('test')
                      ->setVibrationPattern([1, 2, 3])
                      ->setTimestamp(12345)
                      ->setLang('nl');

        $this->assertSame($expected, $this->subject->toJson());
    }

    public function testToString(): void
    {
        $expected = json_encode([
            'title' => 'title',
            'body' => 'body',
            'icon' => 'example',
            'urgency' => 'high',
            'topic' => 'topic',
            'tag' => 'test',
            'vibrate' => [1, 2, 3],
            'timestamp' => 12345,
            'lang' => 'nl',
        ]);

        $this->subject->setTitle('title')
                      ->setBody('body')
                      ->setIconPath('example')
                      ->setUrgency('high')
                      ->setTopic('topic')
                      ->setTag('test')
                      ->setVibrationPattern([1, 2, 3])
                      ->setTimestamp(12345)
                      ->setLang('nl');

        $this->assertSame($expected, $this->subject->__toString());
    }

    public function testJsonSerialize(): void
    {
        $expected = json_encode([
            'title' => 'title',
            'body' => 'body',
            'icon' => 'example',
            'urgency' => 'high',
            'topic' => 'topic',
            'tag' => 'test',
            'vibrate' => [1, 2, 3],
            'timestamp' => 12345,
            'lang' => 'nl',
        ]);

        $this->subject->setTitle('title')
                      ->setBody('body')
                      ->setIconPath('example')
                      ->setUrgency('high')
                      ->setTopic('topic')
                      ->setTag('test')
                      ->setVibrationPattern([1, 2, 3])
                      ->setTimestamp(12345)
                      ->setLang('nl');

        $this->assertSame($expected, $this->subject->jsonSerialize());
    }

    public function testThatIfMessageIsSilentNoVibrationPatternExists(): void
    {
        $this->subject->setVibrationPattern([1, 2, 3])
                      ->setSilent(true);

        $result = $this->subject->toArray();

        $this->assertArrayNotHasKey('vibrate', $result);
    }

    public function testThatIfMessageIsNotSilentVibrationPatternExists(): void
    {
        $this->subject->setVibrationPattern([1, 2, 3])
                      ->setSilent(false);

        $result = $this->subject->toArray();

        $this->assertArrayHasKey('vibrate', $result);
    }

    protected function getPackageProviders($app): array
    {
        return [LaravelWebPushServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new class extends PushMessage
        {
        };
    }

}
