<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Relate;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_RelateMatch;
use PHPUnit\Framework\Attributes\Test;

final class ST_RelateMatchTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_RELATE' => ST_Relate::class,
            'ST_RELATEMATCH' => ST_RelateMatch::class,
        ];
    }

    #[Test]
    public function returns_whether_a_matrix_literal_matches_a_pattern(): void
    {
        $dql = "SELECT ST_RELATEMATCH('FF0FFF0F2', 'FF*FF****') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_whether_an_entity_field_matrix_matches_a_pattern(): void
    {
        $dql = "SELECT ST_RELATEMATCH(ST_RELATE(g.geometry1, g.geometry2), 'FF0FFF0F2') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
