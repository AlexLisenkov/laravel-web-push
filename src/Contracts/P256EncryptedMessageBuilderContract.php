<?php
declare(strict_types=1);

namespace AlexLisenkov\LaravelWebPush\Contracts;

interface P256EncryptedMessageBuilderContract
{
    public function withPublicKey(
        string $public_key
    ): self;

    public function withAuthToken(
        string $auth_token
    ): self;

    public function build(string $payload): P256EncryptedMessageContract;
}
