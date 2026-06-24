<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_IsEmpty;
use PHPUnit\Framework\Attributes\Test;

final class ST_IsEmptyTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_ISEMPTY' => ST_IsEmpty::class,
        ];
    }

    #[Test]
    public function returns_false_for_non_empty_point(): void
    {
        $dql = 'SELECT ST_ISEMPTY(g.geometry1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }
}
