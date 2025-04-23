<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsIntegers;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Greatest;

class GreatestTest extends BaseVariadicFunctionTestCase
{
    protected function createFixture(): BaseVariadicFunction
    {
        return new Greatest('GREATEST');
    }

    protected function getStringFunctions(): array
    {
        return [
            'GREATEST' => Greatest::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'multiple column values' => 'SELECT greatest(c0_.integer1, c0_.integer2, c0_.integer3) AS sclr_0 FROM ContainsIntegers c0_',
            'column value with expression' => 'SELECT greatest(c0_.integer1 * 100, SQRT(11) * 150) AS sclr_0 FROM ContainsIntegers c0_',
            'multiple expressions' => 'SELECT greatest(SQRT(30) * 100, SQRT(11) * 150) AS sclr_0 FROM ContainsIntegers c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'multiple column values' => \sprintf('SELECT GREATEST(e.integer1, e.integer2, e.integer3) FROM %s e', ContainsIntegers::class),
            'column value with expression' => \sprintf('SELECT GREATEST(e.integer1 * 100, sqrt(11) * 150) FROM %s e', ContainsIntegers::class),
            'multiple expressions' => \sprintf('SELECT GREATEST(sqrt(30) * 100, sqrt(11) * 150) FROM %s e', ContainsIntegers::class),
        ];
    }

    /**
     * @test
     */
    public function throws_exception_when_single_argument_given(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);

        $dql = \sprintf('SELECT GREATEST(e.integer1) FROM %s e', ContainsIntegers::class);
        $this->assertSqlFromDql('', $dql);
    }
}
