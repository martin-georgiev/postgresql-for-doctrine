<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_MakeEnvelope;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

final class ST_MakeEnvelopeTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_MAKEENVELOPE' => ST_MakeEnvelope::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'creates envelope from bounds' => 'SELECT ST_MakeEnvelope(0, 0, 1, 1) AS sclr_0 FROM ContainsGeometries c0_',
            'creates envelope with SRID' => 'SELECT ST_MakeEnvelope(0, 0, 1, 1, 4326) AS sclr_0 FROM ContainsGeometries c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'creates envelope from bounds' => 'SELECT ST_MAKEENVELOPE(0, 0, 1, 1) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
            'creates envelope with SRID' => 'SELECT ST_MAKEENVELOPE(0, 0, 1, 1, 4326) FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries g',
        ];
    }

    #[Test]
    public function throws_exception_for_too_many_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('ST_MakeEnvelope() requires between 4 and 5 arguments');

        $dql = \sprintf('SELECT ST_MAKEENVELOPE(0, 0, 1, 1, 4326, 99) FROM %s g', ContainsGeometries::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
