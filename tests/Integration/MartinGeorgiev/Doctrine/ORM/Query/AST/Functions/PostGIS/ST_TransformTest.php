<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Length;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Transform;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_X;
use PHPUnit\Framework\Attributes\Test;

final class ST_TransformTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
            'ST_LENGTH' => ST_Length::class,
            'ST_TRANSFORM' => ST_Transform::class,
            'ST_X' => ST_X::class,
        ];
    }

    #[Test]
    public function returns_the_transformed_form_of_a_wkt_literal(): void
    {
        $dql = "SELECT ST_X(ST_TRANSFORM(ST_GEOMFROMTEXT('POINT(1 1)', 4326), 3857)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(111319.49079327357, $result[0]['result'], 0.00001);
    }

    #[Test]
    public function returns_the_transformed_form_of_an_entity_field(): void
    {
        $dql = 'SELECT ST_X(ST_TRANSFORM(g.geometry2, 3857)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(111319.49079327357, $result[0]['result'], 0.00001);
    }

    #[Test]
    public function accepts_a_proj_string_instead_of_an_srid(): void
    {
        $dql = "SELECT ST_LENGTH(ST_TRANSFORM(g.geometry1, '+proj=longlat +datum=WGS84')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 10";

        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(0.01796630564558687, $result[0]['result'], 0.0000001);
    }
}
