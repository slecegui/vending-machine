<?php

namespace App\Application\User\Login;

class LoginCommand
{
    public function __construct(
        public readonly string $email,
        public readonly string $password
    ) {
    }
}
