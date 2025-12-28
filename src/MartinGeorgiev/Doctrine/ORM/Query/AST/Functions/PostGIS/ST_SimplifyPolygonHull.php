<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use Doctrine\ORM\Query\AST\Node;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Traits\BooleanValidationTrait;

/**
 * Implementation of PostGIS ST_SimplifyPolygonHull() function.
 *
 * Computes a simplified topology-preserving outer or inner hull of a polygon.
 * The result is a valid polygon that contains (outer) or is contained by (inner) the input.
 *
 * @see https://postgis.net/docs/ST_SimplifyPolygonHull.html
 * @since 4.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_SIMPLIFYPOLYGONHULL(g.geometry, 0.5) FROM Entity g"
 * @example Using it in DQL: "SELECT ST_SIMPLIFYPOLYGONHULL(g.geometry, 0.5, 'false') FROM Entity g"
 * Returns a simplified polygon hull.
 */
class ST_SimplifyPolygonHull extends BaseVariadicFunction
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
        return 'ST_SimplifyPolygonHull';
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
