<?php

namespace App\Entity;

class User
{
    const string TABLE = 'users';

    const string COLUMN_EMAIL = 'email';
    const string COLUMN_FIRSTNAME = 'firstName';
    const string COLUMN_LASTNAME = 'lastName';
    const string COLUMN_GENDER = 'gender';
    const string COLUMN_COUNTRY = 'country';
    const string COLUMN_MONEY_REAL = 'money_real';
    const string COLUMN_MONEY_BONUS = 'money_bonus';
    const string COLUMN_BONUS = 'bonus';

    const array SCHEMA = [
        self::COLUMN_EMAIL => 'VARCHAR(255)',
        self::COLUMN_FIRSTNAME => 'VARCHAR(255)',
        self::COLUMN_LASTNAME => 'VARCHAR(255)',
        self::COLUMN_GENDER => 'VARCHAR(50)',
        self::COLUMN_COUNTRY => 'VARCHAR(50)',
        self::COLUMN_BONUS => 'DECIMAL(10, 2) DEFAULT 0',
        self::COLUMN_MONEY_REAL => 'DECIMAL(10, 2) DEFAULT 0',
        self::COLUMN_MONEY_BONUS => 'DECIMAL(10, 2) DEFAULT 0'
    ];
}
