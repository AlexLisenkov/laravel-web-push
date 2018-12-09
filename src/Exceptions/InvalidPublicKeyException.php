<?php

namespace AlexLisenkov\LaravelWebPush\Exceptions;

class InvalidPublicKeyException extends \InvalidArgumentException
{
    protected $message = 'Public key is incorrect';
}
