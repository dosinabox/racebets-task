<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class NotFoundException extends NotFoundHttpException
{
    public function __construct(string $id, string $tableName)
    {
        parent::__construct(
            message: sprintf('Object with ID %s not found in %s table.', $id, $tableName),
            code: Response::HTTP_NOT_FOUND
        );
    }
}
