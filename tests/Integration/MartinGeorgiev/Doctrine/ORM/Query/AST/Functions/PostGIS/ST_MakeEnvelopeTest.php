<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeometryType;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_MakeEnvelope;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_SRID;
use PHPUnit\Framework\Attributes\Test;

final class ST_MakeEnvelopeTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_GEOMETRYTYPE' => ST_GeometryType::class,
            'ST_MAKEENVELOPE' => ST_MakeEnvelope::class,
            'ST_SRID' => ST_SRID::class,
        ];
    }

    #[Test]
    public function creates_polygon_envelope(): void
    {
        $dql = 'SELECT ST_GEOMETRYTYPE(ST_MAKEENVELOPE(0, 0, 1, 1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals('ST_Polygon', $result[0]['result']);
    }

    #[Test]
    public function creates_envelope_with_srid(): void
    {
        $dql = 'SELECT ST_SRID(ST_MAKEENVELOPE(0, 0, 1, 1, 4326)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(4326, $result[0]['result']);
    }
}
