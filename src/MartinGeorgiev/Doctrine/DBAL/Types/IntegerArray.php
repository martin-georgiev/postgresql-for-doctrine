<?php

namespace MartinGeorgiev\Doctrine\DBAL\Types;

/**
 * Implementation of PostgreSql INTEGER[] data type
 * @see https://www.postgresql.org/docs/9.4/static/arrays.html
 *
 * @since 0.1
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class IntegerArray extends SmallIntArray
{
    const TYPE_NAME = 'integer[]';
}
