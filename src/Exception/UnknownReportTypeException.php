<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class UnknownReportTypeException extends BadRequestHttpException
{
    public function __construct(?string $reportType)
    {
        parent::__construct(
            message: sprintf('Unknown type of report requested: %s', $reportType),
            code: Response::HTTP_BAD_REQUEST
        );
    }
}
