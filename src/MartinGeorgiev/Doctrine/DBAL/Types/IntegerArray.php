<?php

namespace MartinGeorgiev\Doctrine\DBAL\Types;

/**
 * Implementation of Postgres' integer[] data type
 */
class IntegerArray extends SmallIntArray
{
    const TYPE_NAME = 'integer[]';
}
