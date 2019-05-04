<?php

namespace AlexLisenkov\LaravelWebPush;

use AlexLisenkov\LaravelWebPush\Contracts\MessageActionContract;
use AlexLisenkov\LaravelWebPush\Contracts\PushMessageContract;
use AlexLisenkov\LaravelWebPush\Contracts\PushSubscriptionContract;
use AlexLisenkov\LaravelWebPush\Contracts\WebPushContract;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Support\Facades\App;

class PushMessage implements PushMessageContract
{
    /**
     * JSON encoding options
     */
    public const DEFAULT_ENCODING_OPTIONS = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;

    /**
     * Available notification directions
     */
    public const NOTIFICATION_DIRECTIONS = ['auto', 'ltr', 'rtl'];

    /**
     * The title that must be shown within the notification
     *
     * @var string
     */
    protected $title = '';

    /**
     * An array of actions to display in the notification. The members of the array should be an object literal. It may
     * contain the following values:
     *
     * @var MessageActionContract[]
     */
    protected $actions;

    /**
     * A badge resource is an icon representing the web application, or the category of the notification if the web
     * application sends a wide variety of notifications. It may be used to represent the notification when there is
     * not enough space to display the notification itself. It may also be displayed inside the notification, but then
     * it should have less visual priority than the image resource and icon resource.
     *
     * @string|null
     */
    protected $badge;

    /**
     * A string representing an extra content to display within the notification.
     *
     * @var string
     */
    protected $body = '';

    /**
     * Arbitrary data that you want to be associated with the notification. This can be of any data type.
     */
    protected $data;

    /**
     * The direction of the notification; it can be auto, ltr or rtl
     *
     * @var string
     */
    protected $dir = 'auto';

    /**
     * The URL of an image to be used as an icon by the notification.
     *
     * @var null|string
     */
    protected $icon;

    /**
     * An image resource is a picture shown as part of the content of the notification, and should be displayed with
     * higher visual priority than the icon resource and badge resource, though it may be displayed in fewer
     * circumstances.
     *
     * @var string|null
     */
    protected $image;

    /**
     * Specify the lang used within the notification. This string must be a valid BCP 47 language tag.
     * https://tools.ietf.org/html/bcp47
     *
     * @var string|null
     */
    protected $lang;

    /**
     * When set indicates that the end user should be alerted after the show steps have run with a new notification
     * that has the same tag as an existing notification.
     *
     * @bool
     */
    protected $renotify = false;

    /**
     * When set, indicates that on devices with a sufficiently large screen, the notification should remain readily
     * available until the user activates or dismisses the notification.
     *
     * @bool
     */
    protected $require_interaction = false;

    /**
     * An ID for a given notification that allows you to find, replace, or remove the notification using a script if
     * necessary.
     *
     * @var string|null
     */
    protected $tag;

    /**
     * Timestamp which is a DOMTimeStamp representing the time, in milliseconds since 00:00:00 UTC on 1 January 1970,
     * of the event for which the notification was created.
     * https://heycam.github.io/webidl/#DOMTimeStamp
     *
     * @var int|null
     */
    protected $timestamp;

    /**
     * When set indicates that no sounds or vibrations should be made.
     *
     * @var bool|null
     */
    protected $silent;

    /**
     * Topics are strings that can be used to replace a pending messages with a new message if they have matching topic
     * names. This is useful in scenarios where multiple messages are sent while a device is offline, and you really
     * only want a user to see the latest message when the device is turned on.
     *
     * @var string
     */
    protected $topic;

    /**
     * Urgency indicates to the push service how important a message is to the user. This can be used by the push
     * service to help conserve the battery life of a user's device by only waking up for important messages when
     * battery is low. It can be: very-low, low, normal or high
     *
     * @var string|null
     */
    protected $urgency;

    /**
     * https://w3c.github.io/vibration/#idl-def-vibratepattern
     * A vibration pattern to run with the display of the notification.
     * A vibration pattern can be an array with as few as one member.
     * The values are times in milliseconds where the even indices (0, 2, 4, etc.) indicate how long to vibrate and the
     * odd indices indicate how long to pause.
     * For example, [300, 100, 400] would vibrate 300ms, pause 100ms, then vibrate 400ms.
     *
     * @var array|null
     */
    protected $vibrate = [0, 200, 1000];

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
     * @return string
     */
    public function __toString(): string
    {
        if ($string = $this->toJson()) {
            return (string) $string;
        }

        return '';
    }

