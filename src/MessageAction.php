<?php

namespace AlexLisenkov\LaravelWebPush;

use AlexLisenkov\LaravelWebPush\Contracts\MessageActionContract;

class MessageAction implements MessageActionContract
{
    /**
     * A DOMString identifying a user action to be displayed on the notification.
     *
     * @var string
     */
    protected $action;

    /**
     * A DOMString containing action text to be shown to the user.
     *
     * @var string
     */
    protected $title;

    /**
     * A USVString containing the URL of an icon to display with the action.
     *
     * @var string
     */
    protected $icon;

    /**
     * Get Action
     *
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * Set Action
     *
     * @param string $action
     *
     * @return MessageAction
     */
    public function setAction(string $action): MessageAction
    {
        $this->action = $action;

        return $this;
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
     * @return MessageAction
     */
    public function setTitle(string $title): MessageAction
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get Icon
     *
     * @return string
     */
    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * Set Icon
     *
     * @param string $icon
     *
     * @return MessageAction
     */
    public function setIcon(string $icon): MessageAction
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'action' => $this->getAction(),
            'title' => $this->getTitle(),
            'icon' => $this->getIcon(),
        ];
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param int $options
     *
     * @return false|string
     */
    public function toJson($options = PushMessage::DEFAULT_ENCODING_OPTIONS)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        if( $string = $this->toJson() ){
            return (string) $string;
        }

        return '';
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize(): string
    {
        return $this->toJson();
    }
}
