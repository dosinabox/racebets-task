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
                    'bonus' => random_int(5, 20)
                ]
            );
        } catch (\Throwable $throwable) {
            return new JsonResponse(
                [
                    'message' => 'Error on adding user: ' . $throwable->getMessage()
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        if ($user) {
            return new JsonResponse(
                [
                    'message' => 'User added.'
                ],
                Response::HTTP_CREATED
            );
        }

        return new JsonResponse(
            [
                'message' => 'User not added.'
            ],
            Response::HTTP_BAD_REQUEST
        );
    }

    #[Route(path: '/users/edit/{id}', name: 'editUser', methods: 'POST')]
    public function update(int $id, Request $request): Response
    {
        try {
            //TODO make a single request
            if (!$this->databaseService->findOneByID('users', $id)) {
                return new JsonResponse(
                    [
                        'message' => 'User not found.'
                    ],
                    Response::HTTP_NOT_FOUND
                );
            }

            $this->databaseService->updateOneByID('users', $id,
                [
                    'email' => $request->getPayload()->get('email'),
                    'firstName' => $request->getPayload()->get('firstName'),
                    'lastName' => $request->getPayload()->get('lastName'),
                    'gender' => $request->getPayload()->get('gender'),
                    'country' => $request->getPayload()->get('country')
                ]
            );
        } catch (\Throwable $throwable) {
            return new JsonResponse(
                [
                    'message' => 'Error on updating user: ' . $throwable->getMessage()
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return new JsonResponse(
            [
                'message' => 'User updated.'
            ]
        );
    }

    #[Route(path: '/users/{id}', name: 'showUser', methods: 'GET')]
    public function show(int $id): Response
    {
        try {
            $user = $this->databaseService->findOneByID('users', $id);
        } catch (\Throwable $throwable) {
            return new JsonResponse(
                [
                    'message' => 'Error on finding user: ' . $throwable->getMessage()
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        if ($user) {
            return new JsonResponse(
                [
                    'user' => $user
                ]
            );
        }

        return new JsonResponse(
            [
                'message' => 'User not found.'
            ],
            Response::HTTP_NOT_FOUND
        );
    }
}
