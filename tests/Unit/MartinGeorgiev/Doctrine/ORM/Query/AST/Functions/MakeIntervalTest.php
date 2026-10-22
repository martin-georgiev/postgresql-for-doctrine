<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\MakeInterval;
use PHPUnit\Framework\Attributes\Test;

final class MakeIntervalTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'MAKE_INTERVAL' => MakeInterval::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'creates an empty interval' => 'SELECT make_interval() AS sclr_0 FROM ContainsNumerics c0_',
            'creates an interval of years' => 'SELECT make_interval(c0_.integer1) AS sclr_0 FROM ContainsNumerics c0_',
            'creates an interval from every part' => 'SELECT make_interval(1, 2, 3, 4, 5, 6, 7.5) AS sclr_0 FROM ContainsNumerics c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'creates an empty interval' => \sprintf('SELECT MAKE_INTERVAL() FROM %s e', ContainsNumerics::class),
            'creates an interval of years' => \sprintf('SELECT MAKE_INTERVAL(e.integer1) FROM %s e', ContainsNumerics::class),
            'creates an interval from every part' => \sprintf('SELECT MAKE_INTERVAL(1, 2, 3, 4, 5, 6, 7.5) FROM %s e', ContainsNumerics::class),
        ];
    }

    #[Test]
    public function throws_exception_for_too_many_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);

        $dql = \sprintf('SELECT MAKE_INTERVAL(1, 2, 3, 4, 5, 6, 7, 8) FROM %s e', ContainsNumerics::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
