<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Lead;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Over;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class LeadTest extends BaseVariadicFunctionTestCase
{
    protected function createFixture(): Lead
    {
        return new Lead('LEAD');
    }

    protected function getStringFunctions(): array
    {
        return [
            'LEAD' => Lead::class,
            'OVER' => Over::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'leads a field' => 'SELECT lead(c0_.integer1) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'leads a field within a partition' => 'SELECT lead(c0_.integer1) OVER (PARTITION BY c0_.bigint1 ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'leads a numeric literal' => 'SELECT lead(5) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'leads a string literal' => "SELECT lead('value') OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_",
            'leads a parameter' => 'SELECT lead(?) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'with an offset' => 'SELECT lead(c0_.integer1, 2) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'with a field offset' => 'SELECT lead(c0_.integer1, c0_.bigint1) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'with an arithmetic offset' => 'SELECT lead(c0_.integer1, c0_.bigint1 + 1) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'with a parameter offset' => 'SELECT lead(c0_.integer1, ?) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'with a numeric default' => 'SELECT lead(c0_.integer1, 1, 0) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'with a NULL default' => 'SELECT lead(c0_.integer1, 1, NULL) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'with a string default' => "SELECT lead(c0_.integer1, 1, 'none') OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_",
            'with a field default' => 'SELECT lead(c0_.integer1, 1, c0_.integer2) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
            'with a parameter default' => 'SELECT lead(c0_.integer1, 1, ?) OVER (ORDER BY c0_.integer2 ASC) AS sclr_0 FROM ContainsNumerics c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'leads a field' => \sprintf('SELECT OVER(LEAD(e.integer1), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'leads a field within a partition' => \sprintf('SELECT OVER(LEAD(e.integer1), PARTITION BY e.bigint1 ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'leads a numeric literal' => \sprintf('SELECT OVER(LEAD(5), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'leads a string literal' => \sprintf("SELECT OVER(LEAD('value'), ORDER BY e.integer2) FROM %s e", ContainsNumerics::class),
            'leads a parameter' => \sprintf('SELECT OVER(LEAD(:value), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'with an offset' => \sprintf('SELECT OVER(LEAD(e.integer1, 2), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'with a field offset' => \sprintf('SELECT OVER(LEAD(e.integer1, e.bigint1), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'with an arithmetic offset' => \sprintf('SELECT OVER(LEAD(e.integer1, e.bigint1 + 1), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'with a parameter offset' => \sprintf('SELECT OVER(LEAD(e.integer1, :offset), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'with a numeric default' => \sprintf('SELECT OVER(LEAD(e.integer1, 1, 0), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'with a NULL default' => \sprintf('SELECT OVER(LEAD(e.integer1, 1, NULL), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'with a string default' => \sprintf("SELECT OVER(LEAD(e.integer1, 1, 'none'), ORDER BY e.integer2) FROM %s e", ContainsNumerics::class),
            'with a field default' => \sprintf('SELECT OVER(LEAD(e.integer1, 1, e.integer2), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
            'with a parameter default' => \sprintf('SELECT OVER(LEAD(e.integer1, 1, :default), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
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
                \sprintf('SELECT OVER(LEAD(), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
                'lead() requires at least 1 argument',
            ],
            'too many arguments' => [
                \sprintf('SELECT OVER(LEAD(e.integer1, 1, 0, 2), ORDER BY e.integer2) FROM %s e', ContainsNumerics::class),
                'lead() requires between 1 and 3 arguments',
            ],
        ];
    }
}
