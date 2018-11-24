<?php
declare(strict_types=1);

namespace AlexLisenkov\LaravelWebPush\Contracts;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

interface PushMessageContract extends Jsonable, Arrayable, \JsonSerializable
{
    /**
     * @param PushSubscriptionContract $push_subscription
     *
     * @return PromiseInterface
     */
    public function sendTo(PushSubscriptionContract $push_subscription): PromiseInterface;

    /**
     * Get Title
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * Set Title
     *
     * @param string $title
     *
     * @return PushMessageContract
     */
    public function setTitle(string $title): PushMessageContract;

    /**
     * Get Body
     *
     * @return string
     */
    public function getBody(): string;

    /**
     * Set Body
     *
     * @param string $body
     *
     * @return PushMessageContract
     */
    public function setBody(string $body): PushMessageContract;

    /**
     * Get IconPath
     *
     * @return null|string
     */
    public function getIconPath(): ?string;

    /**
     * Set IconPath
     *
     * @param null|string $iconPath
     *
     * @return PushMessageContract
     */
    public function setIconPath(?string $iconPath): PushMessageContract;

    /**
     * Get Urgency
     *
     * @return string
     */
    public function getUrgency(): ?string;

    /**
     * Set Urgency
     *
     * @param string $urgency
     *
     * @return PushMessageContract
     */
    public function setUrgency(string $urgency): PushMessageContract;

    /**
     * Get Topic
     *
     * @return string
     */
    public function getTopic(): ?string;

    /**
     * Set Topic
     *
     * @param string $topic
     *
     * @return PushMessageContract
     */
    public function setTopic(string $topic): PushMessageContract;

    /**
     * Get Tag
     *
     * @return null|string
     */
    public function getTag(): ?string;

    /**
     * Set Tag
     *
     * @param null|string $tag
     *
     * @return PushMessageContract
     */
    public function setTag(?string $tag): PushMessageContract;

    /**
     * Get VibrationPattern
     *
     * @return array|null
     */
    public function getVibrationPattern(): ?array;

    /**
     * Set VibrationPattern
     *
     * @param array|null $vibration_pattern
     *
     * @return PushMessageContract
     */
    public function setVibrationPattern(?array $vibration_pattern): PushMessageContract;

    /**
     * Get Timestamp
     *
     * @return int|null
     */
    public function getTimestamp(): ?int;

    /**
     * Set Timestamp
     *
     * @param int|null $timestamp
     *
     * @return PushMessageContract
     */
    public function setTimestamp(?int $timestamp): PushMessageContract;

    /**
     * Get Lang
     *
     * @return null|string
     */
    public function getLang(): ?string;

    /**
     * Set Lang
     *
     * @param null|string $lang
     *
     * @return PushMessageContract
     */
    public function setLang(?string $lang): PushMessageContract;

    /**
     * Get Silent
     *
     * @return bool
     */
    public function isSilent(): bool;

    /**
     * Set Silent
     *
     * @param bool $silent
     *
     * @return PushMessageContract
     */
    public function setSilent(bool $silent): PushMessageContract;

    /**
     * @return array
     */
    public function toArray();

    /**
     * @param int $options
     *
     * @return false|string
     */
    public function toJson($options = 0);

    /**
     * @return false|mixed|string
     */
    public function jsonSerialize();
}
