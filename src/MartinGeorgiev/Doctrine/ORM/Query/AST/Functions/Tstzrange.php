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
class Tstzrange extends BaseVariadicFunction
{
    protected function getFunctionName(): string
    {
        return 'tstzrange';
    }

    /**
     * @return array<string>
     */
    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary'];
    }

    protected function getMinArgumentCount(): int
    {
        return 2;
    }

    protected function getMaxArgumentCount(): int
    {
        return 3;
    }
}
