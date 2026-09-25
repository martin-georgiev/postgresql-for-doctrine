<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ntile;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Over;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class NtileTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'NTILE' => Ntile::class,
            'OVER' => Over::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'without a window specification' => 'SELECT ntile(4) OVER () AS sclr_0 FROM ContainsNumerics c0_',
            'with PARTITION BY' => 'SELECT ntile(4) OVER (PARTITION BY c0_.integer1) AS sclr_0 FROM ContainsNumerics c0_',
            'with ORDER BY' => 'SELECT ntile(4) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'with PARTITION BY and ORDER BY' => 'SELECT ntile(4) OVER (PARTITION BY c0_.integer1 ORDER BY c0_.integer2 DESC) AS sclr_0 FROM ContainsNumerics c0_',
            'with a field as the bucket count' => 'SELECT ntile(c0_.integer1) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'with a parameter as the bucket count' => 'SELECT ntile(?) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'without a window specification' => \sprintf('SELECT OVER(NTILE(4)) FROM %s e', ContainsNumerics::class),
            'with PARTITION BY' => \sprintf('SELECT OVER(NTILE(4), PARTITION BY e.integer1) FROM %s e', ContainsNumerics::class),
            'with ORDER BY' => \sprintf('SELECT OVER(NTILE(4), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'with PARTITION BY and ORDER BY' => \sprintf('SELECT OVER(NTILE(4), PARTITION BY e.integer1 ORDER BY e.integer2 DESC) FROM %s e', ContainsNumerics::class),
            'with a field as the bucket count' => \sprintf('SELECT OVER(NTILE(e.integer1), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'with a parameter as the bucket count' => \sprintf('SELECT OVER(NTILE(:buckets), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
        ];
    }

    #[DataProvider('provideInvalidArgumentCountCases')]
    #[Test]
    public function throws_exception_for_invalid_argument_count(string $dql, string $expectedMessage): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage($expectedMessage);

        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function provideInvalidArgumentCountCases(): array
    {
        return [
            'too few arguments' => [
                \sprintf('SELECT OVER(NTILE(), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
                'ntile() requires exactly 1 argument',
            ],
            'too many arguments' => [
                \sprintf('SELECT OVER(NTILE(4, 2), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
                'ntile() requires exactly 1 argument',
            ],
        ];
    }
}
