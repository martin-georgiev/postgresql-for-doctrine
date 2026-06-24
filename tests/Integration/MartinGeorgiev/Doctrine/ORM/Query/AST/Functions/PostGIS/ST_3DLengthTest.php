<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_3DLength;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Length;
use PHPUnit\Framework\Attributes\Test;

final class ST_3DLengthTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_3DLENGTH' => ST_3DLength::class,
            'ST_LENGTH' => ST_Length::class,
        ];
    }

    #[Test]
    public function returns_length_for_linestring(): void
    {
        $dql = 'SELECT ST_3DLENGTH(g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 10';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(2000, $result[0]['result']);
    }

    #[Test]
    public function returns_3d_length_greater_than_or_equal_to_2d_length(): void
    {
        $dql = 'SELECT ST_3DLENGTH(g.geometry1) as length_3d, ST_LENGTH(g.geometry1) as length_2d
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertGreaterThanOrEqual($result[0]['length_2d'], $result[0]['length_3d']);
    }
}
