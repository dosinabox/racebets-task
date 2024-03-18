<?php

namespace App\Service;

use Exception;

class TransactionService
{
    const string TYPE_DEPOSIT = 'deposit';
    const string TYPE_WITHDRAWAL = 'withdrawal';

    public function __construct(private readonly DatabaseService $databaseService)
    {
    }

    public function deposit(int $userID, float $amount): bool
    {
        try {
            $this->databaseService->getConnection()->beginTransaction();

            $user = $this->databaseService->findOneByID('users', $userID);
            $this->databaseService->updateOneByID('users', $userID,
                [
                    'money_real' => (float)$user['money_real'] + $amount
                ]
            );
            $this->databaseService->addRowToTable('transactions',
                [
                    'type' => self::TYPE_DEPOSIT,
                    'amount' => $amount,
                    'user_id' => $userID
                ]
            );

            $this->databaseService->getConnection()->commit();

            return true;
        } catch (Exception) {
            $this->databaseService->getConnection()->rollBack();
        }

        return false;
    }

    public function withdraw(int $userID, float $amount): bool
    {
        try {
            $this->databaseService->getConnection()->beginTransaction();

            $user = $this->databaseService->findOneByID('users', $userID);
            $this->databaseService->updateOneByID('users', $userID,
                [
                    'money_real' => (float)$user['money_real'] - $amount
                ]
            );
            $this->databaseService->addRowToTable('transactions',
                [
                    'type' => self::TYPE_WITHDRAWAL,
                    'amount' => $amount,
                    'user_id' => $userID
                ]
            );

            $this->databaseService->getConnection()->commit();

            return true;
        } catch (Exception) {
            $this->databaseService->getConnection()->rollBack();
        }

        return false;
    }
}
