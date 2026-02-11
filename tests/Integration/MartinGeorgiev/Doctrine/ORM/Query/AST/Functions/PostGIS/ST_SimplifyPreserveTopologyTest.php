<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Equals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Length;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_SimplifyPreserveTopology;
use PHPUnit\Framework\Attributes\Test;

class ST_SimplifyPreserveTopologyTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_EQUALS' => ST_Equals::class,
            'ST_LENGTH' => ST_Length::class,
            'ST_SIMPLIFYPRESERVETOPOLOGY' => ST_SimplifyPreserveTopology::class,
        ];
    }

    #[Test]
    public function returns_simplified_linestring(): void
    {
        $dql = 'SELECT ST_LENGTH(ST_SIMPLIFYPRESERVETOPOLOGY(g.geometry1, 0.1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(2.8284271247461903, $result[0]['result'], 0.0000000000000001);
    }

    #[Test]
    public function returns_original_point(): void
    {
        $dql = 'SELECT ST_EQUALS(ST_SIMPLIFYPRESERVETOPOLOGY(g.geometry1, 0.1), g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_simplified_linestring_with_parameter(): void
    {
        $dql = 'SELECT ST_LENGTH(ST_SIMPLIFYPRESERVETOPOLOGY(g.geometry1, :tolerance)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql, ['tolerance' => 0.1]);
        $this->assertEqualsWithDelta(2.8284271247461903, $result[0]['result'], 0.0000000000000001);
    }

    #[Test]
    public function returns_simplified_linestring_with_function_expression(): void
    {
        $dql = 'SELECT ST_LENGTH(ST_SIMPLIFYPRESERVETOPOLOGY(g.geometry1, ABS(0.1))) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(2.8284271247461903, $result[0]['result'], 0.0000000000000001);
    }
}
