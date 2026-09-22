<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_AsText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Translate;
use PHPUnit\Framework\Attributes\Test;

final class ST_TranslateTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_ASTEXT' => ST_AsText::class,
            'ST_GEOMFROMTEXT' => ST_GeomFromText::class,
            'ST_TRANSLATE' => ST_Translate::class,
        ];
    }

    #[Test]
    public function returns_the_translated_form_of_a_wkt_literal(): void
    {
        $dql = "SELECT ST_ASTEXT(ST_TRANSLATE(ST_GEOMFROMTEXT('POINT(1 2)'), 10, 10)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('POINT(11 12)', $result[0]['result']);
    }

    #[Test]
    public function returns_the_translated_form_of_an_entity_field(): void
    {
        $dql = 'SELECT ST_ASTEXT(ST_TRANSLATE(g.geometry1, 10, 10)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('POINT(10 10)', $result[0]['result']);
    }

    #[Test]
    public function respects_the_z_offset_argument(): void
    {
        $dql = 'SELECT ST_ASTEXT(ST_TRANSLATE(g.geometry1, 1, 2, 3)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 11';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('POINT Z (1 2 8)', $result[0]['result']);
    }
}
