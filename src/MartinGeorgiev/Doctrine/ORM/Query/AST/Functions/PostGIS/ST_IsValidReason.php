<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;

/**
 * Implementation of PostGIS ST_IsValidReason().
 *
 * Returns the reason a geometry is invalid, together with the offending location, or "Valid Geometry" when it is well-formed.
 * The optional flags argument selects the validity rules applied.
 *
 * @see https://postgis.net/docs/ST_IsValidReason.html
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_ISVALIDREASON(g.geometry1) FROM Entity g"
 * @example Using it in DQL with flags: "SELECT ST_ISVALIDREASON(g.geometry1, 1) FROM Entity g"
 */
class ST_IsValidReason extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return [
            'StringPrimary,ArithmeticPrimary',
            'StringPrimary',
        ];
    }

    protected function getFunctionName(): string
    {
        return 'ST_IsValidReason';
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
