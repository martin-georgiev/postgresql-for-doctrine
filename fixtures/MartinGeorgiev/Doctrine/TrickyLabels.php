<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine;

/**
 * Enum labels that PostgreSQL either quotes or leaves bare in its array output,
 * covering every escaping decision the array parser has to reverse.
 */
enum TrickyLabels: string
{
    case PLAIN = 'plain';
    case WITH_SPACE = 'with space';
    case WITH_COMMA = 'with,comma';
    case WITH_DOUBLE_QUOTE = 'with"quote';
    case WITH_BACKSLASH = 'with\\backslash';
    case WITH_BRACES = '{brace}';
    case EMPTY_LABEL = '';
    case NUMERIC_LOOKING = '42';
    case BOOLEAN_LOOKING = 'true';
    case LOWERCASE_NULL = 'null';
    case UPPERCASE_NULL = 'NULL';
}
