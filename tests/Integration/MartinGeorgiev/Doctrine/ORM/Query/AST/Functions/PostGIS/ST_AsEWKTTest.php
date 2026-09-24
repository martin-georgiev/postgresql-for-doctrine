<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_AsEWKT;
use PHPUnit\Framework\Attributes\Test;

final class ST_AsEWKTTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_ASEWKT' => ST_AsEWKT::class,
        ];
    }

    #[Test]
    public function returns_ewkt_for_geography(): void
    {
        $dql = 'SELECT ST_ASEWKT(g.geography1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('SRID=4326;POINT(-9.1393 38.7223)', $result[0]['result']);
    }

    #[Test]
    public function returns_ewkt_with_max_decimal_digits(): void
    {
        $dql = 'SELECT ST_ASEWKT(g.geography1, 2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('SRID=4326;POINT(-9.14 38.72)', $result[0]['result']);
    }
}
