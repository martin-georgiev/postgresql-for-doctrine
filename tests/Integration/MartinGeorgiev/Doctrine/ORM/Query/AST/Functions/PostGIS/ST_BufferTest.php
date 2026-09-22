<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Area;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Buffer;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use PHPUnit\Framework\Attributes\Test;

final class ST_BufferTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_AREA' => ST_Area::class,
            'ST_BUFFER' => ST_Buffer::class,
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
        ];
    }

    #[Test]
    public function returns_the_buffer_of_a_wkt_literal(): void
    {
        $dql = "SELECT ST_AREA(ST_BUFFER(ST_GEOMFROMTEXT('POINT(0 0)'), 1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(3.121445152258052, $result[0]['result'], 0.000000001);
    }

    #[Test]
    public function returns_the_buffer_of_an_entity_field(): void
    {
        $dql = 'SELECT ST_AREA(ST_BUFFER(g.geometry1, 1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(3.121445152258052, $result[0]['result'], 0.000000001);
    }

    #[Test]
    public function respects_the_quad_segs_argument(): void
    {
        $dql = 'SELECT ST_AREA(ST_BUFFER(g.geometry1, 1, 32)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(3.1403311569547543, $result[0]['result'], 0.000000001);
    }

    #[Test]
    public function respects_the_buffer_style_argument(): void
    {
        $dql = "SELECT ST_AREA(ST_BUFFER(g.geometry1, 1, 'quad_segs=8')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(3.121445152258052, $result[0]['result'], 0.000000001);
    }
}
