<?php

namespace App\Infrastructure\User;

use App\Application\User\Login\LoginCommand;
use App\Application\User\Login\LoginUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class LoginController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request, LoginUseCase $useCase): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? $data['username'] ?? null;
        $password = $data['password'] ?? null;

        if (!$email || !$password) {
            return new JsonResponse(['error' => 'Missing credentials'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $token = $useCase->execute(new LoginCommand($email, $password));

            return new JsonResponse(['token' => $token]);
        } catch (AuthenticationException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'An error occurred'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
