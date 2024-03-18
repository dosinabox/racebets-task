<?php

namespace App\Controller;

use App\Service\DatabaseService;
use Exception;
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
                    'bonus' => random_int(5, 20)
                ]
            );
        } catch (Exception $exception) {
            $error = $exception->getMessage();
            $errorCode = $exception->getCode();
        }

        return new JsonResponse(
            [
                'success' => $user ?? false,
                'error' => $error ?? null
            ],
            $errorCode ?? Response::HTTP_OK
        );
    }

    #[Route(path: '/users/edit/{id}', name: 'editUser', methods: 'POST')]
    public function update(int $id, Request $request): Response
    {
        try {
            $user = $this->databaseService->updateOneByID('users', $id,
                [
                    'email' => $request->getPayload()->get('email'),
                    'firstName' => $request->getPayload()->get('firstName'),
                    'lastName' => $request->getPayload()->get('lastName'),
                    'gender' => $request->getPayload()->get('gender'),
                    'country' => $request->getPayload()->get('country')
                ]
            );
        } catch (Exception $exception) {
            $error = $exception->getMessage();
            $errorCode = $exception->getCode();
        }

        return new JsonResponse(
            [
                'success' => $user ?? false,
                'error' => $error ?? null
            ],
            $errorCode ?? Response::HTTP_OK
        );
    }

    #[Route(path: '/users/{id}', name: 'showUser', methods: 'GET')]
    public function show(int $id): Response
    {
        try {
            $user = $this->databaseService->findOneByID('users', $id);
        } catch (Exception $exception) {
            $error = $exception->getMessage();
            $errorCode = $exception->getCode();
        }

        return new JsonResponse(
            [
                'user' => $user ?? [],
                'success' => (bool)($user ?? false),
                'error' => $error ?? null
            ],
            $errorCode ?? Response::HTTP_OK
        );
    }
}
