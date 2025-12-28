<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use Doctrine\ORM\Query\AST\Node;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Traits\BooleanValidationTrait;

/**
 * Implementation of PostGIS ST_ConcaveHull() function.
 *
 * Computes a possibly concave geometry that contains all input geometry vertices.
 * The result approaches the convex hull as the target percent increases.
 *
 * @see https://postgis.net/docs/ST_ConcaveHull.html
 * @since 4.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_CONCAVEHULL(g.geometry, 0.9) FROM Entity g"
 * @example Using it in DQL: "SELECT ST_CONCAVEHULL(g.geometry, 0.9, 'true') FROM Entity g"
 * Returns a concave hull polygon.
 */
class ST_ConcaveHull extends BaseVariadicFunction
{
    use BooleanValidationTrait;

    protected function getNodeMappingPattern(): array
    {
        return [
            'StringPrimary,ArithmeticPrimary,StringPrimary',
            'StringPrimary,ArithmeticPrimary',
        ];
    }

    protected function getFunctionName(): string
    {
        return 'ST_ConcaveHull';
    }

    protected function getMinArgumentCount(): int
    {
        return 2;
    }

    protected function getMaxArgumentCount(): int
    {
        return 3;
    }

    protected function validateArguments(Node ...$arguments): void
    {
        parent::validateArguments(...$arguments);

        if (\count($arguments) === 3) {
            $this->validateBoolean($arguments[2], $this->getFunctionName());
        }
    }
}
