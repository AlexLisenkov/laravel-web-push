<?php
declare(strict_types=1);

namespace AlexLisenkov\LaravelWebPush\Contracts;

use AlexLisenkov\LaravelWebPush\MessageAction;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

interface MessageActionContract extends Jsonable, Arrayable, \JsonSerializable
{
    /**
     * Get Action
     *
     * @return string
     */
    public function getAction(): string;

    /**
     * Set Action
     *
     * @param string $action
     *
     * @return MessageAction
     */
    public function setAction(string $action): MessageAction;

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
     * @return MessageAction
     */
    public function setTitle(string $title): MessageAction;

    /**
     * Get Icon
     *
     * @return string
     */
    public function getIcon(): ?string;

    /**
     * Set Icon
     *
     * @param string $icon
     *
     * @return MessageAction
     */
    public function setIcon(string $icon): MessageAction;
}
