<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_TileEnvelope;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

final class ST_TileEnvelopeTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_TILEENVELOPE' => ST_TileEnvelope::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'creates tile envelope from zoom/x/y' => 'SELECT ST_TileEnvelope(10, 512, 384) AS sclr_0 FROM ContainsGeometries c0_',
            'creates tile envelope with bounds' => 'SELECT ST_TileEnvelope(10, 512, 384, c0_.geometry1) AS sclr_0 FROM ContainsGeometries c0_',
            'creates tile envelope with bounds and margin' => 'SELECT ST_TileEnvelope(10, 512, 384, c0_.geometry1, 0.1) AS sclr_0 FROM ContainsGeometries c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'creates tile envelope from zoom/x/y' => 'SELECT ST_TILEENVELOPE(10, 512, 384) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
            'creates tile envelope with bounds' => 'SELECT ST_TILEENVELOPE(10, 512, 384, g.geometry1) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
            'creates tile envelope with bounds and margin' => 'SELECT ST_TILEENVELOPE(10, 512, 384, g.geometry1, 0.1) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
        ];
    }

    #[Test]
    public function throws_exception_for_too_many_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('ST_TileEnvelope() requires between 3 and 5 arguments');

        $dql = \sprintf('SELECT ST_TILEENVELOPE(10, 512, 384, g.geometry1, 0.1, 99) FROM %s g', ContainsGeometries::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
