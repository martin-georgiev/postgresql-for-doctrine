<?php

namespace MartinGeorgiev\Doctrine\DBAL\Types;

/**
 * Implementation of Postgres' bigint[] data type
 */
class BigIntArray extends IntegerArray
{
    const TYPE_NAME = 'bigint[]';
}
