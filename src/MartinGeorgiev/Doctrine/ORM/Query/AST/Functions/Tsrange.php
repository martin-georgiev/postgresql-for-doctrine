<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL TSRANGE().
 *
 * @see https://www.postgresql.org/docs/17/rangetypes.html
 * @since 2.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class Tsrange extends BaseRangeFunction
{
    protected function getFunctionName(): string
    {
        return 'tsrange';
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
