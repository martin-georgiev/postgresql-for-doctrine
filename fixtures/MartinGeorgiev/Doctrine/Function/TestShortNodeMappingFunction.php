<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine\Function;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;

/**
 * A node mapping pattern deliberately shorter than the maximum argument count,
 * so a third argument finds no mapping to parse with.
 */
class TestShortNodeMappingFunction extends BaseVariadicFunction
{
    protected function getFunctionName(): string
    {
        return 'test_short_node_mapping';
    }

    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary,StringPrimary'];
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
