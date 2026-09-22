<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_IsValidReason;
use PHPUnit\Framework\Attributes\Test;

final class ST_IsValidReasonTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
            'ST_ISVALIDREASON' => ST_IsValidReason::class,
        ];
    }

    #[Test]
    public function returns_the_validity_reason_of_a_wkt_literal(): void
    {
        $dql = "SELECT ST_ISVALIDREASON(ST_GEOMFROMTEXT('POLYGON((0 0,2 2,2 0,0 2,0 0))')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('Self-intersection[1 1]', $result[0]['result']);
    }

    #[Test]
    public function returns_the_validity_reason_of_an_entity_field(): void
    {
        $dql = 'SELECT ST_ISVALIDREASON(g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('Valid Geometry', $result[0]['result']);
    }

    #[Test]
    public function respects_the_esri_flag(): void
    {
        $dql = "SELECT ST_ISVALIDREASON(ST_GEOMFROMTEXT('POLYGON((0 0,2 2,2 0,0 2,0 0))'), 1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('Self-intersection', $result[0]['result']);
    }
}
