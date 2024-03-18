<?php

namespace App\Entity;

class Transaction
{
    const string TABLE = 'transactions';

    const string COLUMN_DATE = 'date';
    const string COLUMN_TYPE = 'type';
    const string COLUMN_AMOUNT = 'amount';
    const string COLUMN_USER_ID = 'user_id';

    const array SCHEMA = [
        self::COLUMN_DATE => 'DATETIME DEFAULT CURRENT_TIMESTAMP',
        self::COLUMN_TYPE => 'ENUM("deposit", "withdrawal") NOT NULL',
        self::COLUMN_AMOUNT => 'DECIMAL(10, 2) NOT NULL',
        self::COLUMN_USER_ID => 'INT'
    ];
}
