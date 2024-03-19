<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

final class InsufficientBalanceException extends AccessDeniedHttpException
{
    public function __construct(string $userID)
    {
        parent::__construct(
            message: sprintf('Insufficient balance to complete this operation for user %s.', $userID),
            code: Response::HTTP_FORBIDDEN
        );
    }
}
