<?php

namespace App\Controller;

use App\Exception\UnknownTransactionTypeException;
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

    #[Route(path: '/transactions/{userID}', name: 'transaction', methods: 'POST')]
    public function create(int $userID, Request $request): Response
    {
        try {
            $transactionType = $request->getPayload()->get('type');
            $transaction = match ($transactionType) {
                TransactionService::TYPE_DEPOSIT => $this->transactionService
                    ->deposit($userID, $request->getPayload()->get('amount')),
                TransactionService::TYPE_WITHDRAWAL => $this->transactionService
                    ->withdraw($userID, $request->getPayload()->get('amount')),
                default => throw new UnknownTransactionTypeException($transactionType),
            };
        } catch (Exception $exception) {
            $error = $exception->getMessage();
            $code = $exception->getCode();
        }

        return new JsonResponse(
            [
                'success' => $transaction ?? false,
                'error' => $error ?? null
            ],
            $code ?? Response::HTTP_OK
        );
    }
}
