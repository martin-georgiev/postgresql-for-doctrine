<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Relate;
use PHPUnit\Framework\Attributes\Test;

class ST_RelateTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_RELATE' => ST_Relate::class,
        ];
    }

    #[Test]
    public function returns_intersection_matrix_when_two_arguments(): void
    {
        $dql = 'SELECT ST_RELATE(g.geometry1, g.geometry2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertEquals('FF0FFF0F2', $result[0]['result']);
    }

    #[Test]
    public function returns_true_when_three_arguments_and_pattern_matches(): void
    {
        $dql = 'SELECT ST_RELATE(g.geometry1, g.geometry2, \'FF0FFF0F2\') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_false_when_three_arguments_and_pattern_does_not_match(): void
    {
        $dql = 'SELECT ST_RELATE(g.geometry1, g.geometry2, \'T*T***T**\') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }
}
