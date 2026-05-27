<?php

namespace App\Application\User\Login;

use App\Domain\User\UserRepositoryInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class LoginUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly JWTTokenManagerInterface $jwtManager
    ) {
    }

    public function execute(LoginCommand $command): string
    {
        $user = $this->userRepository->findByEmail($command->email);

        if (!$user) {
            throw new AuthenticationException('Invalid credentials.');
        }

        if (!$this->passwordHasher->isPasswordValid($user, $command->password)) {
            throw new AuthenticationException('Invalid credentials.');
        }

        return $this->jwtManager->create($user);
    }
}
