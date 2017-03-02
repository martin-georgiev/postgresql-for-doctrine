<?php

namespace MartinGeorgiev\Doctrine\DBAL\Types;

/**
 * Implementation of PostgreSql BIGINT[] data type
 * @see https://www.postgresql.org/docs/9.4/static/arrays.html
 * 
 * @since 0.1
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class BigIntArray extends IntegerArray
{
    const TYPE_NAME = 'bigint[]';
}
