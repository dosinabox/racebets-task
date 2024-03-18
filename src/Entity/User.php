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

    const array ALLOWED_COLUMNS = [
        self::COLUMN_EMAIL,
        self::COLUMN_FIRSTNAME,
        self::COLUMN_LASTNAME,
        self::COLUMN_GENDER,
        self::COLUMN_COUNTRY,
        self::COLUMN_MONEY_REAL,
        self::COLUMN_MONEY_BONUS,
        self::COLUMN_BONUS
    ];
}