    /**
     * @param int $options
     *
     * @return false|string
     */
    public function toJson($options = self::DEFAULT_ENCODING_OPTIONS)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_filter([
            'title' => $this->getTitle(),
            'options' => array_filter([
                'actions' => $this->mapActionsToArray(),
                'badge' => $this->getBadge(),
                'body' => $this->getBody(),
                'data' => $this->getData(),
                'dir' => $this->getDir(),
                'icon' => $this->getIcon(),
                'image' => $this->getImage(),
                'lang' => $this->getLang(),
                'renotify' => $this->getRenotify(),
                'requireInteraction' => $this->getRequireInteraction(),
                'silent' => $this->isSilent(),
                'tag' => $this->getTag(),
                'timestamp' => $this->getTimestamp(),
                'vibrate' => !$this->isSilent() ? $this->getVibrate() : null,
            ]),
        ]);
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
     * @return array|null
     */
    private function mapActionsToArray(): ?array
    {
        if (!is_array($this->getActions())) {
            return null;
        }

        $actions = $this->getActions();

        return array_map(function (MessageActionContract $action) {
            return $action->toArray();
        }, $actions);
    }

    /**
     * Get Actions
     *
     * @return MessageActionContract[]|null
     */
    public function getActions(): ?array
    {
        return $this->actions;
    }

    /**
     * Set Actions
     *
     * @param MessageActionContract[] $actions
     *
     * @return PushMessage
     */
    public function setActions(array $actions): PushMessage
    {
        foreach ($actions as $action) {
            if (!$action instanceof MessageActionContract) {
                throw new \InvalidArgumentException(get_class($action) . ' must implement ' . MessageActionContract::class);
            }
        }

        $this->actions = $actions;

        return $this;
    }

    /**
     * Get Badge
     *
     * @return string|null
     */
    public function getBadge(): ?string
    {
        return $this->badge;
    }

    /**
     * Set Badge
     *
     * @param string $badge
     *
     * @return PushMessage
     */
    public function setBadge(string $badge): PushMessage
    {
        $this->badge = $badge;

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
     * Get Data
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set Data
     *
     * @param mixed $data
     *
     * @return PushMessage
     */
    public function setData($data): PushMessage
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get Dir
     *
     * @return string
     */
    public function getDir(): string
    {
        return $this->dir;
    }

    /**
     * Set Dir
     *
     * @param string $dir
     *
     * @return PushMessage
     */
    public function setDir(string $dir): PushMessage
    {
        if (!array_has(self::NOTIFICATION_DIRECTIONS, $dir)) {
            throw new \InvalidArgumentException('Direction must be one of ' . implode(', ',
                    self::NOTIFICATION_DIRECTIONS));
        }

        $this->dir = $dir;

        return $this;
    }

    /**
     * Get IconPath
     *
     * @return null|string
     */
    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * Set IconPath
     *
     * @param null|string $icon
     *
     * @return PushMessageContract
     */
    public function setIcon(?string $icon): PushMessageContract
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get Image
     *
     * @return string|null
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * Set Image
     *
     * @param string|null $image
     *
     * @return PushMessage
     */
    public function setImage(?string $image): PushMessage
    {
        $this->image = $image;

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
     * Get Renotify
     *
     * @return mixed
     */
    public function getRenotify()
    {
        return $this->renotify;
    }

    /**
     * Set Renotify
     *
     * @param mixed $renotify
     *
     * @return PushMessage
     */
    public function setRenotify($renotify): PushMessage
    {
        $this->renotify = $renotify;

        return $this;
    }

    /**
     * Get RequireInteraction
     *
     * @return mixed
     */
    public function getRequireInteraction()
    {
        return $this->require_interaction;
    }

    /**
     * Set RequireInteraction
     *
     * @param mixed $require_interaction
     *
     * @return PushMessage
     */
    public function setRequireInteraction($require_interaction): PushMessage
    {
        $this->require_interaction = $require_interaction;

        return $this;
    }

    /**
     * Get Silent
     *
     * @return bool
     */
    public function isSilent(): ?bool
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
    public function setSilent(?bool $silent): PushMessageContract
    {
        $this->silent = $silent;

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
     * Get VibrationPattern
     *
     * @return array|null
     */
    public function getVibrate(): ?array
    {
        return $this->vibrate;
    }

    /**
     * Set VibrationPattern
     *
     * @param array|null $vibrate
     *
     * @return PushMessageContract
     */
    public function setVibrate(?array $vibrate): PushMessageContract
    {
        $this->vibrate = $vibrate;

        return $this;
    }

    /**
     * @return false|mixed|string
     */
    public function jsonSerialize()
    {
        return $this->toJson();
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
     * @return PushMessage
     */
    public function setTopic(?string $topic): PushMessage
    {
        $this->topic = $topic;

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
     * @return PushMessage
     */
    public function setUrgency(?string $urgency): PushMessage
    {
        $this->urgency = $urgency;

        return $this;
    }
}
