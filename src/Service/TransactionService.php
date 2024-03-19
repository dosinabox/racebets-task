<?php

namespace App\Service;

use App\Entity\Transaction;
use App\Entity\User;
use App\Exception\InsufficientBalanceException;
use App\Exception\UnknownTransactionTypeException;
use Exception;

class TransactionService
{
    const string TYPE_DEPOSIT = 'deposit';
    const string TYPE_WITHDRAWAL = 'withdrawal';

    public function __construct(private readonly DatabaseService $databaseService)
    {
    }

    /**
     * @param string|null $transactionType
     * @param int $userID
     * @param int $amount
     * @return bool
     * @throws Exception
     */
    public function addTransaction(?string $transactionType, int $userID, int $amount): bool
    {
        try {
            $this->databaseService->getConnection()->beginTransaction();

            $user = $this->databaseService->findOneByID(User::TABLE, $userID);
            $transaction = match ($transactionType) {
                self::TYPE_DEPOSIT => $this->deposit($user, $userID, $amount),
                self::TYPE_WITHDRAWAL => $this->withdraw($user, $userID, $amount),
                default => throw new UnknownTransactionTypeException($transactionType),
            };

            if ($transaction) {
                $this->databaseService->addRowToTable(Transaction::TABLE,
                    [
                        Transaction::COLUMN_TYPE => $transactionType,
                        Transaction::COLUMN_AMOUNT => $amount,
                        Transaction::COLUMN_USER_ID => $userID
                    ]
                );
            }

            $this->databaseService->getConnection()->commit();

            return $transaction;
        } catch (Exception $exception) {
            $this->databaseService->getConnection()->rollBack();

            throw $exception;
        }
    }

    /**
     * @param array $user
     * @param int $userID
     * @param float $amount
     * @return bool
     * @throws Exception
     */
    private function deposit(array $user, int $userID, float $amount): bool
    {
        $availableBalance = (float)$user[User::COLUMN_MONEY_REAL];
        $availableBonusBalance = (float)$user[User::COLUMN_MONEY_BONUS];
        $currentBonus = (float)$user[User::COLUMN_BONUS];
        $bonusAmount = 0;

        if ($currentBonus > 0) {
            //TODO should be every third
            $bonusAmount = ($amount * $currentBonus) / 100;
        }

        return $this->databaseService->updateOneByID(User::TABLE, $userID,
            [
                User::COLUMN_MONEY_REAL => $availableBalance + $amount,
                User::COLUMN_MONEY_BONUS => $availableBonusBalance + $bonusAmount
            ]
        );
    }

    /**
     * @param array $user
     * @param int $userID
     * @param float $amount
     * @return bool
     * @throws Exception
     */
    private function withdraw(array $user, int $userID, float $amount): bool
    {
        $availableBalance = (float)$user[User::COLUMN_MONEY_REAL];

        if ($amount > $availableBalance) {
            throw new InsufficientBalanceException($userID);
        }

        return $this->databaseService->updateOneByID(User::TABLE, $userID,
            [
                User::COLUMN_MONEY_REAL => $availableBalance - $amount
            ]
        );
    }
}
