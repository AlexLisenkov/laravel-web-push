<?php

namespace AlexLisenkov\LaravelWebPush\Exceptions;

use Throwable;

class MessageSerializationException extends \Exception
{
    public function __construct(string $class_name, $code = 0, Throwable $previous = null)
    {
        parent::__construct("Unable to serialize class \"$class_name\" to JSON", $code, $previous);
    }
}
