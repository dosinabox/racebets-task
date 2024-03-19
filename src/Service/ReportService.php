<?php

namespace App\Service;

use App\Exception\UnknownReportTypeException;

class ReportService
{
    const string TYPE_TRANSACTIONS = 'transactions';

    public function getReport(?string $reportType, ?string $dateStart, ?string $dateEnd): array
    {
        return match ($reportType) {
            self::TYPE_TRANSACTIONS => $this->getTransactionsReport($dateStart, $dateEnd),
            default => throw new UnknownReportTypeException($reportType),
        };
    }

    private function getTransactionsReport(?string $dateStart, ?string $dateEnd): array
    {
        //TODO implement
        return [
            $dateStart,
            $dateEnd
        ];
    }
}
