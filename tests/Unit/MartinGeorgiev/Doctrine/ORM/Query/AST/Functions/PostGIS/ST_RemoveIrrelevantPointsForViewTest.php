<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidBooleanException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_RemoveIrrelevantPointsForView;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class ST_RemoveIrrelevantPointsForViewTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ST_REMOVEIRRELEVANTPOINTSFORVIEW' => ST_RemoveIrrelevantPointsForView::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'with two arguments' => "SELECT ST_RemoveIrrelevantPointsForView(c0_.geometry1, 'BOX(-10 -10, 10 10)') AS sclr_0 FROM ContainsGeometries c0_",
            'with three arguments (cartesian_hint)' => "SELECT ST_RemoveIrrelevantPointsForView(c0_.geometry1, 'BOX(-10 -10, 10 10)', 'true') AS sclr_0 FROM ContainsGeometries c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'with two arguments' => \sprintf("SELECT ST_REMOVEIRRELEVANTPOINTSFORVIEW(g.geometry1, 'BOX(-10 -10, 10 10)') FROM %s g", ContainsGeometries::class),
            'with three arguments (cartesian_hint)' => \sprintf("SELECT ST_REMOVEIRRELEVANTPOINTSFORVIEW(g.geometry1, 'BOX(-10 -10, 10 10)', 'true') FROM %s g", ContainsGeometries::class),
        ];
    }

    #[Test]
    public function throws_exception_for_invalid_cartesian_hint_parameter(): void
    {
        $this->expectException(InvalidBooleanException::class);
        $this->expectExceptionMessage('Invalid boolean value "invalid" provided for ST_RemoveIrrelevantPointsForView. Must be "true" or "false".');

        $dql = \sprintf("SELECT ST_REMOVEIRRELEVANTPOINTSFORVIEW(g.geometry1, 'BOX(-10 -10, 10 10)', 'invalid') FROM %s g", ContainsGeometries::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    #[Test]
    public function throws_exception_for_non_constant_boolean_parameter(): void
    {
        $this->expectException(InvalidBooleanException::class);
        $this->expectExceptionMessage('The boolean parameter for ST_RemoveIrrelevantPointsForView must be a string literal');

        $dql = \sprintf("SELECT ST_REMOVEIRRELEVANTPOINTSFORVIEW(g.geometry1, 'BOX(-10 -10, 10 10)', g.geometry1) FROM %s g", ContainsGeometries::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
