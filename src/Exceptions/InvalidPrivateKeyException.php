<?php

namespace AlexLisenkov\LaravelWebPush\Exceptions;

class InvalidPrivateKeyException extends \InvalidArgumentException
{
    protected $message = 'Private key is incorrect';
}
