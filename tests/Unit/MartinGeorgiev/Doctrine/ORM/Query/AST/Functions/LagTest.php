<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Lag;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Over;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class LagTest extends BaseVariadicFunctionTestCase
{
    protected function createFixture(): Lag
    {
        return new Lag('LAG');
    }

    protected function getStringFunctions(): array
    {
        return [
            'LAG' => Lag::class,
            'OVER' => Over::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'lags a field' => 'SELECT lag(c0_.integer1) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'lags a field within a partition' => 'SELECT lag(c0_.integer1) OVER (PARTITION BY c0_.bigint1 ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'lags a numeric literal' => 'SELECT lag(5) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'lags a string literal' => "SELECT lag('value') OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_",
            'lags a parameter' => 'SELECT lag(?) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'with an offset' => 'SELECT lag(c0_.integer1, 2) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'with a field offset' => 'SELECT lag(c0_.integer1, c0_.bigint1) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'with an arithmetic offset' => 'SELECT lag(c0_.integer1, c0_.bigint1 + 1) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'with a parameter offset' => 'SELECT lag(c0_.integer1, ?) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'with a numeric default' => 'SELECT lag(c0_.integer1, 1, 0) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'with a NULL default' => 'SELECT lag(c0_.integer1, 1, NULL) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'with a string default' => "SELECT lag(c0_.integer1, 1, 'none') OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_",
            'with a field default' => 'SELECT lag(c0_.integer1, 1, c0_.integer2) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'with a parameter default' => 'SELECT lag(c0_.integer1, 1, ?) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'lags a field' => \sprintf('SELECT OVER(LAG(e.integer1), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'lags a field within a partition' => \sprintf('SELECT OVER(LAG(e.integer1), PARTITION BY e.bigint1 ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'lags a numeric literal' => \sprintf('SELECT OVER(LAG(5), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'lags a string literal' => \sprintf("SELECT OVER(LAG('value'), ORDER BY e.integer2) FROM %s e", ContainsNumerics::class),
            'lags a parameter' => \sprintf('SELECT OVER(LAG(:value), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'with an offset' => \sprintf('SELECT OVER(LAG(e.integer1, 2), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'with a field offset' => \sprintf('SELECT OVER(LAG(e.integer1, e.bigint1), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'with an arithmetic offset' => \sprintf('SELECT OVER(LAG(e.integer1, e.bigint1 + 1), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'with a parameter offset' => \sprintf('SELECT OVER(LAG(e.integer1, :offset), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'with a numeric default' => \sprintf('SELECT OVER(LAG(e.integer1, 1, 0), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'with a NULL default' => \sprintf('SELECT OVER(LAG(e.integer1, 1, NULL), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'with a string default' => \sprintf("SELECT OVER(LAG(e.integer1, 1, 'none'), ORDER BY e.integer2) FROM %s e", ContainsNumerics::class),
            'with a field default' => \sprintf('SELECT OVER(LAG(e.integer1, 1, e.integer2), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'with a parameter default' => \sprintf('SELECT OVER(LAG(e.integer1, 1, :default), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
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
                \sprintf('SELECT OVER(LAG(), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
                'lag() requires at least 1 argument',
            ],
            'too many arguments' => [
                \sprintf('SELECT OVER(LAG(e.integer1, 1, 0, 2), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
                'lag() requires between 1 and 3 arguments',
            ],
        ];
    }
}
