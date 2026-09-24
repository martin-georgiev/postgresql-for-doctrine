<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_IsValid;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_MakeValid;
use PHPUnit\Framework\Attributes\Test;

final class ST_MakeValidTest extends SpatialOperatorTestCase
{
    /**
     * A self-intersecting "bowtie" - the shape a map-drawing UI produces when an outline crosses itself.
     *
     * @var string
     */
    private const SELF_INTERSECTING_POLYGON = 'POLYGON((0 0,2 2,0 2,2 0,0 0))';

    protected function getStringFunctions(): array
    {
        return [
            'ST_ISVALID' => ST_IsValid::class,
            'ST_MAKEVALID' => ST_MakeValid::class,
        ];
    }

    #[Test]
    public function returns_a_repaired_self_intersecting_polygon(): void
    {
        $dql = 'SELECT ST_ISVALID(ST_MAKEVALID(:geometry)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql, ['geometry' => self::SELF_INTERSECTING_POLYGON]);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_a_repaired_self_intersecting_polygon_with_the_linework_method(): void
    {
        $dql = "SELECT ST_ISVALID(ST_MAKEVALID(:geometry, 'method=linework')) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1";

        $result = $this->executeDqlQuery($dql, ['geometry' => self::SELF_INTERSECTING_POLYGON]);
        $this->assertTrue($result[0]['result']);
    }
}
