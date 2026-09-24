<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Crosses;
use PHPUnit\Framework\Attributes\Test;

final class ST_CrossesTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_CROSSES' => ST_Crosses::class,
        ];
    }

    #[Test]
    public function returns_whether_the_geometries_cross_from_entity_fields(): void
    {
        $dql = 'SELECT ST_CROSSES(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }

    #[Test]
    public function returns_whether_the_geometries_cross_from_a_literal_operand(): void
    {
        $dql = "SELECT ST_CROSSES(g.geometry1, 'SRID=4326;POINT(1 1)') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }
}
