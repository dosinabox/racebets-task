<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class InsufficientBalanceException extends BadRequestHttpException
{
    public function __construct(string $userID)
    {
        parent::__construct(
            message: sprintf('Insufficient balance to complete this operation for user %s.', $userID),
            code: Response::HTTP_BAD_REQUEST
        );
    }
}
