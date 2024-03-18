<?php

namespace App\Entity;

class Transaction
{
    const string TABLE = 'transactions';

    const string COLUMN_DATE = 'date';
    const string COLUMN_TYPE = 'type';
    const string COLUMN_AMOUNT = 'amount';
    const string COLUMN_USER_ID = 'user_id';

    const array ALLOWED_COLUMNS = [
        self::COLUMN_DATE,
        self::COLUMN_TYPE,
        self::COLUMN_AMOUNT,
        self::COLUMN_USER_ID
    ];
}
