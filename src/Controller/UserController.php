<?php

namespace App\Controller;

use App\Service\DatabaseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public function __construct(private readonly DatabaseService $databaseService)
    {
    }

    #[Route(path: '/users/add', name: 'addUser', methods: 'POST')]
    public function add(Request $request): Response
    {
        try {
            $user = $this->databaseService->addRowToTable('users',
                [
                    'email' => $request->getPayload()->get('email'),
                    'firstName' => $request->getPayload()->get('firstName'),
                    'lastName' => $request->getPayload()->get('lastName'),
                    'gender' => $request->getPayload()->get('gender'),
                    'country' => $request->getPayload()->get('country'),
                    'money_real' => 0,
                    'money_bonus' => 0,
                    'bonus' => rand(5, 20),
                ]
            );
        } catch (\Throwable $throwable) {
            return new JsonResponse(
                [
                    'code' => $throwable->getCode(),
                    'message' => 'Error on adding user: ' . $throwable->getMessage(),
                ]
            );
        }

        if ($user) {
            return new JsonResponse(
                [
                    'code' => Response::HTTP_CREATED,
                    'message' => 'User added.',
                ]
            );
        }

        return new JsonResponse(
            [
                'code' => Response::HTTP_BAD_REQUEST,
                'message' => 'User not added.',
            ]
        );
    }
}
