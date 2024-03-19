<?php

namespace App\Controller;

use App\Entity\User;
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

    #[Route(path: '/api/v1/users/add', name: 'addUser', methods: 'POST')]
    public function add(Request $request): Response
    {
        try {
            $isAdded = $this->databaseService->addRowToTable(User::TABLE,
                [
                    User::COLUMN_EMAIL => $request->getPayload()->get(User::COLUMN_EMAIL),
                    User::COLUMN_FIRSTNAME => $request->getPayload()->get(User::COLUMN_FIRSTNAME),
                    User::COLUMN_LASTNAME => $request->getPayload()->get(User::COLUMN_LASTNAME),
                    User::COLUMN_GENDER => $request->getPayload()->get(User::COLUMN_GENDER),
                    User::COLUMN_COUNTRY => $request->getPayload()->get(User::COLUMN_COUNTRY),
                    User::COLUMN_BONUS => random_int(5, 20)
                ]
            );
        } catch (Exception $exception) {
            $error = $exception->getMessage();
            $errorCode = $exception->getCode();
        }

        return new JsonResponse(
            [
                'success' => $isAdded ?? false,
                'error' => $error ?? null
            ],
            $errorCode ?? Response::HTTP_CREATED
        );
    }

    #[Route(path: '/api/v1/users/edit/{id}', name: 'editUser', methods: 'POST')]
    public function update(int $id, Request $request): Response
    {
        try {
            //find the user first to properly handle the exception if not found
            //TODO use $user to verify something
            $user = $this->databaseService->findOneByID(User::TABLE, $id);
            $isUpdated = $this->databaseService->updateOneByID(User::TABLE, $id,
                [
                    User::COLUMN_EMAIL => $request->getPayload()->get(User::COLUMN_EMAIL),
                    User::COLUMN_FIRSTNAME => $request->getPayload()->get(User::COLUMN_FIRSTNAME),
                    User::COLUMN_LASTNAME => $request->getPayload()->get(User::COLUMN_LASTNAME),
                    User::COLUMN_GENDER => $request->getPayload()->get(User::COLUMN_GENDER),
                    User::COLUMN_COUNTRY => $request->getPayload()->get(User::COLUMN_COUNTRY)
                ]
            );
        } catch (Exception $exception) {
            $error = $exception->getMessage();
            $errorCode = $exception->getCode();
        }

        return new JsonResponse(
            [
                'success' => $isUpdated ?? false,
                'error' => $error ?? null
            ],
            $errorCode ?? Response::HTTP_OK
        );
    }

    #[Route(path: '/api/v1/users/{id}', name: 'showUser', methods: 'GET')]
    public function show(int $id): Response
    {
        try {
            $user = $this->databaseService->findOneByID(User::TABLE, $id);
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
