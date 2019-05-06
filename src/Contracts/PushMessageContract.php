<?php
declare(strict_types=1);

namespace AlexLisenkov\LaravelWebPush\Contracts;

use AlexLisenkov\LaravelWebPush\PushMessage;
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
     * @param int $options
     *
     * @return false|string
     */
    public function toJson($options = PushMessage::DEFAULT_ENCODING_OPTIONS);

    /**
     * @return array
     */
    public function toArray(): array;

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
     * Get Actions
     *
     * @return MessageActionContract[]|null
     */
    public function getActions(): ?array;

    /**
     * Set Actions
     *
     * @param MessageActionContract[] $actions
     *
     * @return PushMessage
     */
    public function setActions(array $actions): PushMessage;

    /**
     * Get Badge
     *
     * @return string|null
     */
    public function getBadge(): ?string;

    /**
     * Set Badge
     *
     * @param string $badge
     *
     * @return PushMessage
     */
    public function setBadge(string $badge): PushMessage;

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
     * Get Data
     *
     * @return mixed
     */
    public function getData();

    /**
     * Set Data
     *
     * @param mixed $data
     *
     * @return PushMessage
     */
    public function setData($data): PushMessage;

    /**
     * Get Dir
     *
     * @return string
     */
    public function getDir(): string;

    /**
     * Set Dir
     *
     * @param string $dir
     *
     * @return PushMessage
     */
    public function setDir(string $dir): PushMessage;

    /**
     * Get IconPath
     *
     * @return null|string
     */
    public function getIcon(): ?string;

    /**
     * Set IconPath
     *
     * @param null|string $icon
     *
     * @return PushMessageContract
     */
    public function setIcon(?string $icon): PushMessageContract;

    /**
     * Get Image
     *
     * @return string|null
     */
    public function getImage(): ?string;

    /**
     * Set Image
     *
     * @param string|null $image
     *
     * @return PushMessage
     */
    public function setImage(?string $image): PushMessage;

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
     * Get Renotify
     *
     * @return mixed
     */
    public function getRenotify();

    /**
     * Set Renotify
     *
     * @param mixed $renotify
     *
     * @return PushMessage
     */
    public function setRenotify($renotify): PushMessage;

    /**
     * Get RequireInteraction
     *
     * @return mixed
     */
    public function getRequireInteraction();

    /**
     * Set RequireInteraction
     *
     * @param mixed $require_interaction
     *
     * @return PushMessage
     */
    public function setRequireInteraction($require_interaction): PushMessage;

    /**
     * Get Silent
     *
     * @return bool
     */
    public function isSilent(): ?bool;

    /**
     * Set Silent
     *
     * @param bool $silent
     *
     * @return PushMessageContract
     */
    public function setSilent(?bool $silent): PushMessageContract;

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
     * Get VibrationPattern
     *
     * @return array|null
     */
    public function getVibrate(): ?array;

    /**
     * Set VibrationPattern
     *
     * @param array|null $vibrate
     *
     * @return PushMessageContract
     */
    public function setVibrate(?array $vibrate): PushMessageContract;

    /**
     * @return false|mixed|string
     */
    public function jsonSerialize();

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
     * @return PushMessage
     */
    public function setTopic(?string $topic): PushMessage;

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
     * @return PushMessage
     */
    public function setUrgency(?string $urgency): PushMessage;
}
