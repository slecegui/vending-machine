<?php

namespace App\Infrastructure\CLI;

use App\Domain\User\User;
use App\Domain\User\UserRepositoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:seed-users', description: 'Creates initial administrator and technician users')]
class CreateUserCommand extends Command
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $usersToCreate = [
            [
                'email' => 'admin@vending.com',
                'password' => 'admin123',
                'roles' => ['ROLE_ADMIN'],
                'label' => 'Administrator'
            ],
            [
                'email' => 'tech@vending.com',
                'password' => 'tech123',
                'roles' => ['ROLE_TECH'],
                'label' => 'Technician'
            ],
        ];

        foreach ($usersToCreate as $userData) {
            $existingUser = $this->userRepository->findByEmail($userData['email']);
            if ($existingUser) {
                $output->writeln(sprintf('<comment>%s user %s already exists. Skipping.</comment>', $userData['label'], $userData['email']));
                continue;
            }

            $user = new User($userData['email'], '', $userData['roles']);
            $hashedPassword = $this->passwordHasher->hashPassword($user, $userData['password']);
            $user->setPassword($hashedPassword);

            $this->userRepository->save($user);

            $output->writeln(sprintf('<info>%s user %s created successfully.</info>', $userData['label'], $userData['email']));
        }

        return Command::SUCCESS;
    }
}
