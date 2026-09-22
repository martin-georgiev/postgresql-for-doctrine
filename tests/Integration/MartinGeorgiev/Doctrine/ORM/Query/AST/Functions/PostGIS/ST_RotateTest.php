<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Rotate;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_X;
use PHPUnit\Framework\Attributes\Test;

final class ST_RotateTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
            'ST_ROTATE' => ST_Rotate::class,
            'ST_X' => ST_X::class,
        ];
    }

    #[Test]
    public function returns_the_rotated_form_of_a_wkt_literal(): void
    {
        $dql = "SELECT ST_X(ST_ROTATE(ST_GEOMFROMTEXT('POINT(2 0)'), 3.141592653589793)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(-2, $result[0]['result'], 0.000000001);
    }

    #[Test]
    public function returns_the_rotated_form_of_an_entity_field(): void
    {
        $dql = 'SELECT ST_X(ST_ROTATE(g.geometry2, 3.141592653589793)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(-1, $result[0]['result'], 0.000000001);
    }

    #[Test]
    public function respects_the_origin_arguments(): void
    {
        $dql = 'SELECT ST_X(ST_ROTATE(g.geometry2, 3.141592653589793, 10, 10)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(19, $result[0]['result'], 0.000000001);
    }
}
