<?php

namespace AlexLisenkov\LaravelWebPush;

use AlexLisenkov\LaravelWebPush\Contracts\PushSubscriptionContract;
use AlexLisenkov\LaravelWebPush\Contracts\WebPushContract;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Support\Facades\App;
use Orchestra\Testbench\TestCase;

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

    public function testBadge(): void
    {
        $expected = 'badge';

        $this->subject->setBadge($expected);

        $this->assertSame($expected, $this->subject->getBadge());
    }

    public function testData(): void
    {
        $expected = [1, 2, 3];

        $this->subject->setData($expected);

        $this->assertSame($expected, $this->subject->getData());
    }

    public function testDirDefaultsToAuto(): void
    {
        $this->assertSame('auto', $this->subject->getDir());
    }

    public function testDirAuto(): void
    {
        $this->subject->setDir('auto');

        $this->assertSame('auto', $this->subject->getDir());
    }

    public function testDirLtr(): void
    {
        $this->subject->setDir('ltr');

        $this->assertSame('ltr', $this->subject->getDir());
    }

    public function testDirRtl(): void
    {
        $this->subject->setDir('rtl');

        $this->assertSame('rtl', $this->subject->getDir());
    }

    public function testDirWithInvalidValueWillThrowException()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->subject->setDir('invalid');
    }

    public function testImage(): void
    {
        $expected = 'https://example.com';

        $this->subject->setImage($expected);

        $this->assertSame($expected, $this->subject->getImage());
    }

    public function testRenotifyDefaultsToFalse(): void
    {
        $this->assertFalse($this->subject->getRenotify());
    }

    public function testRenotify(): void
    {
        $expected = true;

        $this->subject->setRenotify($expected);

        $this->assertSame($expected, $this->subject->getRenotify());
    }

    public function testRequireInteractionDefaultsToFalse(): void
    {
        $this->assertFalse($this->subject->getRequireInteraction());
    }

    public function testRequireInteraction(): void
    {
        $expected = true;

        $this->subject->setRequireInteraction($expected);

        $this->assertSame($expected, $this->subject->getRequireInteraction());
    }

    public function testIconPath(): void
    {
        $expected = 'https://example.com';

        $this->subject->setIcon($expected);

        $this->assertSame($expected, $this->subject->getIcon());
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

        $this->subject->setVibrate($expected);

        $this->assertSame($expected, $this->subject->getVibrate());
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
            'options' => [
                'body' => 'body',
                'dir' => 'auto',
                'icon' => 'example',
                'lang' => 'nl',
                'tag' => 'test',
                'timestamp' => 12345,
                'vibrate' => [1, 2, 3],
            ],
        ];

        $this->subject->setTitle('title')
                      ->setBody('body')
                      ->setIcon('example')
                      ->setUrgency('high')
                      ->setTopic('topic')
                      ->setTag('test')
                      ->setVibrate([1, 2, 3])
                      ->setTimestamp(12345)
                      ->setLang('nl');

        $this->assertSame($expected, $this->subject->toArray());
    }

    public function testToJson(): void
    {
        $expected = json_encode([
            'title' => 'title',
            'options' => [
                'body' => 'body',
                'dir' => 'auto',
                'icon' => 'example',
                'lang' => 'nl',
                'tag' => 'test',
                'timestamp' => 12345,
                'vibrate' => [1, 2, 3],
            ],
        ]);

        $this->subject->setTitle('title')
                      ->setBody('body')
                      ->setIcon('example')
                      ->setUrgency('high')
                      ->setTopic('topic')
                      ->setTag('test')
                      ->setVibrate([1, 2, 3])
                      ->setTimestamp(12345)
                      ->setLang('nl');

        $this->assertSame($expected, $this->subject->toJson());
    }

    public function testToString(): void
    {
        $expected = json_encode([
            'title' => 'title',
            'options' => [
                'body' => 'body',
                'dir' => 'auto',
                'icon' => 'example',
                'lang' => 'nl',
                'tag' => 'test',
                'timestamp' => 12345,
                'vibrate' => [1, 2, 3],
            ],
        ]);

        $this->subject->setTitle('title')
                      ->setBody('body')
                      ->setIcon('example')
                      ->setUrgency('high')
                      ->setTopic('topic')
                      ->setTag('test')
                      ->setVibrate([1, 2, 3])
                      ->setTimestamp(12345)
                      ->setLang('nl');

        $this->assertSame($expected, $this->subject->__toString());
    }

    public function testJsonSerialize(): void
    {
        $expected = json_encode([
            'title' => 'title',
            'options' => [
                'body' => 'body',
                'dir' => 'auto',
                'icon' => 'example',
                'lang' => 'nl',
                'tag' => 'test',
                'timestamp' => 12345,
                'vibrate' => [1, 2, 3],
            ],
        ]);

        $this->subject->setTitle('title')
                      ->setBody('body')
                      ->setIcon('example')
                      ->setUrgency('high')
                      ->setTopic('topic')
                      ->setTag('test')
                      ->setVibrate([1, 2, 3])
                      ->setTimestamp(12345)
                      ->setLang('nl');

        $this->assertSame($expected, $this->subject->jsonSerialize());
    }

    public function testThatIfMessageIsSilentNoVibrationPatternExists(): void
    {
        $this->subject->setVibrate([1, 2, 3])
                      ->setSilent(true);

        $result = $this->subject->toArray();

        $this->assertArrayNotHasKey('vibrate', $result['options']);
    }

    public function testThatIfMessageIsNotSilentVibrationPatternExists(): void
    {
        $this->subject->setVibrate([1, 2, 3])
                      ->setSilent(false);

        $result = $this->subject->toArray();

        $this->assertArrayHasKey('vibrate', $result['options']);
    }

    public function testThatMapActionsToArrayWithNoActionsWillReturnNull(): void
    {
        $this->subject->setActions(null);

        $result = $this->subject->toArray();

        $this->assertArrayNotHasKey('actions', $result['options']);
    }

    public function testThatMapActionsToArrayWithEmptyActionsWillReturnNull(): void
    {
        $this->subject->setActions([]);

        $result = $this->subject->toArray();

        $this->assertArrayNotHasKey('actions', $result['options']);
    }

    public function testThatMapActionsToArrayWitActionsWillReturnArray(): void
    {
        $this->subject->setActions([
            new class extends MessageAction
            {
                protected $action = 'test action';
                protected $title = 'test title';
            },
        ]);

        $result = $this->subject->toArray();

        $this->assertArrayHasKey('actions', $result['options']);
        $this->assertSame([[
            'action' => 'test action',
            'title' => 'test title',
            'icon' => null,
        ]], $result['options']['actions']);
    }

    public function testThatSetActionsWithNullWillSetNull(): void
    {
        $this->subject->setActions(null);

        $this->assertNull($this->subject->getActions());
    }

    public function testThatSetActionsWithIncorrectClassWillThrowException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->subject->setActions([
            new class{}
        ]);
    }

    protected function getPackageProviders($app): array
    {
        return [LaravelWebPushServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new PushMessage();
    }

}
