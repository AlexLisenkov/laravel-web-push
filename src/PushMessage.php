<?php

namespace AlexLisenkov\LaravelWebPush;

use AlexLisenkov\LaravelWebPush\Contracts\PushMessageContract;
use AlexLisenkov\LaravelWebPush\Contracts\PushSubscriptionContract;
use AlexLisenkov\LaravelWebPush\Contracts\WebPushContract;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Support\Facades\App;

abstract class PushMessage implements PushMessageContract
{
    /**
     * @var string
     */
    protected $title = '';
    /**
     * @var string
     */
    protected $body = '';
    /**
     * @var null|string
     */
    protected $iconPath;
    /**
     * @var string|null
     */
    protected $urgency;
    /**
     * @var string|null
     */
    protected $topic;
    /**
     * @var string|null
     */
    protected $tag;
    /**
     * https://w3c.github.io/vibration/#idl-def-vibratepattern
     * array with 3 indexes
     * delay, time to vibrate, sleep
     *
     * @var array|null
     */
    protected $vibration_pattern = [0, 200, 1000];
    /**
     * @var int|null
     */
    protected $timestamp;
    /**
     * @var string|null
     */
    protected $lang;
    /**
     * @var bool
     */
    protected $silent = false;
    /**
     * @var string
     */
    protected $queue;

    /**
     * @param PushSubscriptionContract $push_subscription
     *
     * @return PromiseInterface
     */
    public function sendTo(PushSubscriptionContract $push_subscription): PromiseInterface
    {
        /** @var WebPushContract $web_push */
        $web_push = App::make(WebPushContract::class);

        return $web_push->sendMessage($this, $push_subscription);
    }

    /**
     * Get Title
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Set Title
     *
     * @param string $title
     *
     * @return PushMessageContract
     */
    public function setTitle(string $title): PushMessageContract
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get Body
     *
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * Set Body
     *
     * @param string $body
     *
     * @return PushMessageContract
     */
    public function setBody(string $body): PushMessageContract
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get IconPath
     *
     * @return null|string
     */
    public function getIconPath(): ?string
    {
        return $this->iconPath;
    }

    /**
     * Set IconPath
     *
     * @param null|string $iconPath
     *
     * @return PushMessageContract
     */
    public function setIconPath(?string $iconPath): PushMessageContract
    {
        $this->iconPath = $iconPath;

        return $this;
    }

    /**
     * Get Urgency
     *
     * @return string
     */
    public function getUrgency(): ?string
    {
        return $this->urgency;
    }

    /**
     * Set Urgency
     *
     * @param string $urgency
     *
     * @return PushMessageContract
     */
    public function setUrgency(string $urgency): PushMessageContract
    {
        $this->urgency = $urgency;

        return $this;
    }

    /**
     * Get Topic
     *
     * @return string
     */
    public function getTopic(): ?string
    {
        return $this->topic;
    }

    /**
     * Set Topic
     *
     * @param string $topic
     *
     * @return PushMessageContract
     */
    public function setTopic(string $topic): PushMessageContract
    {
        $this->topic = $topic;

        return $this;
    }

    /**
     * Get Tag
     *
     * @return null|string
     */
    public function getTag(): ?string
    {
        return $this->tag;
    }

    /**
     * Set Tag
     *
     * @param null|string $tag
     *
     * @return PushMessageContract
     */
    public function setTag(?string $tag): PushMessageContract
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get VibrationPattern
     *
     * @return array|null
     */
    public function getVibrationPattern(): ?array
    {
        return $this->vibration_pattern;
    }

    /**
     * Set VibrationPattern
     *
     * @param array|null $vibration_pattern
     *
     * @return PushMessageContract
     */
    public function setVibrationPattern(?array $vibration_pattern): PushMessageContract
    {
        $this->vibration_pattern = $vibration_pattern;

        return $this;
    }

    /**
     * Get Timestamp
     *
     * @return int|null
     */
    public function getTimestamp(): ?int
    {
        return $this->timestamp;
    }

    /**
     * Set Timestamp
     *
     * @param int|null $timestamp
     *
     * @return PushMessageContract
     */
    public function setTimestamp(?int $timestamp): PushMessageContract
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * Get Lang
     *
     * @return null|string
     */
    public function getLang(): ?string
    {
        return $this->lang;
    }

    /**
     * Set Lang
     *
     * @param null|string $lang
     *
     * @return PushMessageContract
     */
    public function setLang(?string $lang): PushMessageContract
    {
        $this->lang = $lang;

        return $this;
    }

    /**
     * Get Silent
     *
     * @return bool
     */
    public function isSilent(): bool
    {
        return $this->silent;
    }

    /**
     * Set Silent
     *
     * @param bool $silent
     *
     * @return PushMessageContract
     */
    public function setSilent(bool $silent): PushMessageContract
    {
        $this->silent = $silent;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array_filter([
            'title' => $this->getTitle(),
            'body' => $this->getBody(),
            'icon' => $this->getIconPath(),
            'urgency' => $this->getUrgency(),
            'topic' => $this->getTopic(),
            'tag' => $this->getTag(),
            'vibrate' => !$this->isSilent() ? $this->getVibrationPattern() : null,
            'timestamp' => $this->getTimestamp(),
            'silent' => $this->isSilent(),
            'lang' => $this->getLang(),
        ]);
    }

    /**
     * @param int $options
     *
     * @return false|string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * @return false|string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * @return false|mixed|string
     */
    public function jsonSerialize()
    {
        return $this->toJson();
    }
}
