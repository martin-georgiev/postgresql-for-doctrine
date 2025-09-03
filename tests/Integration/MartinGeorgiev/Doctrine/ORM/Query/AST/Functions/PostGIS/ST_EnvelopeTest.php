<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Area;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Envelope;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Equals;
use PHPUnit\Framework\Attributes\Test;

class ST_EnvelopeTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_AREA' => ST_Area::class,
            'ST_ENVELOPE' => ST_Envelope::class,
            'ST_EQUALS' => ST_Equals::class,
        ];
    }

    #[Test]
    public function returns_envelope_of_point(): void
    {
        $dql = 'SELECT ST_EQUALS(ST_ENVELOPE(g.geometry1), g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result'], 'PostGIS behavior expects envelope of a point is the point itself');
    }

    #[Test]
    public function returns_envelope_of_polygon(): void
    {
        $dql = 'SELECT ST_AREA(ST_ENVELOPE(g.geometry1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 2';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(16, $result[0]['result'], 'Envelope of POLYGON((0 0, 0 4, 4 4, 4 0, 0 0)) should have area = 16');
    }

    #[Test]
    public function returns_envelope_of_linestring(): void
    {
        $dql = 'SELECT ST_AREA(ST_ENVELOPE(g.geometry1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(4, $result[0]['result'], 'Envelope of LINESTRING(0 0, 1 1, 2 2) should be POLYGON((0 0, 0 2, 2 2, 2 0, 0 0)) with area = 4');
    }
}
