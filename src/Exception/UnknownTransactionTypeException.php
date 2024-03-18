<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class UnknownTransactionTypeException extends BadRequestHttpException
{
    public function __construct(string $transactionType)
    {
        parent::__construct(
            message: sprintf('Unknown type of transaction: %s', $transactionType),
            code: Response::HTTP_BAD_REQUEST
        );
    }
}
