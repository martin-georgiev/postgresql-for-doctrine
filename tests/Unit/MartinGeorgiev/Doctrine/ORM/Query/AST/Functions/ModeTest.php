<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\QueryException;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Mode;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class ModeTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'MODE' => Mode::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'orders ascending by default' => 'SELECT mode() WITHIN GROUP (ORDER BY c0_.integer1 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'orders descending' => 'SELECT mode() WITHIN GROUP (ORDER BY c0_.integer1 DESC) AS sclr_0 FROM ContainsNumerics c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'orders ascending by default' => \sprintf('SELECT MODE(WITHIN GROUP ORDER BY e.integer1) FROM %s e', ContainsNumerics::class),
            'orders descending' => \sprintf('SELECT MODE(WITHIN GROUP ORDER BY e.integer1 DESC) FROM %s e', ContainsNumerics::class),
        ];
    }

    #[DataProvider('provideMalformedInputs')]
    #[Test]
    public function throws_exception_for_malformed_input(string $dqlFunctionCall): void
    {
        $this->expectException(QueryException::class);

        $dql = \sprintf('SELECT %s FROM %s e', $dqlFunctionCall, ContainsNumerics::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideMalformedInputs(): array
    {
        return [
            'direct argument' => ['MODE(e.integer1 WITHIN GROUP ORDER BY e.integer1)'],
            'missing WITHIN GROUP' => ['MODE(ORDER BY e.integer1)'],
            'empty argument list' => ['MODE()'],
        ];
    }
}
