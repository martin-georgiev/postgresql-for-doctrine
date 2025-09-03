<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Area;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_ClipByBox2D;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Envelope;
use PHPUnit\Framework\Attributes\Test;

class ST_ClipByBox2DTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_AREA' => ST_Area::class,
            'ST_CLIPBYBOX2D' => ST_ClipByBox2D::class,
            'ST_ENVELOPE' => ST_Envelope::class,
        ];
    }

    #[Test]
    public function will_preserve_full_area_when_clipping_box_contains_entire_polygon(): void
    {
        $dql = 'SELECT ST_AREA(ST_CLIPBYBOX2D(g.geometry1, \'BOX(0 0, 4 4)\')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(16, $result[0]['result']);
    }

    #[Test]
    public function will_preserve_full_geometry_area_when_clipping_with_geometry_envelope(): void
    {
        $dql = 'SELECT ST_AREA(ST_CLIPBYBOX2D(g.geometry1, ST_Envelope(g.geometry1))) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(16, $result[0]['result']);
    }

    #[Test]
    public function will_preserve_full_geometry_area_when_clipping_with_parameter_placeholder(): void
    {
        $dql = 'SELECT ST_AREA(ST_CLIPBYBOX2D(g.geometry1, :box_param)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql, ['box_param' => 'BOX(0 0, 4 4)']);
        $this->assertEquals(16, $result[0]['result']);
    }
}
