<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Length;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_LineExtend;
use PHPUnit\Framework\Attributes\Test;

class ST_LineExtendTest extends SpatialOperatorTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_LENGTH' => ST_Length::class,
            'ST_LINEEXTEND' => ST_LineExtend::class,
        ];
    }

    #[Test]
    public function extends_line_forward_increases_length(): void
    {
        $dql = 'SELECT ST_LENGTH(ST_LINEEXTEND(g.geometry1, 0.5)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $originalLength = 2.8284271247461903;
        $expectedLength = $originalLength + 0.5;
        $this->assertEqualsWithDelta($expectedLength, $result[0]['result'], 0.000000001, 'extending line forward by 0.5 should increase length by 0.5');
    }

    #[Test]
    public function extends_line_forward_and_backward_increases_length(): void
    {
        $dql = 'SELECT ST_LENGTH(ST_LINEEXTEND(g.geometry1, 0.5, 0.5)) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g
                WHERE g.id = 3';

        $result = $this->executeDqlQuery($dql);
        $originalLength = 2.8284271247461903;
        $expectedLength = $originalLength + 1.0;
        $this->assertEqualsWithDelta($expectedLength, $result[0]['result'], 0.000000001, 'extending line forward and backward by 0.5 each should increase length by 1.0');
    }
}
