<?php

namespace App\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

class UserAuthenticationException extends AuthenticationException
{
    public function __construct(string $message = 'This user is not active. Please contact support.', array $data = [], int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getMessageKey(): string
    {
        return $this->getMessage();
    }
}