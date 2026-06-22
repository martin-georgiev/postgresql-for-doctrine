<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostGIS ST_TileEnvelope().
 *
 * Returns the bounding box polygon for a Web Mercator tile specified by zoom/x/y.
 *
 * @see https://postgis.net/docs/ST_TileEnvelope.html
 * @since 4.7
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ST_TILEENVELOPE(10, 512, 384) FROM Entity g"
 */
class ST_TileEnvelope extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('ST_TileEnvelope(%s, %s, %s)');
        $this->addNodeMapping('ArithmeticPrimary');
        $this->addNodeMapping('ArithmeticPrimary');
        $this->addNodeMapping('ArithmeticPrimary');
    }
}
