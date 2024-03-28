<?php

namespace App\Service;

use App\Entity\Transaction;
use App\Entity\User;
use App\Exception\UnknownReportTypeException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
        if ($dateStart > $dateEnd) {
            throw new BadRequestHttpException('Start date cannot be higher that end date!',
                code: Response::HTTP_BAD_REQUEST
            );
        }

        if (is_null($dateStart) || is_null($dateEnd)) {
            $dateStart = 'CURDATE() - INTERVAL 7 DAY';
            $dateEnd = 'CURDATE()';
        }

        //TODO add unique customers column
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
                sprintf('DATE(%s.%s) BETWEEN "%s" AND "%s"',
                    Transaction::TABLE,
                    Transaction::COLUMN_DATE,
                    $dateStart,
                    $dateEnd
                )
            ],
            [
                User::COLUMN_COUNTRY,
                sprintf('DATE(%s.%s)', Transaction::TABLE, Transaction::COLUMN_DATE)
            ]
        );
    }
}
