<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL TO_TSVECTOR().
 *
 * @see https://www.postgresql.org/docs/9.4/static/textsearch-controls.html
 * @since 0.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class ToTsvector extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return ['StringExpression'];
    }

    protected function getFunctionName(): string
    {
        return 'to_tsvector';
    }

    protected function getMinArgumentCount(): int
    {
        return 1;
    }

    protected function getMaxArgumentCount(): int
    {
        return 2;
    }
}
