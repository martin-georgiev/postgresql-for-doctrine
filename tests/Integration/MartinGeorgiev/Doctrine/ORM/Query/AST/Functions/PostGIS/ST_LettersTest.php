<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Area;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Letters;
use PHPUnit\Framework\Attributes\Test;

class ST_LettersTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_LETTERS' => ST_Letters::class,
            'ST_AREA' => ST_Area::class,
        ];
    }

    #[Test]
    public function creates_geometry_with_consistent_area_for_same_letter(): void
    {
        $dql = "SELECT ST_AREA(ST_LETTERS('A')) as area1, ST_AREA(ST_LETTERS('A')) as area2
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals($result[0]['area1'], $result[0]['area2'], 'ST_Letters should produce consistent geometry for same input');
    }

    #[Test]
    public function longer_text_produces_larger_geometry(): void
    {
        $dql = "SELECT ST_AREA(ST_LETTERS('A')) as area_a, ST_AREA(ST_LETTERS('ABC')) as area_abc
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertLessThan($result[0]['area_abc'], $result[0]['area_a'], 'ABC should have larger area than A');
    }
}
