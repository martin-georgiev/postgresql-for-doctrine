<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Length;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_LineSubstring;
use PHPUnit\Framework\Attributes\Test;

final class ST_LineSubstringTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_LENGTH' => ST_Length::class,
            'ST_LINESUBSTRING' => ST_LineSubstring::class,
        ];
    }

    #[Test]
    public function returns_the_line_substring_from_an_entity_field(): void
    {
        $dql = 'SELECT ST_LENGTH(ST_LINESUBSTRING(g.geometry1, 0.0, 0.5)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1.4142135623730951, $result[0]['result']);
    }

    #[Test]
    public function returns_the_line_substring_from_a_wkt_literal(): void
    {
        $dql = "SELECT ST_LENGTH(ST_LINESUBSTRING('LINESTRING(0 0, 1 1, 2 2)', 0.0, 0.5)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3";

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1.4142135623730951, $result[0]['result']);
    }
}
