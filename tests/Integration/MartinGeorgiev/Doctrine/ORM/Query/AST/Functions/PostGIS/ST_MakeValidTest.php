<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_IsValid;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_MakeValid;
use PHPUnit\Framework\Attributes\Test;

final class ST_MakeValidTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
            'ST_ISVALID' => ST_IsValid::class,
            'ST_MAKEVALID' => ST_MakeValid::class,
        ];
    }

    #[Test]
    public function returns_the_repaired_form_of_a_wkt_literal(): void
    {
        $dql = "SELECT ST_ISVALID(ST_MAKEVALID(ST_GEOMFROMTEXT('POLYGON((0 0,2 2,2 0,0 2,0 0))'))) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_the_repaired_form_of_an_entity_field(): void
    {
        $dql = 'SELECT ST_ISVALID(ST_MAKEVALID(g.geometry1)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function respects_the_method_argument(): void
    {
        $dql = "SELECT ST_ISVALID(ST_MAKEVALID(ST_GEOMFROMTEXT('POLYGON((0 0,2 2,2 0,0 2,0 0))'), 'method=linework')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
