<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use Doctrine\ORM\Query\AST\Node;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Traits\BooleanValidationTrait;

/**
 * Implementation of PostGIS ST_Length() function.
 *
 * Returns the 2D length of the geometry if it is a LineString or MultiLineString.
 * For areal geometries, the perimeter is returned.
 *
 * @see https://postgis.net/docs/ST_Length.html
 * @since 3.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_LENGTH(g.geometry) FROM Entity g"
 * @example Using it in DQL (geography): "SELECT ST_LENGTH(g.geography, TRUE) FROM Entity g"
 */
class ST_Length extends BaseVariadicFunction
{
    use BooleanValidationTrait;

    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary'];
    }

    protected function getFunctionName(): string
    {
        return 'ST_Length';
    }

    protected function getMinArgumentCount(): int
    {
        return 1;
    }

    protected function getMaxArgumentCount(): int
    {
        return 2;
    }

    protected function validateArguments(Node ...$arguments): void
    {
        parent::validateArguments(...$arguments);

        if (\count($arguments) === 2) {
            $this->validateBoolean($arguments[1], $this->getFunctionName());
        }
    }
}
