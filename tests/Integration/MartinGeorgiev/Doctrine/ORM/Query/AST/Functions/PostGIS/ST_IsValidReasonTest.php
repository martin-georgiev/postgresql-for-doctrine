<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_IsValidReason;
use PHPUnit\Framework\Attributes\Test;

final class ST_IsValidReasonTest extends SpatialOperatorTestCase
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
            'ST_ISVALIDREASON' => ST_IsValidReason::class,
        ];
    }

    #[Test]
    public function returns_the_validity_reason_from_a_bound_parameter(): void
    {
        $dql = 'SELECT ST_ISVALIDREASON(:geometry) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql, ['geometry' => self::SELF_INTERSECTING_POLYGON]);
        $this->assertSame('Self-intersection[1 1]', $result[0]['result']);
    }

    #[Test]
    public function returns_the_validity_reason_with_flags(): void
    {
        $dql = 'SELECT ST_ISVALIDREASON(:geometry, 1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql, ['geometry' => self::SELF_INTERSECTING_POLYGON]);
        $this->assertSame('Self-intersection', $result[0]['result']);
    }

    #[Test]
    public function returns_the_validity_reason_from_an_entity_field(): void
    {
        $dql = 'SELECT ST_ISVALIDREASON(g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('Valid Geometry', $result[0]['result']);
    }
}
