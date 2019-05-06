<?php

namespace AlexLisenkov\LaravelWebPush;

use AlexLisenkov\LaravelWebPush\Contracts\PushSubscriptionContract;
use AlexLisenkov\LaravelWebPush\Contracts\WebPushContract;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Support\Facades\App;
use Orchestra\Testbench\TestCase;

class MessageActionTest extends TestCase
{
    /**
     * @var MessageAction
     */
    private $subject;

    public function testAction(): void
    {
        $this->subject->setAction('test action');

        $this->assertSame('test action', $this->subject->getAction());
    }

    public function testIcon(): void
    {
        $this->subject->setIcon('test icon');

        $this->assertSame('test icon', $this->subject->getIcon());
    }

    public function testTitle(): void
    {
        $this->subject->setTitle('test title');

        $this->assertSame('test title', $this->subject->getTitle());
    }

    public function testToArray(): void
    {
        $this->subject->setAction('test action');
        $this->subject->setIcon('test icon');
        $this->subject->setTitle('test title');

        $result = $this->subject->toArray();

        $this->assertSame([
            'action' => 'test action',
            'title' => 'test title',
            'icon' => 'test icon',
        ], $result);
    }

    public function testToJson(): void
    {
        $this->subject->setAction('test action');
        $this->subject->setIcon('test icon');
        $this->subject->setTitle('test title');

        $result = $this->subject->toJson();

        $this->assertSame(json_encode([
            'action' => 'test action',
            'title' => 'test title',
            'icon' => 'test icon',
        ]), $result);
    }

    public function testToString(): void
    {
        $this->subject->setAction('test action');
        $this->subject->setIcon('test icon');
        $this->subject->setTitle('test title');

        $result = $this->subject->__toString();

        $this->assertSame(json_encode([
            'action' => 'test action',
            'title' => 'test title',
            'icon' => 'test icon',
        ]), $result);
    }

    public function testJsonSerialize(): void
    {
        $this->subject->setAction('test action');
        $this->subject->setIcon('test icon');
        $this->subject->setTitle('test title');

        $result = $this->subject->jsonSerialize();

        $this->assertSame(json_encode([
            'action' => 'test action',
            'title' => 'test title',
            'icon' => 'test icon',
        ]), $result);
    }

    protected function getPackageProviders($app): array
    {
        return [LaravelWebPushServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new MessageAction();
    }

}
