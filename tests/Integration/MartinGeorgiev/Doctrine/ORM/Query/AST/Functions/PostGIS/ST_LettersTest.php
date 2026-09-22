<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeometryType;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Letters;
use PHPUnit\Framework\Attributes\Test;

final class ST_LettersTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_GEOMETRYTYPE' => ST_GeometryType::class,
            'ST_LETTERS' => ST_Letters::class,
        ];
    }

    #[Test]
    public function returns_a_geometry_from_a_text_literal(): void
    {
        $dql = "SELECT ST_GEOMETRYTYPE(ST_LETTERS('A')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals('ST_MultiPolygon', $result[0]['result']);
    }
}
