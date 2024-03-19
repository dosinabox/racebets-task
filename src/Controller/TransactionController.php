<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Service\TransactionService;
use Exception;
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

    #[Route(path: '/api/v1/transactions/{userID}', name: 'addTransaction', methods: 'POST')]
    public function add(int $userID, Request $request): Response
    {
        try {
            $transactionType = $request->getPayload()->get(Transaction::COLUMN_TYPE);
            $amount = $request->getPayload()->get(Transaction::COLUMN_AMOUNT);
            $transaction = $this->transactionService->addTransaction($transactionType, $userID, $amount);
        } catch (Exception $exception) {
            $error = $exception->getMessage();
            $code = $exception->getCode();
        }

        return new JsonResponse(
            [
                'success' => $transaction ?? false,
                'error' => $error ?? null
            ],
            $code ?? Response::HTTP_CREATED
        );
    }
}
