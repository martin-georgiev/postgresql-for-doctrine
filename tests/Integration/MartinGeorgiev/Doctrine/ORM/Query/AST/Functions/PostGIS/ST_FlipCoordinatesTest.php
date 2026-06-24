<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_FlipCoordinates;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_X;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Y;
use PHPUnit\Framework\Attributes\Test;

final class ST_FlipCoordinatesTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_FLIPCOORDINATES' => ST_FlipCoordinates::class,
            'ST_X' => ST_X::class,
            'ST_Y' => ST_Y::class,
        ];
    }

    #[Test]
    public function flips_coordinates(): void
    {
        $dql = 'SELECT ST_X(g.geometry1) as original_x, ST_Y(g.geometry1) as original_y,
                       ST_X(ST_FLIPCOORDINATES(g.geometry1)) as flipped_x, ST_Y(ST_FLIPCOORDINATES(g.geometry1)) as flipped_y
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals($result[0]['original_y'], $result[0]['flipped_x']);
        $this->assertEquals($result[0]['original_x'], $result[0]['flipped_y']);
    }
}
