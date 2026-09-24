<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Distance;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Project;
use PHPUnit\Framework\Attributes\Test;

final class ST_ProjectTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_DISTANCE' => ST_Distance::class,
            'ST_PROJECT' => ST_Project::class,
        ];
    }

    #[Test]
    public function returns_the_projected_point_from_an_entity_field(): void
    {
        $dql = 'SELECT ST_DISTANCE(g.geometry1, ST_PROJECT(ST_PROJECT(g.geometry1, 1000, 0.785398), 1000, 0.785398 + 3.14159)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(0, $result[0]['result'], 0.01);
    }
}
