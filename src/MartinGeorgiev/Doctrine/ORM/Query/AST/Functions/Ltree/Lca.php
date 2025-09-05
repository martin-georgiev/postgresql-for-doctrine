<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;

/**
 * Implementation of PostgreSQL lca function.
 *
 * Computes longest common ancestor of paths (up to 8 arguments are supported).
 * Also supports array input: lca(ltree[]).
 *
 * @see https://www.postgresql.org/docs/current/ltree.html#LTREE-FUNCTIONS
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT LCA(e.path1, e.path2) FROM Entity e"
 * Returns ltree, longest common ancestor of paths.
 */
class Lca extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary'];
    }

    protected function getFunctionName(): string
    {
        return 'lca';
    }

    protected function getMinArgumentCount(): int
    {
        return 2;
    }

    protected function getMaxArgumentCount(): int
    {
        return 8;
    }
}
