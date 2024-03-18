<?php

namespace App\Service;

use App\Entity\Transaction;
use App\Entity\User;
use Exception;

class TransactionService
{
    const string TYPE_DEPOSIT = 'deposit';
    const string TYPE_WITHDRAWAL = 'withdrawal';

    public function __construct(private readonly DatabaseService $databaseService)
    {
    }

    /**
     * @param int $userID
     * @param float $amount
     * @return bool
     * @throws Exception
     */
    public function deposit(int $userID, float $amount): bool
    {
        try {
            $this->databaseService->getConnection()->beginTransaction();

            //double requests just to get the user's current money amount - not good!
            //TODO make it better
            $user = $this->databaseService->findOneByID(User::TABLE, $userID);
            $this->databaseService->updateOneByID(User::TABLE, $userID,
                [
                    User::COLUMN_MONEY_REAL => (float)$user[User::COLUMN_MONEY_REAL] + $amount
                ]
            );
            $this->databaseService->addRowToTable(Transaction::TABLE,
                [
                    Transaction::COLUMN_TYPE => self::TYPE_DEPOSIT,
                    Transaction::COLUMN_AMOUNT => $amount,
                    Transaction::COLUMN_USER_ID => $userID
                ]
            );

            $this->databaseService->getConnection()->commit();

            return true;
        } catch (Exception $exception) {
            $this->databaseService->getConnection()->rollBack();

            throw $exception;
        }
    }

    /**
     * @param int $userID
     * @param float $amount
     * @return bool
     * @throws Exception
     */
    public function withdraw(int $userID, float $amount): bool
    {
        try {
            $this->databaseService->getConnection()->beginTransaction();

            //double requests just to get the user's current money amount - not good!
            //TODO make it better
            $user = $this->databaseService->findOneByID(User::TABLE, $userID);
            $this->databaseService->updateOneByID(User::TABLE, $userID,
                [
                    User::COLUMN_MONEY_REAL => (float)$user[User::COLUMN_MONEY_REAL] - $amount
                ]
            );
            $this->databaseService->addRowToTable(Transaction::TABLE,
                [
                    Transaction::COLUMN_TYPE => self::TYPE_WITHDRAWAL,
                    Transaction::COLUMN_AMOUNT => $amount,
                    Transaction::COLUMN_USER_ID => $userID
                ]
            );

            $this->databaseService->getConnection()->commit();

            return true;
        } catch (Exception $exception) {
            $this->databaseService->getConnection()->rollBack();

            throw $exception;
        }
    }
}
