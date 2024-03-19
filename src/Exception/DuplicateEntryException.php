<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class DuplicateEntryException extends BadRequestHttpException
{
    public function __construct(string $tableName)
    {
        parent::__construct(
            message: sprintf('Unable to add an entry to %s table: duplicate entry detected.', $tableName),
            code: Response::HTTP_BAD_REQUEST
        );
    }
}
