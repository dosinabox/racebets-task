<?php

namespace App\Controller;

use App\Service\TransactionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TransactionController extends AbstractController
{
    public function __construct(private readonly TransactionService $transactionService)
    {
    }

    #[Route(path: '/transactions/{userID}', name: 'transaction', methods: 'POST')]
    public function create(int $userID, Request $request): Response
    {
        switch ($request->getPayload()->get('type')) {
            case TransactionService::TYPE_DEPOSIT:
                $transaction = $this->transactionService->deposit($userID, $request->getPayload()->get('amount'));
                break;
            case TransactionService::TYPE_WITHDRAWAL:
                $transaction = $this->transactionService->withdraw($userID, $request->getPayload()->get('amount'));
                break;
            default:
                return new JsonResponse(
                    [
                        'message' => 'Unknown type of transaction.'
                    ],
                    Response::HTTP_BAD_REQUEST
                );
        }

        if ($transaction) {
            return new JsonResponse(
                [
                    'message' => 'Transaction successful.'
                ]
            );
        }

        return new JsonResponse(
            [
                'message' => 'Transaction failed.'
            ],
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
