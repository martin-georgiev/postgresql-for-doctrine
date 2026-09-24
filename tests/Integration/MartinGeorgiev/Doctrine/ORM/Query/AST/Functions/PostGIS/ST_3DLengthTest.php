<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_3DLength;
use PHPUnit\Framework\Attributes\Test;

final class ST_3DLengthTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_3DLENGTH' => ST_3DLength::class,
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
}
