<?php

namespace App\Service;

use App\Entity\Transaction;
use App\Entity\User;
use App\Exception\UnknownReportTypeException;

class ReportService
{
    const string TYPE_TRANSACTIONS = 'transactions';

    public function __construct(private readonly DatabaseService $databaseService)
    {
    }

    public function getReport(?string $reportType, ?string $dateStart, ?string $dateEnd): false|array
    {
        return match ($reportType) {
            self::TYPE_TRANSACTIONS => $this->getTransactionsReport($dateStart, $dateEnd),
            default => throw new UnknownReportTypeException($reportType),
        };
    }

    private function getTransactionsReport(?string $dateStart, ?string $dateEnd): false|array
    {
        //TODO add unique customers column and use start/end date
        return $this->databaseService->leftJoin(
            Transaction::TABLE,
            User::TABLE,
            [
                Transaction::TABLE . '.' . Transaction::COLUMN_USER_ID => User::TABLE . '.id'
            ],
            [
                sprintf('DATE(%s.%s) AS date', Transaction::TABLE, Transaction::COLUMN_DATE),
                sprintf('%s.%s', User::TABLE, User::COLUMN_COUNTRY),
                sprintf('COUNT(DISTINCT CASE WHEN %s.%s = "%s" THEN %s END) AS total_deposits',
                    Transaction::TABLE,
                    Transaction::COLUMN_TYPE,
                    TransactionService::TYPE_DEPOSIT,
                    Transaction::COLUMN_USER_ID
                ),
                sprintf('SUM(CASE WHEN %s.%s = "%s" THEN %s END) AS total_deposits_amount',
                    Transaction::TABLE,
                    Transaction::COLUMN_TYPE,
                    TransactionService::TYPE_DEPOSIT,
                    Transaction::COLUMN_AMOUNT
                ),
                sprintf('COUNT(DISTINCT CASE WHEN %s.%s = "%s" THEN %s END) AS total_withdrawals',
                    Transaction::TABLE,
                    Transaction::COLUMN_TYPE,
                    TransactionService::TYPE_WITHDRAWAL,
                    Transaction::COLUMN_USER_ID
                ),
                sprintf('SUM(CASE WHEN %s.%s = "%s" THEN %s END) AS total_withdrawals_amount',
                    Transaction::TABLE,
                    Transaction::COLUMN_TYPE,
                    TransactionService::TYPE_WITHDRAWAL,
                    Transaction::COLUMN_AMOUNT
                )
            ],
            [
                sprintf('%s.%s >= CURDATE() - INTERVAL 7 DAY',
                    Transaction::TABLE,
                    Transaction::COLUMN_DATE
                )
            ],
            [
                User::COLUMN_COUNTRY,
                sprintf('DATE(%s.%s)', Transaction::TABLE, Transaction::COLUMN_DATE)
            ]
        );
    }
}
