<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Type;

/**
 * Implementation of PostgreSQL VARCHAR[] data type.
 *
 * Covers the CHARACTER VARYING[] alias as well. Behaves exactly like TEXT[]:
 * all array items are preserved as PHP strings, including values PostgreSQL
 * returns unquoted because they look numeric (see GitHub issues #424 and #482).
 *
 * @see https://www.postgresql.org/docs/18/datatype-character.html
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class VarcharArray extends TextArray
{
    /**
     * @var string
     */
    protected const TYPE_NAME = Type::VARCHAR_ARRAY;
}
