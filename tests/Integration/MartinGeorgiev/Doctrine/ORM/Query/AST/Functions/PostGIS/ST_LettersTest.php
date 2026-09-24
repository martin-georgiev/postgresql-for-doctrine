<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Area;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Letters;
use PHPUnit\Framework\Attributes\Test;

final class ST_LettersTest extends SpatialOperatorTestCase
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
        $this->assertEquals($result[0]['area1'], $result[0]['area2']);
    }
}
