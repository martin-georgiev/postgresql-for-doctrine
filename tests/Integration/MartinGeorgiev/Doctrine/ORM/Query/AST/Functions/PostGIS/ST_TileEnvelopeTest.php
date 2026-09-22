<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeometryType;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_SRID;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_TileEnvelope;
use PHPUnit\Framework\Attributes\Test;

final class ST_TileEnvelopeTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_GEOMETRYTYPE' => ST_GeometryType::class,
            'ST_SRID' => ST_SRID::class,
            'ST_TILEENVELOPE' => ST_TileEnvelope::class,
        ];
    }

    #[Test]
    public function returns_a_tile_envelope_from_literal_tile_indexes(): void
    {
        $dql = 'SELECT ST_GEOMETRYTYPE(ST_TILEENVELOPE(10, 512, 384)) as result,
                       ST_SRID(ST_TILEENVELOPE(10, 512, 384)) as srid
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals('ST_Polygon', $result[0]['result']);
        $this->assertEquals(3857, $result[0]['srid']);
    }
}
