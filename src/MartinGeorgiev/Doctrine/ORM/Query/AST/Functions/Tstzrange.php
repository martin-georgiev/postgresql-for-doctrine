<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL TSTZRANGE().
 *
 * @see https://www.postgresql.org/docs/17/rangetypes.html
 * @since 2.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class Tstzrange extends BaseRangeFunction
{
    protected function getFunctionName(): string
    {
        return 'tstzrange';
    }

    protected function getNodeMappingPattern(): array
    {
        return [
            'StringPrimary',
            'StringPrimary',
            'StringPrimary',
        ];
    }
}
